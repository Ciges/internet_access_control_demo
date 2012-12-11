<?php
/**
 *  Test 7:  Mongo read test. Search and show data for a random user.  
 *  This test is meant to be called via web (to make possible load tests with standard web load tools)
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20121129
 */
set_include_path(get_include_path() . PATH_SEPARATOR . "../classes");
require_once("MongoRandomElements.class.php");
 
$mre = new MongoRandomElements("mongodb", "mongodb", "localhost", "InternetAccessLog");

// We get a random date and time between 1st Avril and 31 Jully 2012
$repeat = true; // If no records is found for the random date we will repeat the operation
while ($repeat) {
    $random_date = $mre->getRandomDate("2012-04-01 00:00:00", "2012-07-31 23:59:59");
    echo "<p>Log records for date & time: ".$random_date."</p>";
    $col = MongoRandomElements::NONFTPLOG_NAME;
    $db = $mre->getDB();
    $cursor = $db->$col->find(array("datetime" => new MongoDate(strtotime($random_date))))->limit(10);
    
    if ($cursor->count() >= 0)   {
        $repeat = false;
        echo "<table style=\"font-size: small;\" border=\"1\"><tr><th>Client IP</th><th>User</th><th>Method</th><th>Protocol</th><th>Domain</th><th>URI</th><th>Return code</th><th>Size</th></tr>";
        while ($doc = $cursor->getNext()) {
            printf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>", $doc['clientip'], $doc['user'], $doc['method'], $doc['protocol'], $doc['domain'], $doc['uri'], $doc['return_code'], $doc['size']);
        }
        echo "</table>";
    }
}

?>