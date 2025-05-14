<?php

namespace MyClub\MyClubBooking\Services;

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use MyClub\MyClubBooking\Api\RestApi;
use WP_Query;

class Admin extends Base
{
    public function register()
    {
        add_action( 'admin_menu', [
            $this,
            'add_admin_menu'
        ] );
        add_action( 'admin_init', [
            $this,
            'add_myclub_booking_settings'
        ] );
        add_action( 'admin_enqueue_scripts', [
            $this,
            'enqueue_admin_JS'
        ] );
//        add_action( 'admin_notices', [
//            $this,
//            'wp_cron_admin_notice'
//        ] );
//        add_action( 'manage_post_posts_columns', [
//            $this,
//            'add_group_news_column'
//        ] );
//        add_action( 'after_switch_theme', [
//            $this,
//            'update_theme_page_template'
//        ] );
//        add_action( 'wp_dashboard_setup', [
//            $this,
//            'setup_dashboard_widget'
//        ] );
        add_filter( "plugin_action_links_" . plugin_basename( $this->plugin_path . '/myclub-booking.php' ), [
            $this,
            'add_plugin_settings_link'
        ] );
    }

    public function add_admin_menu()
    {
        add_options_page(
            __( 'MyClub Booking plugin settings', 'myclub-booking' ),
            __( 'MyClub Booking', 'myclub-booking' ),
            'manage_options',
            'myclub-booking-settings',
            [
                $this,
                'admin_settings'
            ]
        );
    }

    public function add_myclub_booking_settings()
    {
        register_setting( 'myclub_booking_settings_tab1', 'myclub_booking_api_key', [
            'sanitize_callback' => [
                $this,
                'sanitize_api_key'
            ],
            'default'           => NULL
        ] );
        register_setting( 'myclub_booking_settings_tab1', 'myclub_booking_last_bookable_sync', [
            'default' => NULL
        ] );
        register_setting( 'myclub_booking_settings_tab1', 'myclub_booking_last_slots_sync', [
            'default' => NULL
        ] );

        add_settings_section( 'myclub_booking_main', __( 'MyClub Booking Main Settings', 'myclub-booking' ), function () {
        }, 'myclub_booking_settings_tab1' );
        add_settings_section( 'myclub_booking_sync', __( 'Synchronization information', 'myclub-booking' ), function () {
        }, 'myclub_booking_settings_tab1' );
        add_settings_field( 'myclub_booking_api_key', __( 'MyClub API Key', 'myclub-booking' ), [
            $this,
            'render_api_key'
        ], 'myclub_booking_settings_tab1', 'myclub_booking_main', [ 'label_for' => 'myclub_booking_api_key' ] );
        add_settings_field( 'myclub_booking_last_bookable_sync', __( 'Bookables last synchronized', 'myclub-booking' ), [
            $this,
            'render_bookable_last_sync'
        ], 'myclub_booking_settings_tab1', 'myclub_booking_sync' );
        add_settings_field( 'myclub_booking_last_slots_sync', __( 'Slots last synchronized', 'myclub-booking' ), [
            $this,
            'render_slots_last_sync'
        ], 'myclub_booking_settings_tab1', 'myclub_booking_sync' );
    }

    public function add_plugin_settings_link( array $links ): array
    {
        $settings_link = '<a href="options-general.php?page=myclub-booking-settings">' . __( 'Settings' ) . '</a>';
        $links[] = $settings_link;
        return $links;
    }

    public function admin_settings()
    {
        return require_once( "$this->plugin_path/templates/admin/admin_settings.php" );
    }

    public function enqueue_admin_JS()
    {
        $current_page = get_current_screen();

        if ( $current_page->base === 'settings_page_myclub-booking-settings' ) {
            wp_register_script( 'myclub_booking_settings_js', $this->plugin_url . 'resources/javascript/myclub_booking_settings.js', [], MYCLUB_BOOKING_PLUGIN_VERSION, true );
            wp_register_style( 'myclub_booking_settings_css', $this->plugin_url . 'resources/css/myclub_booking_settings.css', [], MYCLUB_BOOKING_PLUGIN_VERSION );
            wp_set_script_translations( 'myclub_booking_settings_js', 'myclub-booking', $this->plugin_path . 'languages' );

            wp_enqueue_script( 'jquery-ui-sortable' );
            wp_enqueue_script( 'myclub_booking_settings_js' );
            wp_enqueue_style( 'myclub_booking_settings_css' );
        }
    }

    public function render_api_key( array $args )
    {
        echo '<input type="text" id="' . esc_attr( $args[ 'label_for' ] ) . '" name="myclub_booking_api_key" value="' . esc_attr( get_option( 'myclub_booking_api_key' ) ) . '" />';
    }

    public function render_slots_last_sync()
    {
        $this->render_date_time_field( 'myclub_booking_last_slots_sync' );
    }

    public function render_bookable_last_sync()
    {
        $this->render_date_time_field( 'myclub_booking_last_bookable_sync' );
    }

    public function sanitize_api_key( string $input ): string
    {
        $input = sanitize_text_field( $input );

        $api = new RestApi( $input );
        if ( $api->load_bookables()->status !== 200 ) {
            add_settings_error( 'myclub_booking_api_key', 'invalid-api-key', __( 'Invalid API key entered', 'myclub-booking' ) );
            return get_option( 'myclub_booking_api_key' );
        } else {
            return $input;
        }
    }

    public function wp_cron_admin_notice()
    {
        if ( !wp_next_scheduled( 'wp_version_check' ) ) {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p><?php esc_html_e( 'WP Cron is not running. This is required for running the MyClub booking plugin.', 'myclub-booking' ); ?></p>
            </div>
            <?php
        }
    }

    private function render_date_time_field( string $field_name )
    {
        $last_sync = esc_attr( get_option( $field_name ) );
        $cron_job_name = '';
        $output = '';

        if ( $field_name === 'myclub_booking_last_slots_sync' ) {
            $cron_job_name = 'myclub_booking_refresh_slots_task_cron';
            $cron_job_type = __( 'slots', 'myclub-booking' );
        }

        if ( $field_name === 'myclub_booking_last_bookable_sync' ) {
            $cron_job_name = 'myclub_booking_refresh_bookables_task_cron';
            $cron_job_type = __( 'bookables', 'myclub-booking' );
        }

        if ( !empty( $cron_job_name ) && isset( $cron_job_type ) ) {
            $next_scheduled = wp_next_scheduled( $cron_job_name );
            if ( $next_scheduled ) {
                $output = sprintf( __( 'The %1$s update task is currently running.', 'myclub-booking' ), esc_attr( $cron_job_type ) );
            }
        }

        if ( empty ( $output ) ) {
            $output = empty( $last_sync ) ? __( 'Not synchronized yet', 'myclub-nooking' ) : Utils::format_date_time( $last_sync );
        }

        echo '<div id="' . $field_name . '">' . esc_attr( $output ) . '</div>';
    }
}