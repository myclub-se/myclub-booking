<?php
use MyClub\MyClubBooking\Api\RestApi;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    const MYCLUB_BOOKING_VALID_ACTIONS_TABS = [
            'tab1'
    ];

    CONST MYCLUB_BOOKING_VALID_TABS = [
            'tab1',
            'tab2',
            'tab3'
    ];

    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Tab navigation only, no data is processed.
    $myclub_booking_active_tab = !empty( $_GET[ 'tab'] ) ? sanitize_text_field( wp_unslash( $_GET[ 'tab'] ) ) : 'tab1';

    if ( !in_array( $myclub_booking_active_tab, MYCLUB_BOOKING_VALID_TABS ) ) {
        $myclub_booking_active_tab = 'tab1';
    }

    function myclub_booking_allow_code_html( $translated_string ) {
        echo wp_kses( $translated_string, array( 'code' => array() ) );
    }
?>

<div class="wrap">
    <h1><?php esc_attr_e( 'MyClub Booking settings', 'myclub-booking' ) ?></h1>
    <div class="nav-tab-wrapper">
        <a href="?page=myclub-booking-settings&tab=tab1" class="nav-tab<?php echo $myclub_booking_active_tab === 'tab1' ? ' nav-tab-active' : ''; ?>"><?php esc_attr_e( 'General settings', 'myclub-booking' ) ?></a>
        <a href="?page=myclub-booking-settings&tab=tab2" class="nav-tab<?php echo $myclub_booking_active_tab === 'tab2' ? ' nav-tab-active' : ''; ?>"><?php esc_attr_e( 'Gutenberg blocks', 'myclub-booking' ) ?></a>
        <a href="?page=myclub-booking-settings&tab=tab3" class="nav-tab<?php echo $myclub_booking_active_tab === 'tab3' ? ' nav-tab-active' : ''; ?>"><?php esc_attr_e( 'Shortcodes', 'myclub-booking' ) ?></a>
    </div>

    <form method="post" action="options.php" id="myclub-settings-form">
        <?php
        if( $myclub_booking_active_tab === 'tab1' ) {
            settings_fields( 'myclub_booking_settings_tab1' );
            do_settings_sections( 'myclub_booking_settings_tab1' );
        } else if( $myclub_booking_active_tab === 'tab2' ) {
            ?> <h2><?php esc_attr_e( 'Gutenberg blocks', 'myclub-booking') ?></h2>
            <div><?php esc_attr_e( 'Here are the Gutenberg blocks available from the MyClub booking plugin', 'myclub-booking' )?></div>
            <div><?php esc_attr_e( 'The Gutenberg blocks require a bookable_id parameter.', 'myclub-booking' )?></div>
            <ul>
                <li><strong><?php esc_attr_e( 'Calendar', 'myclub-booking' ) ?></strong> - <?php myclub_booking_allow_code_html( __( 'The calendar block will display a booking calendar. The available attribute is <code>bookable_id</code> which is the MyClub Bookable item id.', 'myclub-booking' ) ) ?></li>
            </ul>
            <?php
        } else { ?>
            <h2><?php esc_attr_e( 'Shortcodes', 'myclub-booking' ) ?></h2>
            <div><?php esc_attr_e( 'Here are the shortcodes available from the MyClub booking plugin', 'myclub-booking' ) ?></div>
            <div><?php esc_attr_e( 'The shortcode requires a bookable_id parameter. The bookable_id parameter can be found in the table below - the bookable_id parameter is required.', 'myclub-booking' )?></div>
            <ul>
                <li><code>[myclub-booking-calendar]</code> - <?php myclub_booking_allow_code_html( __( 'The calendar shortcode will display a booking calendar. The available attributes are <code>bookable_id</code> which is the MyClub bookable id. This is required.', 'myclub-booking' ) ) ?></li>
            </ul>
            <?php
                $myclub_booking_objects = ( new RestApi() )->loadBookables();
                if ( !empty( $myclub_booking_objects->result ) && $myclub_booking_objects->result->count ) :?>
                    <h3><?php esc_attr_e('Available bookable objects are:', 'myclub-booking' )?></h3>
                    <div style="max-height: 480px; overflow-y: auto;">
                        <table class="widefat striped">
                            <thead>
                            <tr>
                                <th><?php esc_attr_e('ID', 'myclub-booking' )?></th>
                                <th><?php esc_attr_e('Name', 'myclub-booking' )?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ( $myclub_booking_objects->result->results as $myclub_booking_object ) : ?>
                                <tr>
                                    <td><code><?php echo esc_html( $myclub_booking_object->id ); ?></code></td>
                                    <td><?php echo esc_html( $myclub_booking_object->name ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <div class="notice notice-info inline">
                        <p><?php esc_html_e( 'No bookable objects found.', 'myclub-booking' ); ?></p>
                    </div>
                <?php endif; ?>
    <?php } ?>
    <?php if ( in_array( $myclub_booking_active_tab, MYCLUB_BOOKING_VALID_ACTIONS_TABS ) ) { ?>
        <div>
            <?php submit_button( esc_html__( 'Save Changes' ), 'primary', 'save', false ); ?>
        </div>
    <?php } ?>
    </form>
</div>