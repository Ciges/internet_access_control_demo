<?php
/**
 *  Test 9.2:  MongoDB analyse query:  Gets the 10 domains most visited in the second half of June and the number of visits for each one.
 *  First the 10 highest numeric values for visits will be got, and then all the domains for this values will be retourned.
 *  
 *  This queries are done over the InternetAccessLogs database directly, which contains example data for 90 millions of records for Non FTP access logs and 4,5 millions of record for FTP access logs
 *  This script makes part of a list of scripts to compare MySQL 5.0.26 with MyISAM tables versus MongoDB 2.2.0rc0.
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20120903
 */
set_include_path(get_include_path() . PATH_SEPARATOR . "classes");
require_once("MongoRandomElements.class.php");
 
$mre = new MongoRandomElements("mongodb", "mongodb", "localhost", "InternetAccessLog");
// First, we get the minimum value of the 10 highest visits per domain
$start = new MongoDate(strtotime("2012-06-15 00:00:00"));
$end = new MongoDate(strtotime("2012-06-30 23:59:59"));
$min_value  = $mre->getOne(array(
            array('$match' => array('datetime' => array( '$gt' => $start, '$lt' => $end ))),
            array('$group' => array('_id' => '$domain', 'visits' => array( '$sum' => 1 ))),
            array('$group' => array('_id' => '$visits')),
            array('$sort' => array('_id' => -1)),
            array('$limit' => 10),
            array('$sort' => array('_id' => 1)),
            array('$limit' => 1),
        ), "NonFTP_Access_log");

// Now, we obtain all the domains with at lest that value
$data = $mre->getResults(array(
            array('$match' => array('datetime' => array( '$gt' => $start, '$lt' => $end ))),
            array('$group' => array('_id' => '$domain', 'visits' => array( '$sum' => 1 ))),
            array('$match' => array('visits' => array( '$gte' => $min_value)))            
        ), "NonFTP_Access_log");

foreach($data as $doc)    {
    print_r($doc);
    }

?>

