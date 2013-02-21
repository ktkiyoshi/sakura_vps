#!/bin/sh

# Move to target directory
cd /var/www/html/

# Back up 
tar zcvf /home/kodamat/backup/backup_`date +%Y%m%d`.tgz *

# Delete old one
rm /home/kodamat/backup/backup_`date -d '7 days ago' +%Y%m%d`.tgz

# Result
echo `date +%Y%m%d`' Finished!'
cd /home/kodamat/backup/
ls -lh
