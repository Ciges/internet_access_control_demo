#!/bin/sh
alias php="$PHP_PEAR_PHP_BIN -c $PHP_CONFIG"
alias phpdoc="php /users2/mnd01/libext/php/pear/bin/phpdoc"
phpdoc --sourcecode on -d classes -o HTML:Smarty:HandS -t doc/

