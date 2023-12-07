=== Multi-Emails for WooCommerce ===

Contributors: Artiosmedia, repon.wp
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=E7LS2JGFPLTH2
Tags: internal product code, product code, company product number, product id number, product id code, second sku
Requires at least: 4.6
Tested up to: 6.3.1
Version: 1.0.0
Stable tag: 1.0.0
Requires PHP: 7.4.33
License: GPLv3 or later license and included
URI: http://www.gnu.org/licenses/gpl-3.0.html

Now there is finally a reliable way to automatically assign recipients' email to any number of products or product categories so that when the order completes, the order is directly submitted to that specified email recipient according to your predefined settings.

== Description ==

This automated delegation feature is desirable for WooCommerce systems that need to communicate with multiple sponsors, manufacturers, dropship affiliates, warehouse contacts, and an endless number of other endpoints. Previously, you could add several emails to a new order from the WooCommerce Settings > Email tab, but all the products would only be delivered to the assigned recipients. You had no way to selectively choose where different product orders were delivered without developer customizations or expensive commercial plugins.

The function of this unique plugin follows the logic of WooCommerce, where it will allow the customer to receive the default notices to the emails entered as follows: Order on hold, Processing order, Completed order, Refunded order, Customer note, Reset password, and New account. The company recipient emails will in turn receive the default admin emails: New order, Cancelled order, and Failed order.

This plugin also allows an administrator to optionally choose to modify the registered user's contact profile by entering multiple email fields under their user profile as well as make them accessible from the shopping cart. This way a customer may choose to be contacted about an order from different entered emails for their convenience. The admin must create labels for each of the email fields with titles like "Email One" and "Email Two" as suggested.

Added as an expanded feature during the plugin's creation, is the ability to install the address along with the email selected as the order's point of origin. This feature overrides the entered default sales origin address in WooCommerce, which allows the shipping to calculate based on the email's physical address, resulting in a much more accurate sales total.

The plugin's language support includes English, Spanish, French, and Russian.

== Installation ==

1. Upload the plugin files to the '/wp-content/plugins/plugin-name' directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Enter the required content from the settings screen link found in the WooCommerce menu. 

== Technical Details for Release 1.0.0 ==

Load time: 0.279 s; Memory usage: 3.61 MiB
PHP up to tested version: 8.2.7
MySQL up to tested version: 8.0.34
MariaDB up to tested version: 11.2
cURL up to tested version: 8.2.1, OpenSSL/3.1.2
PHP 7.4, 8.0, 8.1, and 8.2 compliant.

== Using in Multisite Installation ==

1. Extract the zip file contents in the wp-content/mu-plugins/ directory of your WordPress installation. (This is not created by default. You must create it in the wp-content folder.) The 'mu' does not stand for multi-user like it did for WPMU, it stands for 'must-use' as any code placed in that folder will run without needing to be activated.
2. Access the Plugins settings panel named 'Multi-Emails for WooCommerce' under options.
3. Enter the required content from the settings screen link found in the WooCommerce menu.

== Frequently Asked Questions ==

= Is this plugin frequently updated to Wordpress compliance? =
Yes, attention is given on a staged installation with many other plugins via debug mode.

= Is the plugin as simple to use as it looks? =
Yes. No other plugin exists that adds an additional emails to Woocommerce so easily.

= Has there ever any compatibility issues? =
Since it's release, nothing has been noted.

= Is the code in the plugin proven stable? =

Please click the following link to check the current stability of this plugin:
<a href="https://plugintests.com/plugins/multi-emails-for-woocommerce/latest" rel="nofollow ugc">https://plugintests.com/plugins/multi-emails-for-woocommerce/latest</a>

== Screenshots ==

1. The Multi-Emails settings panel
2. Example of registered user with extra email options

== Upgrade Notice ==

None to report as of the release version

== Changelog ==

1.0.0 12/08/23
- Initial release