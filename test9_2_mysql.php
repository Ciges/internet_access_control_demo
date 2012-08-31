<?php
/**
 *  Test 9.2:  MySQL analyse query:  Gets the 10 domains most visited in the second half of August and the number of visits for each one.
 *  First the 10 highest numeric values for visits will be got, and then all the domains for this values will be retourned.
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

// First, we get the minimum value of the 10 highest visits per domain
$start = "2012-08-15 00:00:00";
$end = "2012-08-31 23:59:59";
$query = "select * from (select distinct(count(*)) as visits from NonFTP_Access_log where datetime between \"".$start."\" and \"".$end."\" group by domain order by visits desc limit 10) as topten_visits_by_domain order by visits limit 1";
$min_value = $mre->getOne($query);

// Now, we obtain all the domains with at lest that value
$query = "select * from (select domain, count(*) as visits from NonFTP_Access_log where datetime between \"".$start."\" and \"".$end."\" group by domain) as visits_by_domain where visits >= ".$min_value;

$results = $mre->getResults($query);
while($row = $results->fetch_assoc())   {
    print_r($row);
    }

?>