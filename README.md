## Test Data Generator

This extension aims to provide an automated tool to generate high quality, life-like data for The Events Calendar family of plugins.

### Setting it up

Setting up the extension is simple, just install it and activate it on your WordPress site, alongside The Events Calendar (v5.1.1+).

You can access all the available features in `Events > Test Data`.

#### What you can do:

1. **Create Venues:** you can automatically generate 10, 100, 1,000 or 10,000 venues for your site.
1. **Create Organizers:** you can automatically generate 10, 100, 1,000 or 10,000 organizers for your site.
1. **Create Events:** you can automatically generate 10, 100, 1,000 or 10,000 events, pulling from your pre-existing Venues, Organizers and uploaded images in your Media Library. Each event's date will be randomly selected, between `-1 month` and `+1 month` from `now`.

PLEASE NOTE that the amount of Venues, Organizers and Events you can create might be limited by your site's server setup (Available memory, timeout, processing power, etc.)

##### Handy Dandy Toolset
1. **Import random images into your site's Media Library:** you can choose to import a few or a bunch of images to your site. A random selection of images with aspect ratio 16:9 will be selected from the service Picsum.photo
1. **Clear all Events-related posts:** this will help you when you need to scratch everything and start over! Automatically removes every Venue, Organizer and Event from your site! Please note that if you have a lot of them, this request may time-out.