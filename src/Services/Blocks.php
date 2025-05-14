<?php

namespace MyClub\MyClubBooking\Services;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Blocks extends Base
{
    const BLOCKS = [
        'calendar',
    ];

    private array $block_args = [];
    private array $handles = [];

    public function enqueue_scripts()
    {
        foreach ($this->handles as $handle) {
            wp_set_script_translations($handle, 'myclub-booking', $this->plugin_path . 'languages');
        }
    }

    public function render_calendar(array $attributes, string $content = ''): string
    {
        wp_enqueue_script('fullcalendar-js');

        ob_start();
        require($this->plugin_path . 'blocks/build/calendar/render.php');
        return ob_get_clean();
    }

    public function register()
    {
        // Register custom MyClub blocks
        add_action('init', [
            $this,
            'register_blocks'
        ]);
        // Enqueue js scripts for translations
        add_action('admin_enqueue_scripts', [
            $this,
            'enqueue_scripts'
        ]);
        // Add custom category to blocks chooser
        add_filter('block_categories_all', [
            $this,
            'register_myclub_category'
        ]);
    }


    public function register_blocks()
    {
        $this->block_args = [
            'calendar' => [
                'description' => __('Display calendar for a selected bookable item', 'myclub-booking'),
                'render_callback' => [
                    $this,
                    'render_calendar'
                ],
                'title' => __('MyClub Booking Calendar', 'myclub-booking')
            ],
        ];

        foreach (Blocks::BLOCKS as $block) {
            $this->register_block($block);
        }

        wp_register_script('fullcalendar-js', $this->plugin_url . 'resources/javascript/fullcalendar.6.1.11.min.js', [], '6.1.11', true);
    }

    public function register_myclub_category(array $categories): array
    {
        $categories[] = array(
            'slug' => 'myclub',
            'title' => 'MyClub'
        );

        return $categories;
    }

    private function register_block(string $block)
    {
        $block_type = register_block_type($this->plugin_path . 'blocks/build/' . $block, $this->block_args[$block]);

        if (!$block_type) {
            error_log("Unable to register block $block");
        } else {
            array_push($this->handles, ...$block_type->view_script_handles, ...$block_type->editor_script_handles);
        }
    }
}