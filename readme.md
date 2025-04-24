# Display photos selected by path from the Media Library

Contributors: hupe13    
Tags: albums, media library  
Tested up to: 6.8  
Stable tag: 250424     
Requires at least: 6.8     
Requires PHP: 8.1     
License: GPLv2 or later

Organize your photos in folders, select a path and display these photos with any gallery shortcode.

## Description

Organize your photos in folders, select a path and display these photos with any gallery shortcode.

You will get updates with the [Plugin Update Checker](https://github.com/YahnisElsts/plugin-update-checker).
Maybe you need a Github token.

The plugin is not ready yet. Please also check manually whether updates are available.

## Howto
<p>
<ul>
<li> Organize your photos in directories. Store the photos for each album in a subdirectory in the WordPress upload directory.</li>
<li> Useful plugins:
<ul>
 <li> <a href="https://wordpress.org/plugins/bulk-media-register/">Bulk Media Register</a> to import the photos to the Media Library.</li>
 <li> <a href="https://wordpress.org/plugins/upload-media-exif-date/">Upload Media Exif Date</a> to store to the date/time of Exif information.</li>
 <li> <a href="https://wordpress.org/plugins/exif-caption/">Exif Caption</a> to insert the Exif data to the caption of the media.</li>
</ul>
</li>
<li> Use the default <code>gallery</code> shortcode or install a plugin to display photos from Media Library, which has a shortcode for this, for example <a href="https://wordpress.org/plugins/photonic/">Photonic Gallery & Lightbox for Flickr, SmugMug & Others</a>.</li>
<li> Configure the name of this shortcode (default <code>gallery</code>) and the list option (default <code>ids</code>) in admin backend.</li>
<li> Write your shortcode as usual, omit the list option and use an extra option <code>path</code>.</li>
<li> <code>path</code> is a substring of the directory path, for example <code>holidays25/day1</code>.</li>
</ul>
</p>

## Changelog

### 250424

* Everything revised and new procedure
