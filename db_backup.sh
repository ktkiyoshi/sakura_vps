#!/bin/sh

# Read setting file
. /home/kodamat/scripts/shell_env

FILENAME=db_backup_`date +%Y%m%d`.sql

# Back up 
mysqldump -u${USER} -p${PASS} _kiyoshi_wp > /home/kodamat/backup/${FILENAME}

# Delete old one
rm /home/kodamat/backup/db_backup_`date -d '7 days ago' +%Y%m%d`.sql

# Result
echo `date +%Y%m%d`' Finished!'
cd /home/kodamat/backup/
ls -lh
