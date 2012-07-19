<?php
/**
 *  Test 1.3:  MongoDB insertion WITHOUT indexes and WITHOUT verifying if the user name is already in the table
 *  In this test 1.300.000 random domains are generated and saved in MongoDB
 *  Note that most of time the script is generating the random data!
 *
 *  @author Jos� Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20120719
 *
 */

set_include_path(get_include_path() . PATH_SEPARATOR . "classes");
require_once("MongoRandomElements.class.php");

$mre = new MongoRandomElements();
$mre->createDomains(1300000, FALSE, FALSE);


 
?>