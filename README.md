# Album of photos from a folder in the Media Library Github

## Description

Display photos from the media library - which are organized into folders - using any gallery shortcode.

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

Please install <a href="https://github.com/hupe13/ghu-update-puc">ghu-update-puc</a> to get updates and keep an eye on this repository in case I've made any mistakes.

## Changelog

see <a href="https://github.com/hupe13/album-medialib-github/blob/main/CHANGELOG.md">Changelog</a>
