## Test Data Generator

This extension aims to provide an automated tool to generate high quality, life-like data for The Events Calendar family of plugins.

### Setting it up

Setting up the extension is simple, just install it and activate it on your WordPress site, alongside The Events Calendar (v5.1.1+).

You can access all the available features in `Events > Test Data`.

#### What you can do:

1. **Create Venues:** you can automatically generate any specified number of venues for your site.
1. **Create Organizers:** you can automatically generate any specified number of organizers for your site.
1. **Create Events:** you can automatically generate any specified number of events, pulling from your pre-existing Venues, Organizers and uploaded images in your Media Library. You can select the date range for which each generated event's date will be randomly selected. Generated events can be standard events, Virtual events or Recurring events. You can also choose to add RSVP and/or Tickets to the generated events.

###### PLEASE NOTE that the amount of Venues, Organizers and Events you can create might be limited by your site's server setup (Available memory, timeout, processing power, etc.)

##### Handy Dandy Toolset
1. **Import random images into your site's Media Library:** you can choose to import a few or a bunch of images to your site. A random selection of images with aspect ratio 16:9 will be selected from the service Picsum.photo
1. **Clear generated Events-related posts:** this will help you when you need to scratch everything that has been automatically generated and start over! Automatically removes every Venue, Organizer and Event created using this extension. This won't delete your pre-existing Organizers, Venues or Events.
1. **Delete ALL Events-related posts:** this will do as promise and wipe out all existing Organizers, 
1. **Reset TEC Settings and Options:** this will clear out all your TEC-related options, settings and transients, effectively giving you a "factory reset".

###### Please note that if you have a lot of Organizers, Venues or Events, the delete requests may time-out.

#### WP-CLI Support

Example:
```bash
wp tec-test-data events generate 23 --with-organizers=10 --with-venues=5 --with-images=10
```
The command above will:
* Upload 10 images
* Create 5 Venues
* Create 10 Organizers
* Create 23 Events

To specify a value each available option via the built-in prompt, use:
```bash
wp tec-test-data events generate --prompt 
```

Using the delete command:

To delete **generated** events-related data:
```bash
wp tec-test-data delete
```

To delete **all** events-related data:
```bash
wp tec-test-data delete --all
```

For a full list of supported capabilities, check out src/Tribe/Cli/Command.php 
