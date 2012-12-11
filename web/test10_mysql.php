<?php
/**
 *  Test 10:  MySQL read test. Search and show log records for a random domain.  
 *  This test is meant to be called via web (to make possible load tests with standard web load tools)
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20121128
 */
set_include_path(get_include_path() . PATH_SEPARATOR . "../classes");
require_once("MySQLRandomElements.class.php");
 
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

?>