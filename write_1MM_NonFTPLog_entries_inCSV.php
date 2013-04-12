<?php
/**
 *  Record insertion test in MySQL
 *  This script writes 1 million of Non FTP log entries all corresponding to the las week of the year in a MySQL table in CSV format at "CSV/NonFTP_Access_log_1million.csv" file
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20130327
 *
 */
set_include_path(get_include_path() . PATH_SEPARATOR . "classes");
require_once("MySQLRandomElements.class.php");

$filename = "CSV/NonFTP_Access_log_1million.csv";
$logentries = 1000000;

echo "Loading data in RAM ...";
$mre = new MySQLRandomElements();
$mre->loadDataInRAM();
echo "Done\n";

// Generate & write random log entires
$start = mktime(0,0,0,12,24,2012);
$end = mktime(23,59,0,12,31,2012);

$fh = $mre->createRandomNonFTPLogEntryCSV($filename);
for ($i = 0; $i < $logentries; $i++)       {
    $log = $mre->getRandomNonFTPLogEntry($start, $end);
    $mre->saveRandomNonFTPLogEntryToCSV($log, $filename, $fh);
}
fclose($fh);
printf ("%d non ftp log entries saved in %s\n", $logentries, $filename);

?>
