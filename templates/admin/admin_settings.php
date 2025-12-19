<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    $valid_tabs = [ 'tab1', 'tab2', 'tab3'];
    $active_tab = !empty( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'tab1';
    $valid_action_tabs = [ 'tab1'];

    if ( !in_array( $active_tab, $valid_tabs ) ) {
        $active_tab = 'tab1';
    }

    function myclub_booking_allow_code_html( $translated_string ) {
        echo wp_kses( $translated_string, array( 'code' => array() ) );
    }
?>

<div class="wrap">
    <h1><?php esc_attr_e( 'MyClub Booking settings', 'myclub-booking' ) ?></h1>
    <div class="nav-tab-wrapper">
        <a href="?page=myclub-booking-settings&tab=tab1" class="nav-tab<?php echo $active_tab === 'tab1' ? ' nav-tab-active' : ''; ?>"><?php esc_attr_e( 'General settings', 'myclub-booking' ) ?></a>
        <a href="?page=myclub-booking-settings&tab=tab2" class="nav-tab<?php echo $active_tab === 'tab2' ? ' nav-tab-active' : ''; ?>"><?php esc_attr_e( 'Gutenberg blocks', 'myclub-booking' ) ?></a>
        <a href="?page=myclub-booking-settings&tab=tab3" class="nav-tab<?php echo $active_tab === 'tab3' ? ' nav-tab-active' : ''; ?>"><?php esc_attr_e( 'Shortcodes', 'myclub-booking' ) ?></a>
    </div>

    <form method="post" action="options.php" id="myclub-settings-form">
        <?php
        if( $active_tab === 'tab1' ) {
            settings_fields( 'myclub_booking_settings_tab1' );
            do_settings_sections( 'myclub_booking_settings_tab1' );
        } else if( $active_tab === 'tab2' ) {
            ?> <h2><?php esc_attr_e( 'Gutenberg blocks', 'myclub-booking') ?></h2>
            <div><?php esc_attr_e( 'Here are the Gutenberg blocks available from the MyClub booking plugin', 'myclub-booking' )?></div>
            <div><?php esc_attr_e( 'The group Gutenberg blocks require a bookable_id parameter.', 'myclub-booking' )?></div>
            <ul>
                <li><strong><?php esc_attr_e( 'Calendar', 'myclub-booking' ) ?></strong> - <?php myclub_booking_allow_code_html( __( 'The calendar block will display a booking calendar. The available attribute is <code>bookable_id</code> which is the MyClub Bookable item id for the group page.', 'myclub-booking' ) ) ?></li>
            </ul>
            <?php
        } else { ?>
            <h2><?php esc_attr_e( 'Shortcodes', 'myclub-booking' ) ?></h2>
            <div><?php esc_attr_e( 'Here are the shortcodes available from the MyClub booking plugin', 'myclub-booking' ) ?></div>
            <div><?php esc_attr_e( 'The group shortcodes require a post_id or a bookable_id parameter (the club shortcodes do not). The post_id parameter is the ID of the MyClub Booking page that the plugin creates for the Bookable ID. The group_id parameter is found on the MyClub Bookable page under the MyClub bookable information tab - the property `MyClub bookable id`', 'myclub-booking' )?></div>
            <ul>
                <li><code>[myclub-booking-calendar]</code> - <?php myclub_booking_allow_code_html( __( 'The calendar shortcode will display a booking calendar. The available attributes are <code>post_id</code> which can be set to the WordPress post id of the group page that you want to get the calendar from or <code>group_id</code> which is the MyClub group id for the group page. The default is to use the current page.', 'myclub-booking' ) ) ?></li>
            </ul>
    <?php } ?>
    <?php if ( in_array( $active_tab, $valid_action_tabs ) ) { ?>
        <div>
            <?php submit_button( esc_html__( 'Save Changes' ), 'primary', 'save', false ); ?>
        </div>
    <?php } ?>
    </form>
</div>