#!/bin/sh
cd $(dirname $0)
for script in ?*
  do
    if [ $script != '..' ] && [ $script != '.git' ]
      then
        ln -Fis "$PWD/$script" /var/www/dat
    fi
  done
echo 'All scripts are updated!'
