<?php
/**
 *  MySQL write test. Write a (predefined) log record in Non FTP Logs collection;
 *  This test is meant to be called via web (to make possible load tests with standard web load tools)
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20130404
 */
set_include_path(get_include_path() . PATH_SEPARATOR . "../../classes");
require_once("MySQLRandomElements.class.php");

// Generate & write random log entires
$start = mktime(0,0,0,12,24,2012);
$end = mktime(23,59,0,12,31,2012);
$datetime = mt_rand($start, $end);

$log = array(
    "clientip" => "133.62.242.56",
    "user" => "E198869",
    "datetime" => $datetime,
    "method" => "GET",
    "protocol" => "http",
    "domain" => "www.barrapunto.com",
    "uri" => "article.pl?sid=12/11/09/1420215",
    "return_code" => 200,
    "size" => 41805
);


$mre = new MySQLRandomElements();
$mre->saveRandomNonFTPLogEntry($log, FALSE);
echo "<p>Saved log data (datetime ".date("Y-m-d H:i:s",$datetime)."): </p>";
echo "<pre>";
print_r($log);
echo "</pre>";

?>
