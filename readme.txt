=== Gravity Forms ConvertKit Add-On ===
Contributors: nathanbarry, growdev, travisnorthcutt
Donate link: https://convertkit.com
Tags: email, marketing, embed form, convertkit, capture
Requires at least: 5.0
Tested up to: 6.5
Requires PHP: 5.6.20
Stable tag: 1.4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

ConvertKit is an email marketing platform for capturing leads from your WordPress blog.

== Description ==

** Please use the official [Gravity Forms ConvertKit Add-On](https://www.gravityforms.com/blog/convertkit-add-on/). Your existing settings will automatically migrate once installed and active. This Add-on will only receive security updates. **

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

1. Gravity Forms Form's Feed ConvertKit Settings
2. Gravity Forms ConvertKit Settings

== Changelog ==

### 1.4.3 2024-03-08
* Updated: ConvertKit WordPress Libraries to 1.4.2

### 1.4.2 2024-02-09
* Added: Notice recommending to use the official [Gravity Forms ConvertKit Add-On](https://www.gravityforms.com/blog/convertkit-add-on/)

### 1.4.1 2024-01-16
* Updated: ConvertKit WordPress Libraries to 1.4.1

### 1.4.0 2023-11-09
* Updated: ConvertKit WordPress Libraries to 1.4.0

### 1.3.9 2023-10-05
* Updated: ConvertKit WordPress Libraries to 1.3.9

### 1.3.8 2023-08-31
* Updated: WordPress Coding Standards
* Updated: ConvertKit WordPress Libraries to 1.3.8

### 1.3.7 2023-07-17
* Added: Enable Creator Network Recommendations modal on individual Forms at Forms > Settings
* Updated: ConvertKit WordPress Libraries to 1.3.7

### 1.3.6 2023-06-13
* Updated: ConvertKit WordPress Libraries to 1.3.6

### 1.3.5 2023-04-06
* Updated: ConvertKit WordPress Libraries to 1.3.4

### 1.3.4 2023-03-30
* Updated: Tested with WordPress 6.2

### 1.3.3 2023-02-23
* Updated: ConvertKit WordPress Libraries to 1.3.3

### 1.3.2 2023-02-14
* Updated: ConvertKit WordPress Libraries to 1.3.2

### 1.3.1 2023-02-02
* Fix: Form: Feed Settings: ConvertKit Form: Display ConvertKit Forms in alphabetical order
* Fix: Form: Feed Settings: Map Fields: Display Custom Fields in alphabetical order

### 1.3.0 2023-01-16
* Updated: ConvertKit WordPress Libraries to 1.3.0

### 1.2.9 2023-01-05
* Fix: PHP Warning: Trying to access array offset on value of type null

### 1.2.8 2022-11-21
* Updated: Compatibility with WordPress 6.1.1

### 1.2.7 2022-10-25
* Updated: ConvertKit WordPress Libraries to 1.2.1

### 1.2.6 2022-09-07
* Development: Moved /lib folder to managed repository

### 1.2.5 2022-07-20
* Fix: Capabilities: Correctly define capabilities for add-on settings, form settings and uninstall action

### 1.2.4 2022-06-23
* Added: Support for WordPress 6.0

### 1.2.3 2022-04-19
* Added: Form: Entries: Show ConvertKit icon next to Form Entries' Notes

### 1.2.2 2022-04-05
* Fix: Updated API Class

### 1.2.1 2022-03-17
* Added: PHP 8.x compatibility
* Added: Developers: Action and filter hooks.  See https://github.com/ConvertKit/convertkit-gravity-forms/blob/main/ACTIONS-FILTERS.md
* Added: Developers: Capabilities for Settings Page, Form Settings and Uninstall actions
* Added: Settings: Debug option
* Added: Form: Feed Settings: Optionally tag subscriber
* Added: Form: Feed Settings: Optionally map a Form Field to be used as the value for tagging a subscriber
* Added: Form: Entries: Log success and error messages to Form Entries' Notes
* Fix: Settings: API Key: Show error notification and contextual tooltip error message when an invalid API Key is specified
* Fix: Form: Feed Settings: Don't attempt to subscribe to ConvertKit if the Email field's value isn't an email address 

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
