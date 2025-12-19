<?php

namespace MyClub\MyClubBooking;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use MyClub\MyClubBooking\Services\Admin;
use MyClub\MyClubBooking\Services\Api;
use MyClub\MyClubBooking\Services\Blocks;

class Services
{
    const SERVICES = [
        Admin::class,
        Api::class,
        Blocks::class,
    ];

    public static function register_services()
    {
        foreach ( self::SERVICES as $class ) {
            $service = new $class();
            if ( method_exists( $service, 'register' ) ) {
                $service->register();
            }
        }
    }
}