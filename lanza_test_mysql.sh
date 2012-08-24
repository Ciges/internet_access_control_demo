#!/bin/sh
alias mysqlc='mysql -u mysqldb --password=mysqldb InternetAccessLog'
alias php='/soft/php525/bin/php5 -c /soft/php525/php.ini'
for i in `seq 1 1`; do
    time php "$1"
    echo "drop table $2"|mysqlc
done;
