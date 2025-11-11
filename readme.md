#  Album of photos from a folder in the Media Library Github

Contributors: hupe13    
Tags: albums, Media Library  
Tested up to: 6.9  
Stable tag: 251112     
Requires at least: 6.7     
Requires PHP: 8.1     
License: GPLv2 or later

Organize your photos in folders, select a path and display these photos with any gallery shortcode.

## Description

Organize your photos in folders, select a path and display these photos with any gallery shortcode.

To get updates with the [Plugin Update Checker](https://github.com/YahnisElsts/plugin-update-checker) please
install [ghu-update-puc](https://github.com/hupe13/ghu-update-puc).

## Howto

* Organize your photos in directories. Store the photos for each album in a subdirectory in the WordPress upload directory. You can use a <a href="https://wordpress.org/plugins/search/media+library+folder/">plugin</a> to do this.
* Import these photos to the Media Library. Maybe your plugin has this function too.
* I use <a href="https://wordpress.org/plugins/bulk-media-register/">Bulk Media Register</a> to import the photos to the Media Library, which I uploaded before with sftp.
* Use the default `gallery` shortcode or install a plugin to display photos from Media Library, which has a shortcode for this, for example <a href="https://wordpress.org/plugins/photonic/">Photonic Gallery & Lightbox for Flickr, SmugMug & Others</a>.
* Configure the name of this shortcode (default `gallery`) and the list option (default `ids`) in admin backend.
* Write your shortcode as usual, omit the list option and use an extra option `path`.
* `path` is a substring of the directory path, for example `holidays25/day1`.

## Installation

* Install the plugin in the usual way.
* Go to Settings - Media - Media Album - and get documentation and settings options.

## Changelog

### 251112

* Tested with WordPress 6.9
* PCP V 1.7.0 checks reviewed
* ghu-update-puc

### 250530

* First Release of the WordPress plugin
