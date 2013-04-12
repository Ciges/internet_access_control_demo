<?php
/**
 *  Counting records by date test in MySQL
 *  This script counts the number of records between two dates with more than a month between them. The idea is to force MySQL to search in more than one partition (in the case of a partitioned table by month)
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20130321
 */

set_include_path(get_include_path() . PATH_SEPARATOR . "classes");
require_once("MySQLRandomElements.class.php");

function microtime_float()  {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

$mre = new MySQLRandomElements();

$start_date = '2012-02-25';
$end_date = '2012-10-9';

$begin = microtime_float();
$n = $mre->getOne("select count(*) from NonFTP_Access_log where datetime between '".$start_date."' and '".$end_date. "'");
$end = microtime_float();
$ms_spent = ($end-$begin)*1000;
printf("Records between %s and %s, Time spent (ms): %d\n", $start_date, $end_date, $ms_spent);


?>
