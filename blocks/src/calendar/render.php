<?php

use MyClub\MyClubBooking\Api\RestApi;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$header = get_option( 'myclub_booking_calendar_title' );

?>
    <div class="myclub-booking-calendar">
        <div class="myclub-booking-calendar-container">
            <h3 class="myclub-booking-header"><?php echo esc_attr( $header ) ?></h3>
<?php
if ( !empty( $attributes ) ) {
    $bookable_id = $attributes['bookable_id'];
}

if ( empty ( $bookable_id ) || $bookable_id == 0 ) {
    echo esc_html__( 'No bookable page found. Invalid post_id or bookable_id.', 'myclub-booking' );
} else {
    $api = new RestApi();
    $meta = $api->load_bookable_slots( $bookable_id )->result;

    if ( !empty( $meta ) ):
        $slots = $meta->results;
        $labels = [
            'calendar'       => __( 'Calendar', 'myclub-booking' ),
            'today'          => __( 'today', 'myclub-booking' ),
            'day'            => __( 'day', 'myclub-booking' ),
            'month'          => __( 'month', 'myclub-booking' ),
            'week'           => __( 'week', 'myclub-booking' ),
            'list'           => __( 'list', 'myclub-booking' ),
            'weekText'       => __( 'W', 'myclub-booking' ),
            'weekTextLong'   => __( 'Week', 'myclub-booking' ),
        ];
        ?>

        <div id="calendar-div"
             data-events="<?php echo esc_attr( wp_json_encode( $slots, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT ) ); ?>"
             data-labels="<?php echo esc_attr( wp_json_encode( $labels, JSON_UNESCAPED_UNICODE ) ); ?>"
             data-locale="<?php echo esc_attr( get_locale() ); ?>"
             data-first-day-of-week="<?php echo esc_attr( get_option( 'start_of_week', 1 ) ); ?>"
        ></div>
    <?php
    endif;
}
?>
    </div>
    <div id="calendar-modal" class="calendar-modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>
