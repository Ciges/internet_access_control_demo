<?php
/**
 *  Test 6:  MySQL read/write test. This scripts makes one of two actions:
 *  - Search and show data for a random user (read test)
 *  - Write a new random user in the database (write test)
 *  The read test is made 80% of times, the write one the 20%.
 *  This test is meant to be called via web (to make possible load tests with standard web load tools)
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20120829
 */
function ver($var)   {
    echo "<pre>";
    print_r($var);
    echo "</pre>";
}

set_include_path(get_include_path() . PATH_SEPARATOR . "../classes");
require_once("MySQLRandomElements.class.php");
 
$mre = new MySQLRandomElements("mysqldb", "mysqldb", "localhost", "InternetAccessLog");
$db = $mre->getDB();

if (rand(0,9) < 8)	{
    // Read test

    // Number of users
    $n = $mre->getUserNumber();

    $data = null;
    // Get the data for a random user in June, if the users does not exists we make a new search for another one
    while (is_null($data))  {
        $username = $mre->getUserFromID(rand(0,$n));
        $data = $mre->getUserCollectedData($username, 2012, 6);
        }

    echo "<p>Data for June for random user ".$username.":";
    echo "<ul>";
        echo "<li> Hits:  ".$data["nb"];
        echo "<li> Volume:  ".$data["volume"];
    echo "</ul>";
}
else    {
    // Write test

    // If the user is in the collection we get another one
    $username = $mre->getRandomUser();
    while ($mre->existUser($username, "fakeusers"))  {
        $username = $mre->getRandomUser();
    }

    $mre->addUser($username, "fakeusers");
    echo("<p>User ".$username." added to the database</p>");
    
}

?>