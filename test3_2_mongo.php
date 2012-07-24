<?php
/**
 *  Test 3.1:  MongoDB insertion of 5 million of simulated http (and tunnel) log entries
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20120724
 *
 */

set_include_path(get_include_path() . PATH_SEPARATOR . "classes");
require_once("MongoRandomElements.class.php");

$mre = new MongoRandomElements();
// All the entries will be generated in April
$start = mktime(0,0,0,5,1,2012);
$end = mktime(23,59,0,5,31,2012);

for ($i = 0; $i < 5000000; $i++)	{
	$log = $mre->getRandomNonFTPLogEntry($start, $end);
    $mre->saveRandomNonFTPLogEntry($log, FALSE);
}
 
?>
