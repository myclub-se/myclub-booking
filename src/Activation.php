<?php

namespace MyClub\MyClubBooking;

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Activation
{

    private array $options;

    /**
     * Initializes the class by setting default options.
     *
     * @return void
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->options = array (
            [
                'name'     => 'myclub_booking_api_key',
                'value'    => null,
                'autoload' => 'no'
            ],
            [
                'name'     => 'myclub_booking_calendar_title',
                'value'    => __( 'Booking Calendar', 'myclub-booking' ),
                'autoload' => 'no'
            ]
        );
    }

    /**
     * Activates the plugin by adding configured options.
     *
     * @return void
     * @since 1.0.0
     */
    public function activate()
    {
        foreach ( $this->options as $option ) {
            $this->addOption( $option[ 'name' ], $option[ 'value' ], $option[ 'autoload' ] );
        }
    }

    /**
     * Deactivates the functionality by removing stored configuration options.
     *
     * @return void
     * @since 1.0.0
     */
    public function deactivate()
    {
        delete_option( 'myclub_booking_api_key' );
    }

    /**
     * Uninstalls the plugin by removing all stored options.
     *
     * @return void
     * @since 1.0.0
     */
    public function uninstall()
    {
        foreach ( $this->options as $option ) {
            delete_option( $option[ 'name' ] );
        }
    }

    /**
     * Adds an option to the WordPress database if it doesn't already exist.
     *
     * @param string $optionName The name of the option.
     * @param mixed $default The default value for the option.
     * @param string|null $autoload Sets if the option should be loaded.
     *
     * @return void
     * @since 1.0.0
     */
    private function addOption( string $optionName, $default, string $autoload )
    {
        if ( get_option( $optionName ) === false ) {
            add_option( $optionName, $default, '', $autoload );
        }
    }
}