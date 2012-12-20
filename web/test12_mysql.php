<?php
/**
 *  Test 12:  MySQL read test. Search and show data for a random domain.  
 *  This test is meant to be called via web (to make possible load tests with standard web load tools)
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20121220
 */
set_include_path(get_include_path() . PATH_SEPARATOR . "../classes");
require_once("MySQLRandomElements.class.php");
 
$mre = new MySQLRandomElements("mysqldb", "mysqldb", "localhost", "InternetAccessLog");

// Number of domains
$n = $mre->getDomainNumber();

$data = null;
// Get the data for a random domain in April, if the domain does not exists we make a new search for another one
while (is_null($data))  {
    $domain = $mre->getDomainFromID(rand(0,$n));
    $data = $mre->getDomainCollectedData($domain, 2012, 4);
    }

echo "<p>Data for April for random domain ".$domain.":";
echo "
<ul>
    <li> Hits:  ".$data["nb"]."
    <li> Traffic volume:  ".$data["volume"]."
</ul>";

?>