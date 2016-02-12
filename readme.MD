Onawes!
=======

(Oh No, Another Website Edit System !)

## URL Rewriting

URL Rewriting : <https://ciarand.me/posts/refactoring-php-part-2/>

### Lighttpd

Edit `lighttpd.conf`, uncomment `"mod_rewrite"` and this line :

    url.rewrite-if-not-file = ("\/Onawes\/(.*)" => "/Onawes/index.php/$1")

Where `Onawes` is the script dir.

If Onawes is installed on root directory or in subdirectory not named 'onawes', do not forget to edit '.htaccess' file (only if webserver is Apache, of course)

## Components used

* Pagedown-bootstrap : https://github.com/izambasiron/pagedown-bootstrap

En cas de mise à jour, penser à remettre la classe CSS "img-responsive" sur les images (faire une recherche sur `<img` et rajouter la classe partout)