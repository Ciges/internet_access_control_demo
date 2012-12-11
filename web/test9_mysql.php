<?php
/**
 *  Test 9:  MySQL read/write test. MongoDB read/write test on 100 millions records collection. This script makes one of two read or write actions. The read test is made 80% of times, the write one the 20%.
 *  - Search and show logs records starting for a random date (limited to 10) (read test)
 *  - Write a (predefined) log record in Non FTP Logs collectio
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20121112
 */

set_include_path(get_include_path() . PATH_SEPARATOR . "../classes");
require_once("MySQLRandomElements.class.php");


if (rand(0,9) < 8)	{
    // Read test

    $mre = new MySQLRandomElements("mysqldb", "mysqldb", "localhost", "InternetAccessLog");

    // We get a random date and time between 1st Avril and 31 Jully 2012
    $repeat = true; // If no records is found for the random date we will repeat the operation
    while ($repeat) {
        $random_date = $mre->getRandomDate("2012-04-01 00:00:00", "2012-07-31 23:59:59");
        echo "<p>Log records for date & time: ".$random_date."</p>";
        $query = "select * from NonFTP_Access_log where datetime=\"".$random_date."\" limit 10;";
        if ($data = $mre->getResults($query))   {
            $repeat = false;
            echo "<table style=\"font-size: small;\" border=\"1\"><tr><th>Client IP</th><th>User</th><th>Method</th><th>Protocol</th><th>Domain</th><th>URI</th><th>Return code</th><th>Size</th></tr>";
            while ($row = $data->fetch_assoc()) {
                printf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>", $row['clientip'], $row['user'], $row['method'], $row['protocol'], $row['domain'], $row['uri'], $row['return_code'], $row['size']);
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
