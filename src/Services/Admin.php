<?php

namespace MyClub\MyClubBooking\Services;

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use MyClub\MyClubBooking\Api\RestApi;

class Admin extends Base
{
    public function register()
    {
        add_action( 'admin_menu', [
            $this,
            'addAdminMenu'
        ] );
        add_action( 'admin_init', [
            $this,
            'addMyclubBookingSettings'
        ] );
        add_action( 'admin_enqueue_scripts', [
            $this,
            'enqueueAdminJS'
        ] );
        add_filter( "plugin_action_links_" . plugin_basename( $this->plugin_path . '/myclub-booking.php' ), [
            $this,
            'addPluginSettingsLink'
        ] );
    }

    public function addAdminMenu()
    {
        add_options_page(
            __( 'MyClub Booking plugin settings', 'myclub-booking' ),
            __( 'MyClub Booking', 'myclub-booking' ),
            'manage_options',
            'myclub-booking-settings',
            [
                $this,
                'adminSettings'
            ]
        );
    }

    public function addMyclubBookingSettings()
    {
        register_setting( 'myclub_booking_settings_tab1', 'myclub_booking_api_key', [
            'sanitize_callback' => [
                $this,
                'sanitizeApiKey'
            ],
            'default'           => NULL
        ] );

        register_setting( 'myclub_booking_settings_tab1', 'myclub_booking_calendar_title', [
            'sanitize_callback' => [
                $this,
                'sanitizeBookingCalendarTitle'
            ],
            'default'           => __( 'MyClub Booking Calendar title', 'myclub-booking' ),
        ]);

        add_settings_section( 'myclub_booking_main', __( 'MyClub Booking Main Settings', 'myclub-booking' ), function () {
        }, 'myclub_booking_settings_tab1' );

        add_settings_field( 'myclub_booking_api_key', __( 'MyClub API Key', 'myclub-booking' ), [
            $this,
            'renderApiKey'
        ], 'myclub_booking_settings_tab1', 'myclub_booking_main', [ 'label_for' => 'myclub_booking_api_key' ] );
        add_settings_field( 'myclub_booking_calendar_title', __( 'Booking Calendar title', 'myclub-booking' ), [
            $this,
            'renderBookingCalendarTitle'
        ], 'myclub_booking_settings_tab1', 'myclub_booking_main', [ 'label_for' => 'myclub_booking_calendar_title' ] );
    }

    public function addPluginSettingsLink( array $links ): array
    {
        $settings_link = '<a href="options-general.php?page=myclub-booking-settings">' . __( 'Settings', 'myclub-booking' ) . '</a>';
        $links[] = $settings_link;
        return $links;
    }

    public function adminSettings()
    {
        return require_once( "$this->plugin_path/templates/admin/admin_settings.php" );
    }

    public function enqueueAdminJS()
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

    public function renderApiKey( array $args )
    {
        echo '<input type="text" id="' . esc_attr( $args[ 'label_for' ] ) . '" name="myclub_booking_api_key" value="' . esc_attr( get_option( 'myclub_booking_api_key' ) ) . '" />';
    }

    public function renderBookingCalendarTitle( array $args )
    {
        echo '<input type="text" id="' . esc_attr( $args[ 'label_for' ] ) . '" name="myclub_booking_calendar_title" value="' . esc_attr( get_option( 'myclub_booking_calendar_title' ) ) . '" />';
    }

    public function sanitizeApiKey( string $input ): string
    {
        $input = sanitize_text_field($input);

        $api = new RestApi($input);
        if ($api->loadBookables()->status !== 200) {
            add_settings_error('myclub_booking_api_key', 'invalid-api-key', __('Invalid API key entered', 'myclub-booking'));
            return get_option('myclub_booking_api_key');
        } else {
            return $input;
        }
    }

    public function sanitizeBookingCalendarTitle( string $input ): string
    {
        return sanitize_text_field($input);
    }
}