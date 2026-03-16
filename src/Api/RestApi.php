<?php

namespace MyClub\MyClubBooking\Api;

if (!defined('ABSPATH')) exit; // Exit if accessed directly


use MyClub\Common\Api\BaseRestApi as CommonRestApi;

/**
 * Class RestApi
 *
 * Provides methods to interact with the MyClub backend API, including retrieving club calendar,
 * menu items, other teams, group details, news, and executing GET requests.
 */
class RestApi extends CommonRestApi
{
    protected string $apiKeyOptionName = 'myclub_booking_api_key';

    public function __construct(string $apiKey = null)
    {
        parent::__construct('MyClub Groups WordPress', MYCLUB_BOOKING_PLUGIN_VERSION, $apiKey);
    }
}
