# Photonic album from Media Library Github

Contributors: hupe13    
Tags: photonic, media library  
Tested up to: 6.8  
Stable tag: 250420     
Requires at least: 6.8     
Requires PHP: 8.2     
License: GPLv2 or later

Display photos from Media Library by path with <a href="https://wordpress.org/plugins/photonic/">Photonic Gallery & Lightbox for Flickr, SmugMug & Others</a>.

## Description

Display photos from Media Library by path with <a href="https://wordpress.org/plugins/photonic/">Photonic Gallery & Lightbox for Flickr, SmugMug & Others</a>.

You will get updates with the [Plugin Update Checker](https://github.com/YahnisElsts/plugin-update-checker).
Maybe you need a Github token.

The plugin is not ready yet. Please also check manually whether updates are available.

## Howto

- Organize your photos in folders. Store the photos for each album in a folder of the WordPress upload folder.
- Use a plugin like [Bulk Media Register](https://wordpress.org/plugins/bulk-media-register/) to import the photos to the Media Library.
- Use the Photonic Gallery block to generate the display of photos.
- Choose as Gallery Source "WordPress".
- Choose as Type of Gallery "Photos from Media Library"
- Select one any photo.
- Pick Your Layout.
- Configure Your Layout.
- In window "Your Gallery" select the shortcode and paste this into a Shortcode block.
- Change the shortcode from
```
[photonic ids='....' main_size='...' tile_size='...' style='...' ....]
```
to
```
[photonic-album path='....' main_size='...' tile_size='...' style='...' ....]
```
- Use as `path` a substring of the path, for example `holidays25`.

## Changelog

### 250420

- initial release
