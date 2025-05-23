#  Album of photos from a folder in the Media Library

Contributors: hupe13    
Tags: albums, Media Library  
Tested up to: 6.8  
Stable tag: 250509     
Requires at least: 6.7     
Requires PHP: 8.1     
License: GPLv2 or later

Organize your photos in folders, select a path and display these photos with any gallery shortcode.

## Description

Organize your photos in folders, select a path and display these photos with any gallery shortcode.

You will get updates with the [Plugin Update Checker](https://github.com/YahnisElsts/plugin-update-checker).
Maybe you need a Github token.

## Howto
<p>
<ul>
<li> Organize your photos in directories. Store the photos for each album in a subdirectory in the WordPress upload directory. You can use a <a href="https://wordpress.org/plugins/search/media+library+folder/">plugin</a> to do this.</li>
<li> Import these photos to the Media Library. Maybe your plugin has this function too.</li>
<li> I use <a href="https://wordpress.org/plugins/bulk-media-register/">Bulk Media Register</a> to import the photos to the Media Library, which I uploaded before with sftp.</li>
<li> Use the default <code>gallery</code> shortcode or install a plugin to display photos from Media Library, which has a shortcode for this, for example <a href="https://wordpress.org/plugins/photonic/">Photonic Gallery & Lightbox for Flickr, SmugMug & Others</a>.</li>
<li> Configure the name of this shortcode (default <code>gallery</code>) and the list option (default <code>ids</code>) in admin backend.</li>
<li> Write your shortcode as usual, omit the list option and use an extra option <code>path</code>.</li>
<li> <code>path</code> is a substring of the directory path, for example <code>holidays25/day1</code>.</li>
</ul>
</p>

## Installation

* Install the plugin in the usual way.
* Go to Settings - Media - Media Album - and get documentation and settings options.

## Changelog

### 250507 - 09

* Menus
* documentation

### 250504

* Use transients

### 250428

* Update error on multisite fixed

### 250427

* german translation
* update procedure for all my plugins adjusted
* php warning about path fixed

### 250425

* Everything revised and new procedure
