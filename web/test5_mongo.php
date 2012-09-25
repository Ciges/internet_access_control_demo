<?php
/**
 *  Test 5:  MongoDB write test. Write a random user  
 *  This test is meant to be called via web (to make possible load tests with standard web load tools)
 *
 *  @author Jos� Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20120914
 */
set_include_path(get_include_path() . PATH_SEPARATOR . "../classes");
require_once("MongoRandomElements.class.php");

$mre = new MongoRandomElements("mongodb", "mongodb", "localhost", "loadtests");

$username = $mre->getRandomUser();
if ($mre->addFakeUser($username, "fakeusers"))  {
    echo("<p>User ".$username." added to the database</p>");
}
else    {
    header('HTTP/1.1 500 Internal Server Error');
}

?>
