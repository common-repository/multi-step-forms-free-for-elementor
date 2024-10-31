=== Multi-step Forms FREE (for Elementor) ===
Contributors: kyriscodes
Donate link: https://teklovers.com
Tags: page builder, editor, landing page, drag-and-drop, elementor, visual editor, wysiwyg, design, website builder, landing page builder, front-end builder, elementor-pro, elementor widget, survey, survey form, form, webform, elementor form, wordpress form
Requires at least: 5.0
Tested up to: 5.4.1
Requires PHP: 5.6
Stable tag: 1.2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple plugin that streamlines the creation of multistep (or multiple page) forms to an easy drag-and-drop through the power of Elementor Pro.
== Description ==
Multi-step Forms FREE (for Elementor) simplifies the process of creating multistep forms so easily that it's as simple as one, two, three.
1. Add the "Multistep Form Basic" widget to your page.
2. Set your page breaks (or where each page should begin) by adding pagebreaks (select from the 'field type' dropdown).
3. Add your form questions, and hit save!

Make use of Elementor Pro's built in functions to send emails, process webhooks, and/or redirect the user after the form is complete, seamlessly integrating the form into the look and feel of your website. From contact, to help forms, we've got you covered.  

== Installation ==
1. Upload `/multi-step-forms-free/` to the `/wp-content/plugins/` directory or add it via the wordpress repository.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Edit a page with Elementor Pro, add the "Multistep Form Basic" widget to your page, and design away.


== Frequently Asked Questions ==
= Do I need Elementor to run this plugin? =
Yes, the widget is coded to make use of Elementor's powerful drag-and-drop abilities, making it easy and simple to use.
= Do I need Elementor Pro to run this plugin? =
Yes, Elementor Pro is a required library in order to run this plugin.
= Do I need to know how to code/use HTML/CSS/JS? =
Right out of the box, you do not need to know how to use HTML/CSS/JS, however, to make changes to the styling and color of the different parts of the form, some basic knowledge of CSS is required.  
== CSS Styling Information for Advanced Users ==
The following are just a few examples of the CSS classes that you can style. Put all CSS changes in the Advanced > Custom CSS section when editing the widget.
- **All button and label text, except for the warning text**. "selector .elementor_multistep_free *:not(.multistep-warning) {}"
- **Forward button**. "selector .elementor_multistep_free .multistep-button-controls-forward {}"
- **Backwards button**. "selector .elementor_multistep_free .multistep-button-controls-backwards {}"
- **Submit button**. "selector .elementor_multistep_free .multistep-button-controls .multistep-submit {}"
- **All page steps**. "selector .elementor_multistep_free .step {}"
- **Current page step**. "selector .elementor_multistep_free .step.active {}"
- **All step super wrapper**. "selector .elementor_multistep_free .current-page-tracker {}"
- **Space between buttons/steps and last form field on page**. "selector .elementor_multistep_free .multistep-button-controls-spacer {}"
- **Page**. "selector .elementor_multistep_free .page_break_class {}"
- **Warning text for failed validation**. "selector .elementor_multistep_free .multistep-warning {}"
== Screenshots ==
1. View of the Multi-step Forms FREE (for Elementor) widget in action.
== Changelog ==

= 1.2.4 = 
* Added field for personalized message of the following error response: "This field is required"
* Added field for personalized message of the following error response: "Max length is 15 digits."
* Added field for personalized message of the following error response: "This field doesn't follow phone format"
* Added field for personalized message of the following error response: "This field doesn't follow email format"

= 1.2.3 = 
* Fixed bug that caused phone fields to not validate properly
* Added Select Dropdown Placeholder (optional)
* Modified validation policy to only check a field when attempting to submit or after it is changed/loses focus
* Fixed bug that caused email fields to not validate properly

= 1.2.2 =
* Fixed bug that caused radio buttons to be required, even when not required
* Fixed bug that caused all checkbox values to show in email, even non-selected ones
* Fixed bug that caused all select-boxes values set to multiple to show in email, even non-selected ones.

= 1.2.1 =
* Fixed bug that prevented custom button text for forward
* Fixed bug that prevented custom button text for backward
= 1.2.0 =* Fixed bug that caused Utils to not load properly* Fixed bug that caused shortcodes to de-populate after using the admin panel for some time* Fixed bug that caused forms to not submit while in a popup* Added queried post internal field to support= 1.1.9 =* Fixed bug that caused reply-to fields to not populate* Fixed bug that caused shortcodes to not populate* Fixed bug that allowed required multichoice questions / radios to be skipped and not properly validated.= 1.1.8 =* Fixed bug that caused the File Upload Field to not display field specific options* Added compatibility to the Upload Field for warnings and messages when a file is added that does not support base parameters
= 1.1.7 =
* Fixed bug that cause an error from get_post_id() to prevent the page from loading.

= 1.1.6 =
* Fixed bug that caused HQ Referral Link to cause page from preventing to load.
= 1.1.5 =
* Fixed bug that prevented Elementor from loading.
= 1.1.4 =
* Added support for Shortcodes
* Fixed bug that prevented the captcha from submitting correctly.
= 1.1.3 =
* Bug fix for js preventing captcha from displaying correctly.
= 1.1.2 =
* Bug fix for js preventing submission on certain websites.
= 1.0.0 =
* Inital Release.
* Added "Multistep Form Basic" widget
* Fixed bug that caused the step tracker to wrap incorrectly



