<?php

namespace MyClub\MyClubBooking\Api;

if (!defined('ABSPATH')) exit; // Exit if accessed directly


use stdClass;
use WP_Error;

/**
 * Class RestApi
 *
 * Provides methods to interact with the MyClub backend API, including retrieving club calendar,
 * menu items, other teams, group details, news, and executing GET requests.
 */
class RestApi
{
    const MYCLUB_SERVER_API_PATH = 'https://member.myclub.se/api/v3/external/';

    private string $apiKey;

    private bool $multiSite;

    private string $site;

    public function __construct(string $apiKey = null)
    {
        if (!empty($apiKey)) {
            $this->apiKey = $apiKey;
        } else {
            $this->apiKey = get_option('myclub_booking_api_key');
        }

        $this->multiSite = is_multisite();
        $this->site = get_bloginfo('url');
    }


    public function load_bookables()
    {
        $service_path = 'bookables/';

        if (empty($this->apiKey)) {
            $return_value = new stdClass();
            $return_value->result = [];
            $return_value->status = 401;
            return $return_value;
        }

        $decoded = $this->get($service_path, ['limit' => "null"]);

        if (is_wp_error($decoded)) {
            error_log('Unable to load bookable items: Error occurred in API call');
            $return_value = new stdClass();
            $return_value->result = [];
            $return_value->status = 500;
            return $return_value;
        }

        return $decoded;
    }

    public function load_bookable_slots(string $bookableId = null, string $start_date = null, string $end_date = null)
    {
        if (empty($this->apiKey)) {
            return false;
        }

        if (is_null($bookableId)) {
            return false;
        }
        $args = array(
            "limit" => "null"
        );
        if (!is_null($start_date)) {
            $args["start_date"] = $start_date;
        }
        if (!is_null($end_date)) {
            $args["end_date"] = $end_date;
        }

        $service_path = sprintf("bookables/%s/slots/", $bookableId);

        $decoded = $this->get($service_path, $args);
        if (is_wp_error($decoded) || $decoded->status !== 200) {
            error_log('Unable to load bookable slots: Error occurred in API call');
        }

        return $decoded;
    }

    public function load_bookable_slot(string $bookableId = null, string $slotId = null)
    {
        if (empty($this->apiKey)) {
            return false;
        }

        if (is_null($bookableId) || is_null($slotId)) {
            return false;
        }

        $service_path = sprintf("bookables/%s/slots/%s/", $bookableId, $slotId);

        $decoded = $this->get($service_path);
        if (is_wp_error($decoded) || $decoded->status !== 200) {
            error_log('Unable to load bookable slots: Error occurred in API call');
        }

        return $decoded;
    }

    public function book_slot(string $bookableId, string $slotId, string $startTime, string $endTime, string $email, string $firstName = null, string $lastName = null)
    {
        if (empty($this->apiKey)) {
            return false;
        }
        $args = array(
            "start_time" => $startTime,
            "end_time" => $endTime,
            "email" => $email,
            "first_name" => $firstName,
            "last_name" => $lastName,
            "bookable_zones_taken" => 1,
        );
        $service_path = sprintf("bookables/%s/slots/%s/book/", $bookableId, $slotId);
        $decoded = $this->post($service_path, $args);
        if (is_wp_error($decoded)) {
            error_log('Unable to book slot: Error occurred in API call');
        }
        return $decoded;
    }

    private function get(string $service_path, array $data = [])
    {
        if (!empty ($data)) {
            $service_path = $service_path . '?' . http_build_query($data);
        }
        $response = wp_remote_get($this->get_server_url($service_path),
            [
                'headers' => $this->create_request_headers(),
                'timeout' => 20
            ]
        );

        if (is_wp_error($response)) {
            error_log('Error occurred during API get call, additional info: ' . $response->get_error_message());
            return $response;
        } else {
            $value = new stdClass();
            $value->result = json_decode(wp_remote_retrieve_body($response));
            $value->status = $response['response']['code'];
            return $value;
        }
    }

    private function post(string $service_path, array $data = [])
    {
        $args = $this->get_post_args($data);
        $response = wp_remote_post($this->get_server_url($service_path), $args);

        if (is_wp_error($response)) {
            error_log('Error occurred during API get call, additional info: ' . $response->get_error_message());
            return $response;
        } else {
            $value = new stdClass();
            $value->result = json_decode(wp_remote_retrieve_body($response));
            $value->status = $response['response']['code'];
            return $value;
        }
    }

    protected function get_post_args(array $data = [])
    {
        return array(
            'timeout' => 5,
            'body' => json_encode($data),
            'headers' => $this->create_request_headers(),
            'sslverify' => apply_filters( 'https_local_ssl_verify', false ), // Local requests, fine to pass false.
        );
    }

    private function create_request_headers(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => "Api-Key $this->apiKey",
            'X-MyClub-Request' => 'MyClub Booking WordPress',
            'X-MyClub-MultiSite' => $this->multiSite ? 'true' : 'false',
            'X-MyClub-Site' => $this->site,
            'X-MyClub-Version' => MYCLUB_BOOKING_PLUGIN_VERSION,
        ];
    }

    private function get_server_url(string $path): string
    {
        return self::MYCLUB_SERVER_API_PATH . $path;
    }
}