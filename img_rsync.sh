#!/bin/sh

#Move to target directory
cd /var/www/html/
echo `diff img wp/wp-content/themes/kiyoshi.wp/img`

#rsync
rsync -acv -e ssh img/ wp/wp-content/themes/kiyoshi.wp/img/

#Result
echo `diff img wp/wp-content/themes/kiyoshi.wp/img`
echo `date +%Y%m%d`' Finished!'
