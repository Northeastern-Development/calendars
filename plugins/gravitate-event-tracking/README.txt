=== Gravitate Event Tracking ===
Contributors: (Gravitate)
Tags: Gravitate, Event Tracking, Google Analytics
Requires at least: 3.5
Tested up to: 4.7.4
Stable tag: trunk

Easily create Tracking Events to be sent to Google Analytics.

== Description ==

Author: Gravitate http://www.gravitatedesign.com

Description: Easily create Tracking Events to be sent to Google Analytics. This Plugin only adds the Tracking Script to your website.
It does not offer any reports. To view the Tracking details, you will need to login to your Google Analytics account that is associated with this website.
Google Analytics Reports for Event Tracking are in real time, so you should be able to see the results immediately from your Google Analytics account.

==Requirements==

- jQuery
- Google Analytics Tracking Code with or without Universal Code
- WordPress 3.5 or above


== Installation ==

1. Upload the `gravitate_event_tracking` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. You can configure the Plugin Settings in `Settings` -> `Gravitate Event Tracking`

== Screenshots ==

1. Configuration Page
2. Import Settings from another Site.
3. Advanced Settings to delay the links to elements.

== Changelog ==

= 1.5.3 =
* Added delay for Google Analytics when not found to try again.

= 1.5.2 =
* Added defer="defer" Tracking script.

= 1.5.1 =
* Added 'displayfeatures' to Google Analytics script.

= 1.5.0 =
* Added the ability to add Google Tag Manager to the site.
* Updated Events to work with Google Tag manager is loading Google Analytics

= 1.4.1 =
* Added Form Submissions
* Added On Change Events

= 1.4.0 =
* Added ability to embed Google Analytics Code to website
* Added Scroll and Resize Presets and Options
* Cleaned Up Code
* Fixed Bug with duplicate Labels
* Fixed Bug with duplicate firing

= 1.3.1 =
* Updated Debugging Info

= 1.3.0 =
* Updated Debugging

= 1.2.2 =
* Added Debugging
* Changed Order of check system for jQuery

= 1.2.1 =
* Removed js alert for debugging

= 1.2.0 =
* Added Advanced Settings Page. Now comes with Delay re-capture settings. This allows you to setup tracking events to elements that get created after load. The Advanced Settings will also be Imported and Exported with the Import/Export feature.  PLEASE NOTE that Exporting Settings from version 1.2.0 will not work importing in version 1.1.1 or lower.  But you can Import settings from 1.1.1 or lower into version 1.2.0

NOTE: When needing to use the Delay Re-Capture make sure your Selectors are unique to the elements that get created.  Otherwise it may find another match on load and will not fire the delay to capture the other elements.

= 1.1.1 =
* Added Relative URL Tag to lables.

= 1.1.0 =
* Added Import and Export Settings Option.

= 1.0.1 =
* Fixed Bug for Universal Code - HitType was improperly set.

= 1.0.0 =
* Initial Creation
