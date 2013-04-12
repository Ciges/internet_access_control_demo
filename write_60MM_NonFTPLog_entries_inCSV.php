<?php
set_include_path(get_include_path() . PATH_SEPARATOR . "classes");
require_once("MySQLRandomElements.class.php");

$logentries = 60000000;

echo "Loading data in RAM ...";
$mre = new MySQLRandomElements();
$mre->loadDataInRAM();
echo "Done\n";

// // Export random elements from database to CSV files
// $mre->exportUsersToCSV();
// echo "Users list exported to CSV/Users.csv\n";
// $mre->exportIPsToCSV();
// echo "IPs list exported to CSV/IPs.csv\n";
// $mre->exportDomainsToCSV();
// echo "Domains list exported to CSV/Domains.csv\n";
// $mre->exportURIsToCSV();
// echo "URIs list exported to CSV/URIs.csv\n";

// Generate & write random log entires
$start = mktime(0,0,0,1,1,2012);
$end = mktime(23,59,0,12,31,2012);

$fh = $mre->createRandomNonFTPLogEntryCSV();
for ($i = 0; $i < $logentries; $i++)       {
    $log = $mre->getRandomNonFTPLogEntry($start, $end);
    $mre->saveRandomNonFTPLogEntryToCSV($log, NULL, $fh);
}
fclose($fh);
printf ("%d non ftp log entries saved in %s\n", $logentries, "CSV/NonFTP_Access_log.csv");

?>
