<?php
/**
 *  Test 1.3:  MySQL insertion WITHOUT indexes and WITHOUT verifying if the user name is already in the table
 *  In this test 1.300.000 random domains are generated and saved in MySQL
 *  Note that most of time the script is generating the random data!
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20120720
 *
 */

set_include_path(get_include_path() . PATH_SEPARATOR . "classes");
require_once("MySQLRandomElements.class.php");

$mre = new MySQLRandomElements();
$mre->createDomains(1300000, FALSE, FALSE);


 
?>
