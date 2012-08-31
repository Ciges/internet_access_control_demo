<?php
/**
 *  Test 4:  MySQL read test. Search and show data for a random user.  
 *  This test is meant to be called via web (to make possible load tests with standard web load tools)
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20120829
 */
set_include_path(get_include_path() . PATH_SEPARATOR . "../classes");
require_once("MySQLRandomElements.class.php");
 
$mre = new MySQLRandomElements("mysqldb", "mysqldb", "localhost", "InternetAccessLog");

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

?>