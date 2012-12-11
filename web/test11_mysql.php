<?php
/**
 *  Test 11:  MySQL read/write test. MongoDB read/write test on 100 millions records collection. This script makes one of two read or write actions. The read test is made 80% of times, the write one the 20%.
 *  - Search and show log records for a random domain (read test)
 *  - Write a (predefined) log record in Non FTP Logs collection (write test)
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20121128
 */

set_include_path(get_include_path() . PATH_SEPARATOR . "../classes");
require_once("MySQLRandomElements.class.php");


if (rand(0,9) < 8)	{
    // Read test

    $mre = new MySQLRandomElements("mysqldb", "mysqldb", "localhost", "InternetAccessLog");
    // Get a random domain from database
    $domain = $mre->getDomainFromID(rand(0,$mre->getDomainNumber()));

    // Show records for domain got
    $repeat = true; // If no records is found for the random date we will repeat the operation
    while ($repeat) {
        echo "<p>Log records for domain: ".$domain."</p>";
        $query = "select * from NonFTP_Access_log where domain=\"".$domain."\"";
        if ($data = $mre->getResults($query))   {
            $repeat = false;
            echo "<table style=\"font-size: small;\" border=\"1\"><tr><th>Client IP</th><th>User</th><th>Method</th><th>Protocol</th><th>URI</th><th>Return code</th><th>Size</th></tr>";
            while ($row = $data->fetch_assoc()) {
                printf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>", $row['clientip'], $row['user'], $row['method'], $row['protocol'], $row['uri'], $row['return_code'], $row['size']);
            }
            echo "</table>";
        }
    }

}
else    {
    // Write test
    
    $mre = new MySQLRandomElements("mysqldb", "mysqldb", "localhost", "loadtests");

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
