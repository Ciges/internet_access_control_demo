<?php
/**
 *  Test 9.5:  MongoDB analyse query:  Gets the 5 most visited domains by month
 *  First the 10 highest numeric values for hit will be get, and then all the users for this values will be retourned.
 *  
 *  This queries are done over the InternetAccessLogs database directly, which contains example data for 90 millions of records for Non FTP access logs and 4,5 millions of record for FTP access logs
 *  This script makes part of a list of scripts to compare MySQL 5.0.26 with MyISAM tables versus MongoDB 2.2.0rc0.
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20120831
 */
set_include_path(get_include_path() . PATH_SEPARATOR . "classes");
require_once("MongoRandomElements.class.php");
 
$mre = new MongoRandomElements("mongodb", "mongodb", "localhost", "InternetAccessLog");
// Search the distinct months stored 
$months = $mre->getResults(array(
    array('$project' => array('_id' => 0, 'month' => array('$month' => '$datetime'))),
    array('$group' => array('_id' => '$month')),
    array('$sort' => array('_id' => 1))
    ), "NonFTP_Access_log");
    
foreach($months as $doc) {
    $n = $doc[_id];
    // First, we get the minimum value of the 5 highest visits per domain for each month
    $min_value  = $mre->getOne(array(
        array('$project' => array('_id' => 0, 'month' => array('$month' => '$datetime'), 'domain' => 1)),
        array('$match' => array('month' => $n)),
        array('$group' => array('_id' => '$domain', 'visits' => array( '$sum' => 1 ))),
        array('$group' => array('_id' => '$visits')),
        array('$sort' => array('_id' => -1)),
        array('$limit' => 5),
        array('$sort' => array('_id' => 1)),
        array('$limit' => 1),
        ), "NonFTP_Access_log");
        
    // Now, we obtain all the domains of this month with at lest that value
    $data = $mre->getResults(array(
        array('$project' => array('_id' => 0, 'month' => array('$month' => '$datetime'), 'domain' => 1)),
        array('$match' => array('month' => $n)),
        array('$group' => array('_id' => array('month' => 1, 'domain' => 1), 'visits' => array('$sum' => 1))),
        array('$match' => array('visits' => array( '$gte' => $min_value))),
        ), "NonFTP_Access_log");
    }

foreach($data as $doc)  {
    print_r($doc);
    }

?>