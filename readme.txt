=== Gravity Forms ConvertKit Add-On ===
Contributors: nathanbarry, growdev, travisnorthcutt
Donate link: https://convertkit.com
Tags: email, marketing, embed form, convertkit, capture
Requires at least: 5.0
Tested up to: 5.9
Requires PHP: 5.6.20
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

ConvertKit is an email marketing platform for capturing leads from your WordPress blog.

== Description ==

[ConvertKit](https://convertkit.com) makes it easy to capture more leads and sell more products by easily embedding email capture forms anywhere.

This Plugin integrates Gravity Forms with ConvertKit, allowing form submissions to be automatically sent to your ConvertKit account.

Full plugin documentation is located [here](https://help.convertkit.com/en/articles/2502569-gravity-forms-integration).

== Installation ==

1. Upload the `convertkit-gravity-forms` folder to the `/wp-content/plugins/` directory
2. Active the ConvertKit for Gravity Forms plugin through the 'Plugins' menu in WordPress

== Configuration ==

1. Configure the plugin by navigating to Forms > Settings > ConvertKit in the WordPress Administration Menu, entering your [API Key](https://app.convertkit.com/account_settings/advanced_settings)
2. Configure sending Gravity Form Entries to ConvertKit by defining one or more Feeds. This is done by editing your Gravity Form, and navigating to Settings > ConvertKit within the Form.

== Frequently asked questions ==

= Does this plugin require a paid service? =

No. You must first have an account on ConvertKit.com, but you do not have to use a paid plan!

== Screenshots ==

1. 
2. 

== Changelog ==

### 1.2.1 2022-01-xx
* Added: Settings: Debug option
* Fix: Settings: API Key: Show error notification and contextual tooltip error message when an invalid API Key is specified

### 1.2.0  2021-05-18

* Support Gravity Forms v2.5

### 1.1.0  2019-06-09

* Correctly send full name to ConvertKit when used in a field mapping
* Only show field mappings for ConvertKit custom fields if they exist
* Output correct form URL on feed settings screen
* No longer requires a name field mapping, for email-only forms

### 1.0.4  2018-03-14

* PHPCS code cleanup.
* Testing with latest version of WordPress and Gravity Forms v2.2.6.
* Added languages directory with pot file.

### 1.0.3  2017-01-05

* Added ability to map ConvertKit Custom Fields to GF entries.
* Cleaned up PHPDoc

### 1.0.2

* Email mapping field can now be mapped to a Simple Text Field
* Added growdev as contributor

### 1.0.1

* Added delayed payment support for GF PayPal
* Code cleanup

### 1.0.0

* Initial release

== Upgrade notice ==
