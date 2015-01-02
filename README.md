podcastFeed
===========

p3 populär podcast feed

[![Coverage Status](https://img.shields.io/coveralls/christiansipola/podcastFeed.svg)](https://coveralls.io/r/christiansipola/podcastFeed?branch=master)

    <VirtualHost *>
      ServerName podcast.kungekasen.se
    
      ## Vhost docroot
      DocumentRoot "/var/www/podcastfeed/web"
    
      Options -MultiViews
    
      RewriteEngine On
        # Do not enable rewriting for files that exist
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
    
        # Rewrite to index.php/URL
      RewriteRule ^(.*)$ /index.php/$1 [PT,L]
    
    </VirtualHost>
