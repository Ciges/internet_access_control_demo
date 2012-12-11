<?php
/**
 *  Test 9:  MySQL read/write test. MySQL read/write test on 100 millions records collection. This script makes one of two read or write actions. The read test is made 80% of times, the write one the 20%.
 *  - Search and show logs records starting for a random date (limited to 10) (read test)
 *  - Write a (predefined) log record in Non FTP Logs collectio
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20121129
 */

set_include_path(get_include_path() . PATH_SEPARATOR . "../classes");
require_once("MongoRandomElements.class.php");


if (rand(0,9) < 8)	{
    // Read test

    $mre = new MongoRandomElements("mongodb", "mongodb", "localhost", "InternetAccessLog");

    // We get a random date and time between 1st Avril and 31 Jully 2012
    $repeat = true; // If no records is found for the random date we will repeat the operation
    while ($repeat) {
        $random_date = $mre->getRandomDate("2012-04-01 00:00:00", "2012-07-31 23:59:59");
        echo "<p>Log records for date & time: ".$random_date."</p>";
        $col = MongoRandomElements::NONFTPLOG_NAME;
        $db = $mre->getDB();
        $cursor = $db->$col->find(array("datetime" => new MongoDate(strtotime($random_date))));
        
        $query = "select * from NonFTP_Access_log where datetime=\"".$random_date."\" limit 10;";
        if ($cursor->count() >= 0)   {
            $repeat = false;
            echo "<table style=\"font-size: small;\" border=\"1\"><tr><th>Client IP</th><th>User</th><th>Method</th><th>Protocol</th><th>Domain</th><th>URI</th><th>Return code</th><th>Size</th></tr>";
            while ($doc = $cursor->getNext()) {
                printf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>", $doc['clientip'], $doc['user'], $doc['method'], $doc['protocol'], $doc['domain'], $doc['uri'], $doc['return_code'], $doc['size']);
            }
            echo "</table>";
        }
    }

}
else    {
    // Write test
    
    $mre = new MongoRandomElements("mongodb", "mongodb", "localhost", "loadtests");

    $db = $mre->getDB();
    $col = MongoRandomElements::NONFTPLOG_NAME;
            
    $log = array(
        "clientip" => "133.62.242.56",
        "user" => "E198869",
        "datetime" => strtotime("2012-04-30 18:35:45"),
        "method" => "GET",
        "protocol" => "http",
        "domain" => "www.barrapunto.com",
        "uri" => "article.pl?sid=12/11/09/1420215",
        "return_code" => 200, 
        "size" => 41805
    );

    $mre->saveRandomNonFTPLogEntry($log, FALSE);
    echo "<p>Saved log data: </p>";
    echo "<pre>";
    print_r($log);
    echo "</pre>";

}

?>
