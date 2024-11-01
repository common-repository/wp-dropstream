=== DropStream - Automated eCommerce Fulfillment ===
Contributors: karlfalconer, Dropstream
Donate link: http://getdropstream.com/merchants
Tags: e-commerce, ecommerce, fulfillment, wp-e-commerce, woocommerce, fulfillment by amazon
Requires at least: 4.0
Tested up to: 5.9
Stable tag: 1.2.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

DropStream is a powerful eCommerce plugin that integrates your WordPress site with your shipping solution or third-party fulfillment provider, allowing for automated processing of your sales orders.

== Description ==

= DropStream Automates Your Order Fulfillment process =

DropStream is the leading provider of ecommerce fulfillment integrations, allowing merchants to automate the process of fulfilling their sales orders. DropStream automates 3 main processes:

1. Automatically send orders to your fulfillment center or shipping solution
2. Automatically send tracking numbers back to WordPress, notifying shoppers that their package is on the way
3. Automatically update product inventory levels, keeping your online store updated with accurate available quantities

DropStream offers two core products:

1. DropStream (http://getdropstream.com/)
2. PackageBee (http://packagebee.com/)

Depending on your integration needs, you are able to you use one or the other. Contact DropStream to find out which product is best for you. (http://getdropstream.com/contact)

= See what others say about DropStream =

> We were hampered by data entry work-arounds, until DropStream tore down the technical wall that stood between us and our customers. 
> -- Clay Clarkson, Whole Heart Ministries


> DropStream is a useful capability that gives us greater flexibility in meeting our fulfillment requirements. The service was easy to set up and has been very reliable. 
> -- Scott Madsen, National Imports LLC

= Get Started With Your Free 14-day Trial =

DropStream is actively integrated with thousands of fulfillment providers in North America and Europe, giving you tremendous flexibility on where to send your sales orders. You can see a [list of desitnation systems: (http://support.getdropstream.com/customer/en/portal/articles/2847048-currently-supported-warehouse-systems-wmss-imss-erps-?b_id=2404)] on our website. Don't know the system used by your fulfillment center? [Contact us](http://getdropstream.com/contact "Contact DropStream") and we'll confirm whether we currently support the system or if we can add support for it for you.

== Installation ==

= Minimum Requirements =

* WordPress 4.0 or greater
* PHP version 5.3 or greater
* MySQL version 5.0 or greater
* Some payment gateways require fsockopen support (for IPN access)
* Compatible Wordpress eCommerce plugin. Ex: WooCommerce
* WooCommerce 3.0 and greater

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t even need to leave your web browser. To do an automatic install of Dropstream:

1. Log in to your WordPress admin panel
2. Navigate to the Plugins menu and click Add New.
3. In the search field type “Dropstream” and click Search Plugins. 

Once you’ve found our eCommerce plugin you can view details about it such as the the point release, rating and description. Most importantly of course, you can install it by simply clicking Install Now. After clicking that link you will be asked if you’re sure you want to install the plugin. Click yes and WordPress will automatically complete the installation.

= Manual installation =

The manual installation method involves downloading our plugin and uploading it to your webserver via your favourite FTP application.

1. Download the plugin file to your computer and unzip it
2. Using an FTP program, or your hosting control panel, upload the unzipped plugin folder to your WordPress installation’s wp-content/plugins/ directory.
3. Activate the plugin from the Plugins menu within the WordPress admin.

== Frequently Asked Questions ==

= Which WordPress eCommerce Plugin's does this plugin support = 

This plugin supports WP E-Commerce and WooCommerce with an active Dropstream account.

= What do I need to get started? =

You'll an active DropStream account and need to have a relationship with a fulfillment center that ships your orders to customers. You can see a [full list of fulfillment centers](http://getdropstream.com/merchants/supported-connectors "Dropstream for Merchants eCommerce connectors") on our website. Don't see your fulfillment center listed? [Contact us](http://getdropstream.com/merchants/contact-us "Contact Dropstream") and we'll add it, free of charge.

= Where can I find Dropstream documentation and user guides =

For help setting up and configuring Dropstream please refer to our [user guide](http://support.getdropstream.com)

== Screenshots ==

1. Support multiple sales channels including Amazon Marketplace, and eBay
2. Paid orders are automatically sent to your fulfillment center
3. Orders are accurate every time
4. Tracking numbers are automatically sent back to your shopping cart

== Changelog ==
= 1.2.3
* Compatibility update 
* Add support for Sequential Order Numbers for WooCommerce (wt-woocommerce-sequential-order-numbers)

= 1.2.2
* Fix syntax error in release 1.2.1

= 1.2.0
* Added support for Shipment service level to Order Metadata

= 1.1.8
* Added OrderItem id to response
* Added Order date_paid to response

= 1.1.7
* Added Currency Code to ordr data

= 1.1.6
* Formating ordered_at DateTime to string

= 1.1.5
* Add 'payment_method' to exported order data

= 1.1.4
* Ensure that only published products receieve an inventory update

= 1.1.2
* Version Bump

= 1.1.1
* Renamed internal logging method to avoid conflicts with other plugins

= 1.1.0
* Added support for Product Bundles extension

= 1.0.0
* WARNING - Do not upgrade unless the mimumum requirements are met.
* No longer supporting Wordpress < 4.0 
* No longer supporting WooCommerce < 3.0
* Added batch inventory update support
* Added 'wc_dropstream_order' filter for post-processing of an order before it is returned to DropStream server
* Fixed WooCommerce 3+ deprecation warnings

= 0.9.3
* Fix for shipping address mapping

= 0.9.2
* Fix code which used WooCommerce model attributed directly.

= 0.9.1
* Fix for importing order item unit price instead of product unit price.

= 0.9.0
* Added support for Shipment Tracking 1.6.4

= 0.8.6
* Allow for orders in the 'Awaiting Fulfillment' status to be included in the my-account.php list

= 0.8.5
* Bug fix to be compatible with renamed "wp-content" directories

= 0.8.4
* Added support for order numer with plugin woocommerce-sequential-order-numbers

= 0.8.3
* Default Shipping Phone to Billing Phone

= 0.8.2
* Fixed PHP Warning for WooCommerce 2.2.8 Order Reports

= 0.8.1
* Fixed problem where WooCommerce order item references deleted product

= 0.8.0
* Added support for Woocommerce 2.2.x

= 0.7.2
* Confirmed compatibility with WP 4.0

= 0.7.1
* Update WooCommerce reports to include 'awaiting-fulfillment' order status

= 0.7.0
* Added support for additional WooCommerce order fields, including discounts, taxes, and coupons. WooCommerce 2.1 or greater is required

= 0.6.6
* Bump for WP 3.9 and WooCommerce 2.1 support

= 0.6.5
* Added support for WooCommerce subscription.
* Change to use WooCommerce shipping method title, rather than shipping method id. **NOTE** Dropstream rules will need to be updated.

= 0.6.3
* Added custom order status for WooCommerce 'awaiting-fulfillment'. This order status will be used to acknowledge orders have been received by the fulfillment center.

= 0.6.2
* Added check for WooCommerce to skip processing order_items with an invalid product_id

= 0.6.1
* Added default value for created_after date filter

= 0.6.0
* Added additional order search filter for WooCommerce. NOTE: Only available on WP 3.7 or higher
* Fix for WooCommerce order search only returning top 10 orders

= 0.5.5 =
* Added support for WooCommerce Variable product types

= 0.5.0 =
* Added support for [WooCommerce Sequential Order Numbers Pro](http://www.woothemes.com/products/sequential-order-numbers-pro/)

= 0.0.2 =

* Support for WooCommerce.
* Updated WP E-Commerce order data to include customer e-mail address.

= 0.0.1 =

* This is the initial release with support for  WP E-Commerce
