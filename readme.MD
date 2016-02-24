Onawes!
=======

(Oh No, Another Website Edit System !)

Onawes allow to generate simple websites, based on Twitter Bootstrap 3.

It supports themes and custom blocks.

The structure is organized in pages, which contains rows, which contains blocks.

For now, blocks types are :

- Generic Text blocks (with markdown syntax)
- News blocks (with UI to create articles)
- Contact Form
- Address block

## Requirements

- PHP 5.4 (may work with previous PHP 5 versions, but not tested)
- Apache 2.4 or lighttpd (Nginx not tested)

Data is saved in JSON files, no SQL database needed !

## Install

### With Git

    git clone https://github.com/Dric/Onawes.git

### Manual install

Unzip archive on your web server

### Settings

Just access your website, the installer will run automatically.

## URL Rewriting

URL Rewriting : <https://ciarand.me/posts/refactoring-php-part-2/>

### Lighttpd

Edit `lighttpd.conf`, uncomment `"mod_rewrite"` and this line :

    url.rewrite-if-not-file = ("\/Onawes\/(.*)" => "/Onawes/index.php/$1")

Where `Onawes` is the script dir.

If Onawes is installed on root directory or in subdirectory not named 'onawes', do not forget to edit '.htaccess' file (only if webserver is Apache, of course)

## Components used

- [Twitter Bootstrap](http://getbootstrap.com) 3.3.1.
- [Font Awesome](http://fortawesome.github.io/Font-Awesome/) 4.5.0 : Replace `Glyphicon` from Bootstrap.
- [jQuery](http://jquery.com) 2.2.0.
- [Pagedown Bootstrap](http://kevin.oconnor.mp/pagedown-bootstrap) : Nearly-wysiwyg Markdown editor.
- [noty](http://ned.im/noty) - A jQuery Notification Plugin.
- [bootstrap-fileinput ](http://plugins.krajee.com/file-input) 4.3.0 : An enhanced HTML 5 file input for Bootstrap 3.x.
- [eqHeight](http://jsliang.com/eqHeight.coffee/) 1.3.5 : jQuery plugin that stretches fluid columns to equal height.
- [bootstrap-slider](https://github.com/seiyria/bootstrap-slider) 6.0.17 : Sliders for Bootstrap

Icon from <https://freeiconshop.com/icon/edit-document-icon-flat/>

