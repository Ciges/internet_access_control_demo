<?php
/**
 *  Test 9.4:  MongoDB analyse query:  Gets the mean by day for traffic volume in June
 *  
 *  This queries are done over the InternetAccessLogs database directly, which contains example data for 90 millions of records for Non FTP access logs and 4,5 millions of record for FTP access logs
 *  This script makes part of a list of scripts to compare MySQL 5.0.26 with MyISAM tables versus MongoDB 2.2.0rc0.
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20120906
 */
set_include_path(get_include_path() . PATH_SEPARATOR . "classes");
require_once("MongoRandomElements.class.php");
 
$mre = new MongoRandomElements("mongodb", "mongodb", "localhost", "InternetAccessLog");
$start = new MongoDate(strtotime("2012-06-01 00:00:00"));
$end = new MongoDate(strtotime("2012-06-30 23:59:59"));

$result = round($mre->getOne(array(
    array('$match' => array('datetime' => array( '$gte' => $start, '$lte' => $end ))),
    array('$project' => array('_id' => 0, 'day' => array ( '$dayOfMonth' => '$datetime' ), 'size' => 1)),
    array('$group' => array('_id' => '$day', 'volume' => array( '$sum' => '$size'))),
    array('$group' => array('_id' => 'all', 'average' => array( '$avg' => '$volume'))),
    array('$project' => array('_id' => '$average'))
    ), "NonFTP_Access_log"));

printf("Traffic volume mean by day in bytes for June: %.0f\n", $result);
?>
