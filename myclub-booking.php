<?php

/*
Plugin Name: MyClub Booking
Plugin URI: https://github.com/myclub-se/myclub-booking
Description: Retrieves booking information from the MyClub member administration platform. Creates a Gutenberg block and shortcode to display bookable items.
Version: 0.9.1
Requires at least: 6.4
Tested up to: 6.9
Requires PHP: 7.4
Author: MyClub AB
Author URI: https://www.myclub.se
Text Domain: myclub-booking
Domain Path: /languages
License: GPLv2 or later
*/

use MyClub\MyClubBooking\Activation;
use MyClub\MyClubBooking\Services;

defined( 'ABSPATH' ) or die( 'Access denied' );

if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
    exit( "This plugin requires PHP 7.4 or higher. You're still on PHP " . PHP_VERSION );
}

if ( file_exists( plugin_dir_path( __FILE__ ) . '/lib/autoload.php' ) ) {
    require_once( plugin_dir_path( __FILE__ ) . '/lib/autoload.php' );
}

define( 'MYCLUB_BOOKING_PLUGIN_VERSION', '0.9.1' );

if ( file_exists( plugin_dir_path( __FILE__ ) . '/src/Activation.php' ) ) {
    function myclub_booking_activate()
    {
        $activation = new Activation();
        $activation->activate();
    }

    // Register activation code
    register_activation_hook( __FILE__, 'myclub_booking_activate' );

    function myclub_booking_deactivate()
    {
        $activation = new Activation();
        $activation->deactivate();
    }

    // Register deactivation code
    register_deactivation_hook( __FILE__, 'myclub_booking_deactivate' );
}


if ( file_exists( plugin_dir_path( __FILE__) . '/src/Services.php' ) ) {
    // Register all plugin functionality
    Services::registerServices();
}