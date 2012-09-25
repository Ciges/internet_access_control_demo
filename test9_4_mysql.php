<?php
/**
 *  Test 9.4:  MySQL analyse query:  Gets the mean by day for traffic volume in June
 *  
 *  This queries are done over the InternetAccessLogs database directly, which contains example data for 90 millions of records for Non FTP access logs and 4,5 millions of record for FTP access logs
 *  This script makes part of a list of scripts to compare MySQL 5.0.26 with MyISAM tables versus MongoDB 2.2.0rc0.
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20120905
 */
set_include_path(get_include_path() . PATH_SEPARATOR . "classes");
require_once("MySQLRandomElements.class.php");

$mre = new MySQLRandomElements("mysqldb", "mysqldb", "localhost", "InternetAccessLog");

$start = "2012-06-01 00:00:00";
$end = "2012-06-30 23:59:59";
$query="select round(avg(volume)) from (select sum(size) as volume from NonFTP_Access_log where datetime between \"".$start."\" and \"".$end."\" group by dayofmonth(datetime)) as sizebyday";
$result = $mre->getOne($query);

printf("Traffic volume mean by day in bytes for June: %.0f\n", $result);
 
?>
