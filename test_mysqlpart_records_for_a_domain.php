<?php
/**
 *  Counting records by date test in MySQL
 *  This script counts the number of records for a domain. The idea is to force MySQL to search in more than one partition using a field which does not make part of the keys (in the case of a partitioned table by month).
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20130321
 */

set_include_path(get_include_path() . PATH_SEPARATOR . "classes");
require_once("MySQLRandomElements.class.php");

function microtime_float()  {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

$mre = new MySQLRandomElements();

$domain = "www.ugyi.pro";

$begin = microtime_float();
$n = $mre->getOne("select sum(size) from NonFTP_Access_log where domain = \"".$domain."\"");
$end = microtime_float();
$ms_spent = ($end-$begin)*1000;
printf("%d bytes transferred from domain %s, Time spent (ms): %d\n", $n, $domain, $ms_spent);

?>