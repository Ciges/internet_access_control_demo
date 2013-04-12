<?php
/**
 *  Counting records by date test in MySQL
 *  This script counts the number of records in Non FTP log entries for each month.
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

function show_2012records_by_month($month)  {
    global $mre;

    switch($month)  {
        case 1:
            $start = '2012-01-01 00:00:00';
            $end = '2012-01-31 23:59:00';
            break;
        case 2:
            $start = '2012-02-01 00:00:00';
            $end = '2012-02-29 23:59:00';
            break;
        case 3:
            $start = '2012-03-01 00:00:00';
            $end = '2012-03-31 23:59:00';
            break;
        case 4:
            $start = '2012-04-01 00:00:00';
            $end = '2012-04-30 23:59:00';
            break;
        case 5:
            $start = '2012-05-01 00:00:00';
            $end = '2012-05-31 23:59:00';
            break;
        case 6:
            $start = '2012-06-01 00:00:00';
            $end = '2012-06-30 23:59:00';
            break;
        case 7:
            $start = '2012-07-01 00:00:00';
            $end = '2012-07-31 23:59:00';
            break;
        case 8:
            $start = '2012-08-01 00:00:00';
            $end = '2012-08-31 23:59:00';
            break;
        case 9:
            $start = '2012-09-01 00:00:00';
            $end = '2012-09-30 23:59:00';
            break;
        case 10:
            $start = '2012-10-01 00:00:00';
            $end = '2012-10-30 23:59:00';
            break;
        case 11:
            $start = '2012-11-01 00:00:00';
            $end = '2012-11-30 23:59:00';
            break;
        case 12:
            $start = '2012-12-01 00:00:00';
            $end = '2012-12-31 23:59:00';
            break;
    }

    $begin = microtime_float();
    $query = "select count(*) from NonFTP_Access_log where datetime between '".$start."' and '".$end. "'";
    $n = $mre->getOne($query);
    $end = microtime_float();
    $ms_spent = ($end-$begin)*1000;
    printf("Records for %s, Time spent (ms): %d, number %d\n", date("F", strtotime($start)), $ms_spent, $n);
    return $ms_spent;
}

$mre = new MySQLRandomElements();
$times = array();
for ($month = 1; $month <= 12; $month++)  {
    array_push($times, show_2012records_by_month($month));
}

printf("Average time: %d\n", array_sum($times)/12);

?>