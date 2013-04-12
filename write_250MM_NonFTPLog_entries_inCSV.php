<?php
/**
 * Saves 250 millions of Non FTP log entries in /users6/CIGES/NonFTP_Access_log_250MM.csv
 * Version:  20130410
 */

set_include_path(get_include_path() . PATH_SEPARATOR . "classes");
require_once("MySQLRandomElements.class.php");

$logentries = 250000000;
$csv_file = "/users6/CIGES/NonFTP_Access_log_250MM.csv";

echo "Loading data in RAM ...";
$mre = new MySQLRandomElements();
$mre->loadDataInRAM();
echo "Done\n";

function saveEntryToCSV($csv = NULL, $n) {
    global $mre;

    // Generate & write random log entries
    $start = mktime(0,0,0,1,1,2012);
    $end = mktime(23,59,0,12,31,2012);

    if (!file_exists($csv)) {
        $fh = $mre->createRandomNonFTPLogEntryCSV($csv);
    }
    else {
        $fh = fopen($csv, "a");
    }
    for ($i = 0; $i < $n; $i++)       {
        $log = $mre->getRandomNonFTPLogEntry($start, $end);
        $mre->saveRandomNonFTPLogEntryToCSV($log, $csv, $fh);
    }
    fclose($fh);
}

# Saves in blocks of 100.000 entries
$entries_block = 100000;
$nblocks = $logentries / $entries_block;

for ($i = 0; $i < $nblocks; $i++) {
    saveEntryToCSV($csv_file, $entries_block);
    printf ("%d non ftp log entries saved in %s\n", ($i+1)*$entries_block, $csv_file);
}



?>
