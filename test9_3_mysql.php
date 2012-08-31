<?php
/**
 *  Test 9.3:  MySQL analyse query:  Gets the 10 users with most hits
 *  First the 10 highest numeric values for hit will be get, and then all the users for this values will be retourned.
 *  
 *  This queries are done over the InternetAccessLogs database directly, which contains example data for 90 millions of records for Non FTP access logs and 4,5 millions of record for FTP access logs
 *  This script makes part of a list of scripts to compare MySQL 5.0.26 with MyISAM tables versus MongoDB 2.2.0rc0.
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20120830
 */
set_include_path(get_include_path() . PATH_SEPARATOR . "classes");
require_once("MySQLRandomElements.class.php");

$mre = new MySQLRandomElements("mysqldb", "mysqldb", "localhost", "InternetAccessLog");

// First, we get the minimum value of the 10 highest visits per user
$query = "select * from (select distinct(count(*)) as visits from NonFTP_Access_log group by user order by visits desc limit 10) as topten_visits_by_user order by visits limit 1";
$min_value = $mre->getOne($query);

// Now, we obtain all the users with at least that value
$query = "select * from (select user, count(*) as visits from NonFTP_Access_log group by user) as visits_by_user where visits >= ".$min_value;
$results = $mre->getResults($query);
while($row = $results->fetch_assoc())   {
    print_r($row);
    }
 
?>