<?php
/**
 *  Example data insertion in MongoDB
 *  In this test 70.000 random users, 70.000 random IPs and 1.300.000 domains are generated and saved in MongoDB.
 *  With this elements 90 million of non FTP log entries and 4,5 million of FTP entries (with raports by month and day) are created simulting the estimated volume data for 3 months
 *  Note that most of time the script is generating the random data!
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20121109
 *
 */

set_include_path(get_include_path() . PATH_SEPARATOR . "classes");
require_once("MongoRandomElements.class.php");

$mre = new MongoRandomElements();
#$mre->createUsers(70000);
#echo "Random users created\n";
#$mre->createIPs(70000);
#echo "Random IPs created\n";
#$mre->createDomains(1300000);
#echo "Random Domains created\n";


#// Example data for April
#$start = mktime(0,0,0,4,1,2012);
#$end = mktime(23,59,0,4,30,2012);
#for ($i = 0; $i < 30000000; $i++)	{
#	$log = $mre->getRandomNonFTPLogEntry($start, $end);
#    $mre->saveRandomNonFTPLogEntry($log);
#}
#for ($i = 0; $i < 1500000; $i++)	{
#	$log = $mre->getRandomFTPLogEntry($start, $end);
#    $mre->saveRandomFTPLogEntry($log);
#}
#echo "Example data for April created\n";
#
#
#// Example data for May
#$start = mktime(0,0,0,5,1,2012);
#$end = mktime(23,59,0,5,31,2012);
#for ($i = 0; $i < 30000000; $i++)	{
#	$log = $mre->getRandomNonFTPLogEntry($start, $end);
#    $mre->saveRandomNonFTPLogEntry($log);
#}
#for ($i = 0; $i < 1500000; $i++)	{
#	$log = $mre->getRandomFTPLogEntry($start, $end);
#    $mre->saveRandomFTPLogEntry($log);
#}
#echo "Example data for May created\n";
#
#
#// Example data for June
#$start = mktime(0,0,0,6,1,2012);
#$end = mktime(23,59,0,6,30,2012);
#for ($i = 0; $i < 30000000; $i++)	{
#	$log = $mre->getRandomNonFTPLogEntry($start, $end);
#    $mre->saveRandomNonFTPLogEntry($log);
#}
#for ($i = 0; $i < 1500000; $i++)	{
#	$log = $mre->getRandomFTPLogEntry($start, $end);
#    $mre->saveRandomFTPLogEntry($log);
#}
#echo "Example data for June created\n";


#// Example data for Jully
#$start = mktime(0,0,0,7,1,2012);
#$end = mktime(23,59,0,7,30,2012);
#for ($i = 0; $i < 9992602; $i++)	{
#	$log = $mre->getRandomNonFTPLogEntry($start, $end);
#    $mre->saveRandomNonFTPLogEntry($log, FALSE);
#}
#for ($i = 0; $i < 500000; $i++)	{
#	$log = $mre->getRandomFTPLogEntry($start, $end);
#    $mre->saveRandomFTPLogEntry($log, FALSE);
#}
#echo "Example data for Jully created\n";


// Index creation on data
echo "Creating indexes on log collections ... ";
$col = MongoRandomElements::NONFTPLOG_NAME;
$mre->getDB()->$col->ensureIndex(array('clientip' => 1));
$mre->getDB()->$col->ensureIndex(array('user' => 1));
$mre->getDB()->$col->ensureIndex(array('datetime' => 1));
$mre->getDB()->$col->ensureIndex(array('domain' => 1));
$col = MongoRandomElements::FTPLOG_NAME;
$mre->getDB()->$col->ensureIndex(array('clientip' => 1));
$mre->getDB()->$col->ensureIndex(array('user' => 1));
$mre->getDB()->$col->ensureIndex(array('datetime' => 1));
$mre->getDB()->$col->ensureIndex(array('domain' => 1));
echo "OK\n";
?>
