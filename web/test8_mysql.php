<?php
/**
 *  Test 8:  MySQL write test on 100 millions records collection. Write a (predefined) log record in Non FTP Logs collection;
 *  This test is meant to be called via web (to make possible load tests with standard web load tools)
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20121112
 */
set_include_path(get_include_path() . PATH_SEPARATOR . "../classes");
require_once("MySQLRandomElements.class.php");

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

?>
