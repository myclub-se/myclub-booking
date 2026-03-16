=== MyClub Booking ===
Contributors: myclubse
Donate link: https://www.myclub.se
Tags: groups, members, administration
Requires at least: 6.4
Tested up to: 6.9
Stable tag: 0.9.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Retrieves booking information from the MyClub member administration platform. Creates a Gutenberg block and shortcode to display bookable items.

== Description ==

This plugin is intended for associations and organizations that use the MyClub membership system and need to fetch information about bookable items on their website.

Please ensure that your server is running on PHP 7.4 or higher and your WordPress version is at least 6.4 to utilize this plugin fully.

== Components ==
There is only one component in this plugin:
* Bookable items

This component retrieves bookable items from the MyClub member administration platform and displays them on a wordpress page.

== Appearance ==
All components are minimally designed to make them easier to customize and fit your website’s design. All headers, tables, images, and similar items have their own CSS classes, allowing you to style them according to your preferences.

== Dependencies ==
The plugin has no external plugin dependencies. All requirements are bundled in the plugin itself. However we are using the following opensource library (which is included in the plugin):
* FullCalendar (v5.11.5 and v6.1.11), which can be seen [here](https://fullcalendar.io/). All source to the plugin is available [here](https://github.com/fullcalendar/fullcalendar). No data is being sent to the FullCalendar plugin website.

== Installation ==

To fetch data from MyClub, you must first install this plugin:
1. Login to your WordPress Dashboard
2. Go to Plugins -> Add New
3. Search for MyClub booking plugin
4. Install the MyClub booking plugin
5. Activate it.
6. Add your API key to the plugin settings.

You can generate an API key within MyClub under Productions and prices in MyClub. Please note that once the key is generated, you need to save it immediately and paste it into the newly installed plugin. You can also reuse the API key from the MyClub groups plugin.

Once the plugin is installed with the API key, you can begin using it. The plugin consists of various components that can be added to any page via either Gutenberg blocks or Shortcodes.

== External services ==

This plugin connects to the MyClub member administration platform (https://member.myclub.se/) to fetch data. This is required for the plugin to work.
This service is provided by MyClub AB: https://www.myclub.se/
Privacy policy: https://www.myclub.se/integritetspolicy

== Privacy ==

This plugin communicates with https://member.myclub.se/ to provide data for the plugin.

The following information is transmitted:
- Site URL

The plugin transmits name and email address of the person who is booking an item in the plugin.

== Frequently Asked Questions ==

=== Caching ===
The plugin will try to clear cache on the following cache plugins for MyClub booking:
* Breeze
* Cache Enabler
* Hummingbird performance
* Hyper Cache
* LiteSpeed Cache
* SiteGround Optimizer
* Swift Performance
* WP Fastest Cache
* WP Rocket
* WP Super Cache
* W3 Total Cache
* Redis or Memcache cache

For unsupported cache systems, please contact us to request integration.

== Changelog ==
= 0.9.0 =
* Initial release