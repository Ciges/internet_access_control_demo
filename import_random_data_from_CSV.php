<?php
set_include_path(get_include_path() . PATH_SEPARATOR . "classes");
require_once("MySQLRandomElements.class.php");

// Import random elements from CSV to databasefiles
$mre = new MySQLRandomElements();
$mre->importUsersFromCSV();
echo "Users list imported from CSV/Users.csv\n";
$mre->importIPsFromCSV();
echo "IPs list imported from CSV/IPs.csv\n";
$mre->importDomainsFromCSV();
echo "Domains list imported from CSV/Domains.csv\n";
$mre->importURIsFromCSV();
echo "URIs list imported from CSV/URIs.csv\n";


?>
