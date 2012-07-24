#!/bin/sh
alias mongoc='mongo -u mongodb -p mongodb InternetAccessLog'
alias php='/soft/php525/bin/php5 -c /soft/php525/php.ini'
for i in `seq 1 3`; do
    mongoc --eval "db.$2.drop();"
    time php "$1"
done;
