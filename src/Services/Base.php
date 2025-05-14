<?php

namespace MyClub\MyClubBooking\Services;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Base
{
    protected string $plugin_path;
    protected string $plugin_url;

    public function __construct()
    {
        $this->plugin_path = plugin_dir_path( dirname( __FILE__, 2 ) );
        $this->plugin_url = plugin_dir_url( dirname( __FILE__, 2 ) );
    }
}