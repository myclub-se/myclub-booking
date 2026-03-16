<?php

namespace MyClub\MyClubBooking\Services;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

use MyClub\MyClubBooking\Api\RestApi;
use WP_REST_Request;
use WP_REST_Response;

class Api
{
    public function register()
    {
        add_action('rest_api_init', [
            $this,
            'registerRoutes'
        ]);
    }

    public function registerRoutes()
    {
        register_rest_route('myclub/v1', '/options', [
            'methods' => 'GET',
            'callback' => [
                $this,
                'returnOptions'
            ],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            }
        ]);

        register_rest_route('myclub/v1', '/bookables', [
            'methods' => 'GET',
            'callback' => [
                $this,
                'returnBookables'
            ],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ]);

        register_rest_route('myclub/v1', '/bookables/(?P<id>\w+)/slots', [
            'methods' => 'GET',
            'callback' => [
                $this,
                'returnBookableSlots'
            ],
            'permission_callback' => '__return_true',
            'args' => array(
                'id' => array(
                    'validate_callback' => function ($param, $request, $key) {
                        return is_string($param);
                    }
                ),
            )
        ]);

        register_rest_route('myclub/v1', '/bookables/(?P<id>\w+)/slots/(?P<slot_id>\w+)', [
            'methods' => 'GET',
            'callback' => [
                $this,
                'returnBookableSlot'
            ],
            'permission_callback' => '__return_true',
            'args' => array(
                'id' => array(
                    'validate_callback' => function ($param, $request, $key) {
                        return is_string($param);
                    }
                ),
                'slot_id' => array(
                    'validate_callback' => function ($param, $request, $key) {
                        return is_string($param);
                    }
                ),
            )
        ]);

        register_rest_route('myclub/v1', '/bookables/sessions/bulk', [
            'methods' => 'POST',
            'callback' => [
                $this,
                'bookSlotsBulk'
            ],
            'permission_callback' => function ( WP_REST_Request $request ) {
                return wp_verify_nonce( $request->get_header( 'X-WP-Nonce' ), 'wp_rest' ) !== false;
            },
        ]);

        register_rest_route('myclub/v1', '/bookables/(?P<id>\w+)/slots/(?P<slot_id>\w+)/book', [
            'methods' => 'POST',
            'callback' => [
                $this,
                'bookSlot'
            ],
            'permission_callback' => function ( WP_REST_Request $request ) {
                return wp_verify_nonce( $request->get_header( 'X-WP-Nonce' ), 'wp_rest' ) !== false;
            },
            'args' => array(
                'id' => array(
                    'validate_callback' => function ($param, $request, $key) {
                        return is_string($param);
                    }
                ),
                'slot_id' => array(
                    'validate_callback' => function ($param, $request, $key) {
                        return is_string($param);
                    }
                ),
            )
        ]);
    }

    public function returnBookables(): WP_REST_Response
    {
        $rest_api = new RestApi();
        $myclub_bookables = $rest_api->loadBookables()->result;

        return new WP_REST_Response($myclub_bookables, 200);
    }

    public function returnBookableSlots( WP_REST_Request $request): WP_REST_Response
    {
        $bookable_id = sanitize_text_field( $request['id'] );
        $start_date = sanitize_text_field( $request['start_date'] );
        $end_date = sanitize_text_field( $request['end_date'] );

        $rest_api = new RestApi();
        $myclub_bookable_slots = $rest_api->loadBookableSlots($bookable_id, $start_date, $end_date)->result;

        return new WP_REST_Response($myclub_bookable_slots, 200);
    }

    public function returnBookableSlot( WP_REST_Request $request): WP_REST_Response
    {
        $bookable_id = sanitize_text_field( $request['id'] );
        $slot_id = sanitize_text_field( $request['slot_id'] );

        $rest_api = new RestApi();
        $myclub_bookable_slots = $rest_api->loadBookableSlot($bookable_id, $slot_id)->result;

        return new WP_REST_Response($myclub_bookable_slots, 200);
    }

    public function bookSlot( WP_REST_Request $request): WP_REST_Response
    {
        $bookable_id = sanitize_text_field( $request['id'] );
        $slot_id = sanitize_text_field( $request['slot_id'] );
        $email = sanitize_email( $request['email'] );
        $first_name = sanitize_text_field( $request['first_name'] );
        $last_name = sanitize_text_field( $request['last_name'] );
        $start_time = sanitize_text_field( $request['start_time'] );
        $end_time = sanitize_text_field( $request['end_time'] );
        if (empty($first_name) || $first_name === 'null' || $first_name === 'undefined') {
            $first_name = null;
        }
        if (empty($last_name) || $last_name === 'null' || $last_name === 'undefined') {
            $last_name = null;
        }

        $rest_api = new RestApi();
        $myclub_booked_session = $rest_api->bookSlot($bookable_id, $slot_id, $start_time, $end_time, $email, $first_name, $last_name)->result;

        return new WP_REST_Response($myclub_booked_session, 200);
    }

    public function bookSlotsBulk( WP_REST_Request $request): WP_REST_Response
    {
        $email = sanitize_email( $request['email'] );
        $first_name = sanitize_text_field( $request['first_name'] );
        $last_name = sanitize_text_field( $request['last_name'] );
        $sessions = array_map( function ( $session ) {
            return [
                'slot_id'     => sanitize_text_field( $session['slot_id'] ),
                'start_time'  => sanitize_text_field( $session['start_time'] ),
                'end_time'    => sanitize_text_field( $session['end_time'] ),
                'bookable_id' => sanitize_text_field( $session['bookable_id'] ),
            ];
        }, $request['sessions'] );
        if (empty($first_name) || $first_name === 'null' || $first_name === 'undefined') {
            $first_name = null;
        }
        if (empty($last_name) || $last_name === 'null' || $last_name === 'undefined') {
            $last_name = null;
        }

        $rest_api = new RestApi();
        $result = $rest_api->bookSlotsBulk($email, $sessions, $first_name, $last_name)->result;

        return new WP_REST_Response($result, 200);
    }

    public function returnOptions(): WP_REST_Response
    {
        return new WP_REST_Response([
            'myclub_booking_calendar_title' => esc_attr(get_option('myclub_booking_calendar_title')),
        ], 200);
    }
}