<?php
/**
 *  Test 9.3:  MongoDB analyse query:  Gets the 10 users with most hits
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
require_once("MongoRandomElements.class.php");
 
$mre = new MongoRandomElements("mongodb", "mongodb", "localhost", "InternetAccessLog");

// First, we get the minimum value of the 10 highest visits per user
$min_value  = $mre->getOne(array(
    array('$group' => array('_id' => '$user', 'visits' => array( '$sum' => 1 ))),
    array('$group' => array('_id' => '$visits')),
    array('$sort' => array('_id' => -1)),
    array('$limit' => 10),
    array('$sort' => array('_id' => 1)),
    array('$limit' => 1),
    ), "NonFTP_Access_log");
        
// Now, we obtain all the users with at least that value
$data = $mre->getResults(array(
    array('$group' => array('_id' => '$user', 'visits' => array( '$sum' => 1 ))),
    array('$match' => array('visits' => array( '$gte' => $min_value)))            
    ), "NonFTP_Access_log");

foreach($data as $doc)    {
    print_r($doc);
    }
 
 
?>