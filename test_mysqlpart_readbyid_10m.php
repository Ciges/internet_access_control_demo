<?php
/**
 *  Record reading test in MySQL
 *  This script read Non FTP log entries from database searching by a random id. The operations is repeated 10.000 times
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20130408
 */

set_include_path(get_include_path() . PATH_SEPARATOR . "classes");
require_once("MySQLRandomElements.class.php");

$logentries = 10000; # Number of log entries to read from database
# The id is an autoincrement integer. Minimun and maximum to search by.
$minid = 1;
$maxid = 60000000;

function microtime_float()  {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

$mre = new MySQLRandomElements();

$i = 0;
$begin = microtime_float();
while ($i < $logentries)  {
    $logentry = $mre->getRow("select * from NonFTP_Access_log where id=".mt_rand($minid,$maxid).";");
    $i++;
}
$end = microtime_float();
$ms_spent = ($end-$begin)*1000;
printf("Time spent (ms): %d\n", $ms_spent);

?>
