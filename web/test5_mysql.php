<?php
/**
 *  Test 5:  MySQL write test. Write a random user  
 *  This test is meant to be called via web (to make possible load tests with standard web load tools)
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20120829
 */
set_include_path(get_include_path() . PATH_SEPARATOR . "../classes");
require_once("MySQLRandomElements.class.php");

$mre = new MySQLRandomElements("mysqldb", "mysqldb", "localhost", "loadtests");

// If the user is in the collection we get another one
$username = $mre->getRandomUser();
while ($mre->existUser($username, "fakeusers"))  {
    $username = $mre->getRandomUser();
}

$mre->addUser($username, "fakeusers");
echo("<p>User ".$username." added to the database</p>");

?>