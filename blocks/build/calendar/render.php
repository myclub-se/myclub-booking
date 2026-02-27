<?php

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
             data-labels="<?php echo esc_attr( wp_json_encode( $labels, JSON_UNESCAPED_UNICODE ) ); ?>"
             data-locale="<?php echo esc_attr( get_locale() ); ?>"
             data-bookable-id="<?php echo esc_attr( $bookable_id ); ?>"
             data-first-day-of-week="<?php echo esc_attr( get_option( 'start_of_week', 1 ) ); ?>"
        ></div>
    </div>
    <div id="selected-slots-panel" class="myclub-selected-slots-panel">
        <span class="myclub-panel-label"><?php echo esc_html__( 'Selected', 'myclub-booking' ); ?></span>
        <div id="selected-slots-list" class="myclub-selected-slots-list"></div>
        <button id="book-selected-slots-btn" class="myclub-book-btn"></button>
    </div>
    <div id="calendar-modal" class="calendar-modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>
