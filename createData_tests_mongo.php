<?php
set_include_path(get_include_path() . PATH_SEPARATOR . "classes");
require_once("MongoRandomElements.class.php");

$mre = new MongoRandomElements();
#$mre->createUsers(70000);
#echo "Random users created\n";
#$mre->createIPs(70000);
#echo "Random IPs created\n";
#$mre->createDomains(1300000);
#echo "Random Domains created\n";

// Example data for June, Jully and August
$start = mktime(0,0,0,6,1,2012);
$end = mktime(23,59,0,6,30,2012);
for ($i = 0; $i < 10; $i++)	{
	$log = $mre->getRandomNonFTPLogEntry($start, $end);
    $mre->saveRandomNonFTPLogEntry($log);
}
$start = mktime(0,0,0,7,1,2012);
$end = mktime(23,59,0,6,31,2012);
for ($i = 0; $i < 10; $i++)	{
	$log = $mre->getRandomNonFTPLogEntry($start, $end);
    $mre->saveRandomNonFTPLogEntry($log);
}
$start = mktime(0,0,0,8,1,2012);
$end = mktime(23,59,0,8,31,2012);
for ($i = 0; $i < 10; $i++)	{
	$log = $mre->getRandomNonFTPLogEntry($start, $end);
    $mre->saveRandomNonFTPLogEntry($log);
}

echo "Example data created\n";


?>
