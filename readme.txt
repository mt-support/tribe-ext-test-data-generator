=== The Events Calendar Extension: Test Data Generator ===
Contributors: ModernTribe, aguseo, bordoni, Camwyn, lirianojoel, lucatume
Donate link: http://m.tri.be/29
Tags: events, calendar
Requires at least: 4.9.14
Tested up to: 5.4.2
Requires PHP: 5.6
Stable tag: 1.0.5
License: GPL version 3 or any later version
License URI: https://www.gnu.org/licenses/gpl-3.0.html

== Description ==

This extension aims to provide an automated tool to generate high quality, life-like data for The Events Calendar family of plugins.

== Installation ==

Install and activate like any other plugin!

* You can upload the plugin zip file via the *Plugins ‣ Add New* screen
* You can unzip the plugin and then upload to your plugin directory (typically _wp-content/plugins_) via FTP
* Once it has been installed or uploaded, simply visit the main plugin list and activate it

== Frequently Asked Questions ==

= Where can I find more extensions? =

Please visit our [extension library](https://theeventscalendar.com/extensions/) to learn about our complete range of extensions for The Events Calendar and its associated plugins.

= What if I experience problems? =

Please create a GitHub issue inside the project.

== Changelog ==

= [1.0.5] 2020-10-29 =

* Enhancement - Display request time-out warning across the UI, to let users know which requests may time-out and next steps.
* Feature - Add option to generate events marked as "Featured event".
* Feature - Add custom Event Category for generated events.
* Feature - Add custom Tag for generated events.

= [1.0.4] 2020-08-11 =

* Enhancement - Add WP-CLI support for creating Virtual and Recurring events, and support for the TEC Reset functionality.
* Feature - Add progress bar to WP-CLI functionality.
* Feature - Add Event Category and Tag for generated events.
* Fix - Update event cost meta after creating RSVP or Ticket so the event cost info can be displayed correctly in the calendar views.

= [1.0.3] 2020-07-30 =

* Feature - Ability to create Recurring Events.
* Feature - Option to reset TEC settings to "factory defaults".
* Enhancement - Updated UI to allow more detailed control over the amount of Venues, Organizers or Events that can be generated, as well as to provide a wider range of options for the event's date range.

= [1.0.2] 2020-07-14 =

* Feature - Ability to create Virtual Events with YouTube embed or Zoom meeting link. Requires Virtual Events 1.0.1 or later.

= [1.0.1] 2020-07-07 =

* Feature - Add WP-CLI support.

= [1.0.0.1] 2020-06-29 =

* Fix - Prevent crash in Admin page if Event Tickets is not available.

= [1.0.0] 2020-06-26 =

* Feature - Automatically generate test Organizers.
* Feature - Automatically generate test Venues.
* Feature - Automatically generate test Events.
* Feature - Ability to select date range for test Events creation.
* Feature - Ability to add RSVP to generated test Events.
* Feature - Ability to add Ticket to generated test Events.
* Feature - Upload randomly selected images from Picsum.photos into your WP site.
* Feature - Delete only automatically generated test Organizers, Venues and Events.
* Feature - Delete ALL existing Organizers, Venues and Events.