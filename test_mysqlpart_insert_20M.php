<?php
/**
 *  Record insertion test in MySQL
 *  This script read Non FTP log entires stores in CSV format in CSV/NonFTP_Access_log.csv and stores in in MySQL table.
 *  Each 100 records saved actual time and miliseconds spent are shown in standard exit (so they could be stored in a CSV file to represent them graphically with an statistic tool like R)
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20130318
 *
 */
set_include_path(get_include_path() . PATH_SEPARATOR . "classes");
require_once("MySQLRandomElements.class.php");

$logentries = 20000000; # Number of log entries to read from CSV file
$entriestoread_eachtime = 100; # Number of entries to read before showing time spent
$filename = getcwd()."/CSV/NonFTP_Access_log.csv";


function microtime_float()  {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

$mre = new MySQLRandomElements();

($fh = fopen($filename, "r")) || die("Not possible to open ".$filename." file");
fgets($fh) || die("Not possible to read from  ".$filename." file");

printf("timeStamp,elapsed\n");
// Read from file the number of lines in $logentires in blocks of $entriestoread_eachtime
$i = 0;
while ($i < $logentries)  {
    $begin = microtime_float();
    for ($j = 1; $j <= $entriestoread_eachtime;  $j++)  {
        if (($line = fgets($fh)) !== FALSE)  {
            $mre->saveRandomNonFTPLogEntry_fromCSV($line, FALSE);
            $i++;
        }
        else {
            break 2;
        }
    }
    $end = microtime_float();
    $ms_spent = ($end-$begin)*1000;
    printf("%s,%d\n", strftime("%T"), $ms_spent);
}
fclose($fh);

?>
