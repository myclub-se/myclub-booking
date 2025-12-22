<?php

namespace MyClub\MyClubBooking\Services;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

use MyClub\MyClubBooking\Api\RestApi;
use WP_Error;
use WP_Query;
use WP_REST_Request;
use WP_REST_Response;

class Api
{
    public function register()
    {
        add_action('rest_api_init', [
            $this,
            'register_routes'
        ]);
    }

    public function register_routes()
    {
        register_rest_route('myclub/v1', '/options', [
            'methods' => 'GET',
            'callback' => [
                $this,
                'return_options'
            ],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            }
        ]);

        register_rest_route('myclub/v1', '/bookables', [
            'methods' => 'GET',
            'callback' => [
                $this,
                'return_bookables'
            ],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ]);

        register_rest_route('myclub/v1', '/bookables/(?P<id>\w+)/slots', [
            'methods' => 'GET',
            'callback' => [
                $this,
                'return_bookable_slots'
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
                'return_bookable_slot'
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

        register_rest_route('myclub/v1', '/bookables/(?P<id>\w+)/slots/(?P<slot_id>\w+)/book', [
            'methods' => 'POST',
            'callback' => [
                $this,
                'book_slot'
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
    }

    public function return_bookables(): WP_REST_Response
    {
        $rest_api = new RestApi();
        $myclub_bookables = $rest_api->loadBookables()->result;

        return new WP_REST_Response($myclub_bookables, 200);
    }

    public function return_bookable_slots(WP_REST_Request $request): WP_REST_Response
    {
        $bookable_id = $request['id'];
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];

        $rest_api = new RestApi();
        $myclub_bookable_slots = $rest_api->loadBookableSlots($bookable_id, $start_date, $end_date)->result;

        return new WP_REST_Response($myclub_bookable_slots, 200);
    }

    public function return_bookable_slot(WP_REST_Request $request): WP_REST_Response
    {
        $bookable_id = $request['id'];
        $slot_id = $request['slot_id'];

        $rest_api = new RestApi();
        $myclub_bookable_slots = $rest_api->loadBookableSlot($bookable_id, $slot_id)->result;

        return new WP_REST_Response($myclub_bookable_slots, 200);
    }

    public function book_slot(WP_REST_Request $request): WP_REST_Response
    {
        $bookable_id = $request['id'];
        $slot_id = $request['slot_id'];
        $email = $request['email'];
        $first_name = $request['first_name'];
        $last_name = $request['last_name'];
        $start_time = $request['start_time'];
        $end_time = $request['end_time'];
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

    public function return_options(): WP_REST_Response
    {
        return new WP_REST_Response([
            'myclub_booking_calendar_title' => esc_attr(get_option('myclub_booking_calendar_title')),
        ], 200);
    }
}