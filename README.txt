=== MyClub Booking ===
Contributors: myclubse
Donate link: https://www.myclub.se
Tags: groups, members, administration
Requires at least: 6.4
Tested up to: 6.7.1
Stable tag: 0.0.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Retrieves bookable objects from the MyClub member administration platform. Generates pages to book public items in the MyClub platform.

== Description ==

This plugin is intended for associations and organizations that use the MyClub membership system and need to fetch information about bookable items on their website.

Please ensure that your server is running on PHP 7.4 or higher and your WordPress version is at least 6.4 to utilize this plugin fully.

== Components ==

== Appearance ==

== Dependencies ==

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
= 0.0.1 =
* Initial release