<?php
/**
 *  MySQLRandomElements.class.php
 *  File with the class used to generate random elements and save then in MySQL (users, URL's and IP's)
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page {@link http://www.ciges.net}
 *  @license http://www.gnu.org/copyleft/gpl.html GNU GPLv3
 *  @version 20130314
 *
 *  @package InternetAccessLog
 *  @filesource
 */
require_once("RandomElements.class.php");
/**
 *  This class is used to generate random elements (users, IP's and URL's) and save them into MySQL
 *  With this elements created we can simulate non FTP and FTP log entries (in our demo the acces by FTP are stored in a separate collection)
 */
 class MySQLRandomElements extends RandomElements	{

    /**
     * Default names for random data collections
     */
    const DATA_RNDUSERSC_NAME = "DATA_Random_UsersList";
    const DATA_RNDIPSC_NAME = "DATA_Random_IPsList";
    const DATA_RNDDOMAINSC_NAME = "DATA_Random_DomainsList";
    const DATA_RNDURISC_NAME = "DATA_Random_URIsList";

    const NONFTPLOG_NAME = "NonFTP_Access_log";
    const FTPLOG_NAME = "FTP_Access_log";

    /**
     *  Default prefixes for monthly reports
     */
    const USERS_REPORT_PREFIX = "Users_Monthly_Report_";
    const DOMAINS_REPORT_PREFIX = "Domains_Monthly_Report_";

    /**
     * Constants for default connection values
     */
    const DEFAULT_USER = "mysqldb";
    const DEFAULT_PASSWORD = "mysqldb";
    const DEFAULT_HOST = "localhost";
    const DEFAULT_DB = "InternetAccessLog";

    /**
     * Connection to the database
     * @access private
     * @var Mongo
     */
    private $db_conn;
    /**
     * Database name
     * @access private
     * @var Mongo
     */
    private $db_databasename;

    /**
     * Arrays to load random data in memory
     * @access private
     * @var array;
     */
    private $rnd_users;
    private $rnd_ips;
    private $rnd_domains;
    private $rnd_uris;

    /**
     * Number of element of each created collection in MySQL (for cache purposes)
     * @access private
     * @var string
     */
    private $rnd_users_number;
    private $rnd_ips_number;
    private $rnd_domains_number;
    private $rnd_uris_number;

    /**
     * Gets the connection to MySQL
     * @returns mysqli
     */
    public function getDB()    {
        return $this->db_conn;
    }

    /**
     *  Sends a query to the database and returns the results. If no rows are got null is returned
     *  @param string query
     *  @return mixed
     *  @access public
     */
    public function getResults($query)    {
        if ($results = $this->getDB()->query($query))	{
            if ($results->num_rows > 0) {
                return $results;
            }
            else    {
                return null;
            }
        }
        else	{
            die ("Error sending the query '".$query."' to MySQL: ".$this->db_conn->error."\n");
        }
    }

    /**
     *  Sends a query to the database and returns the first row as an associative array. If no rows are got null is returned
     *  @param string query
     *  @return array
     *  @access public
     */
    public function getRow($query)    {
        $results = $this->getResults($query);
        return $results->fetch_assoc();
    }

    /**
     *  Sends a query to the database and returns the first field of the first row. If no rows are got null is returned
     *  @param string query
     *  @return array
     *  @access public
     */
    public function getOne($query)    {
        $results = $this->getResults($query);
        $row = $results->fetch_row();
        return $row[0];
    }

    /**
     *  This function says if a table exists in MySQL
     *  @param string tablename
     *  @returns boolean
     *  @access private
     */
    private function tableExists($tablename)	{
        $query = "show table status where Name=\"".$tablename."\"";
        if ($result = $this->db_conn->query($query))	{
            return $result->num_rows > 0;
            }
            else	{
                    die ("Error sending the query '".$query."' to MySQL: ".$this->db_conn->error."\n");
            }
    }

    /**
     *  This function deletes a table if it exists in MySQL. If the table does not exists returns false (and does nothing)
     *  @param string tablename
     *  @return boolean
     *  @access public
     */
    public function dropTable($tablename)	{
        if ($this->tableExists($tablename))  {
            if ($this->db_conn->query("drop table if exists ".$tablename)) {
                return true;
                }
            else    {
                die ("Error sending the query '".$query."' to MySQL: ".$this->db_conn->error."\n");
                }
            }
        else    {
            return false;
        }
    }

    /**
     *  This function deletes the table used for saving NonFTP logs. If the table does not exists returns false (and does nothing)
     *  @param string tablename
     *  @return boolean
     *  @access public
     */
    public function dropTableNonFTPLogEntry()	{
        $this->dropTable(self::NONFTPLOG_NAME);
	}

    /**
     * Sends a query to the database and stops the script if it is no succesfull
     * @returns boolean
     * @access private
     */
    private function sendQuery($query)  {
        $this->db_conn->query($query) ||
            die ("Error sending the query '".$query."' to MySQL: ".$this->db_conn->error."\n");
    }

    /**
     *  This function returns the number of records for the table passed as parameter.  If the table does not exists returns 0.
     *  @param string tablename
     *  @returns integer
     *  @access private
     */
    private function recordNumber($tablename)	{
        $query = "show table status where Name=\"".$tablename."\"";
        if ($result = $this->db_conn->query($query))	{
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return (int) $row["Rows"];
        }
        else    {
            return 0;
        }
            }
            else	{
                    die ("Error sending the query '".$query."' to MySQL: ".$this->db_conn->error."\n");
            }
    }

    /**
    *  This function queries the database to return the users number (records in Random_UsersList)
    *  @return integer
    *  @access public
    */
    public function getUserNumber() {
        $this->rnd_users_number = $this->recordNumber(self::RNDUSERSC_NAME);
        return $this->rnd_users_number;
    }

    /**
     *  This function returns the username searching by the id. If the user does not exist null is returner
     *  @param integer $userid
     *  @return string $username
     *  @access public
     */
    public function getUserFromID($userid) {
        if ($userid > count($this->$rnd_users_number))  {
            return null;
        }
        return $this->$rnd_users[$userid];
    }

    /**
    *  This function queries the database to return the domains number (records in Random_DomainsList)
    *  @return integer
    *  @access public
    */
    public function getDomainNumber() {
        $this->rnd_domains_number = $this->recordNumber(self::RNDDOMAINSC_NAME);
        return $this->rnd_domains_number;
    }

    /**
     *  This function returns the domain searching by the id. If the domain does not exist null is returner
     *  @param integer $id
     *  @return string $domain
     *  @access public
     */
    public function getDomainFromID($id) {
        if ($id > count($this->$rnd_domains_number))  {
            return null;
        }
        return $this->$rnd_domains[$id];
    }

    /**
     *  This function returns the user data from the raports for a year and month specified. If there is no data returns null
     *  @param string $username
     *  @param integer $year
     *  @param integer $month   Number from 1 to 12
     *  @return array
     *  @access public
     */
    public function getUserCollectedData($username, $year, $month)  {
        $col = self::USERS_REPORT_PREFIX.$year.sprintf("%02d", $month);
        $query = "select * from ".$col." where user=\"".$username."\"";
        if ($result = $this->db_conn->query($query))   {
            if ($result->num_rows > 0)  {
                $row = $result->fetch_array();
                return $row;
            }
            else    {
                return null;
            }
        }
        else    {
            die ("Error sending the query '".$query."' to MySQL");
        }
    }

    /**
     *  This function returns the domain data from the reports for a year and month specified. If there is no data returns null
     *  @param string $domainname
     *  @param integer $year
     *  @param integer $month   Number from 1 to 12
     *  @return array
     *  @access public
     */
    public function getDomainCollectedData($domainname, $year, $month)  {
        $col = self::DOMAINS_REPORT_PREFIX.$year.sprintf("%02d", $month);
        $query = "select * from ".$col." where domain=\"".$username."\"";
        if ($result = $this->db_conn->query($query))   {
            if ($result->num_rows > 0)  {
                $row = $result->fetch_array();
                return $row;
            }
            else    {
                return null;
            }
        }
        else    {
            die ("Error sending the query '".$query."' to MySQL");
        }
    }

    /**
     *  Helper function to create Users table in database
     *  @param string $tablename
     *  @param boolean $useindex    Sets if a unique index for user name must be created
     *  @access private
     */
    private function createUsersTable($tablename = self::DATA_RNDUSERSC_NAME, $use_index = true) {
        if (!$this->tableExists($tablename)) {
            if ($use_index) {
                $query = "CREATE TABLE ".$tablename." (
                id INT NOT NULL PRIMARY KEY,
                user CHAR(7),
                unique index user_index (user)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
            }
            else {
                $query = "CREATE TABLE ".$tablename." (
                id INT NOT NULL PRIMARY KEY,
                user CHAR(7)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
            }
            $this->db_conn->query($query) ||
				die ("Error sending the query '".$query."' to MySQL: ".$this->db_conn->error."\n");
        }
    }

    /**
     *  This function add a user to the table passed as second argument. If not collection done then the user will be added to Random_UsersList.
     *  This function is coded for load tests, not for real use. The id is autonumeric
     *  Returns true if the user has been succesfull added, false if not
     *  @param string $username
     *  @param string $tablename
     *  @access public
     *  @return boolean
     */
    public function addFakeUser($username, $tablename = self::DATA_RNDUSERSC_NAME)   {
        // Table creation if it does not exists
        if (!$this->tableExists($tablename))    {
            $query = "CREATE TABLE ".$tablename." (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                user CHAR(7)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
            $this->db_conn->query($query) ||
				die ("Error sending the query '".$query."' to MySQL: ".$this->db_conn->error."\n");
        }

        $query = "insert into ".$tablename." (user) values (\"".$username."\")";
		if($this->db_conn->query($query))   {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     *  This function verifies if the user exists in the collection passed as second argument. If not collection done then the user will be added to Random_UsersList.
     *  @param string $username
     *  @param string $tablename
     *  @return boolean
     *  @access public
     */
    public function existUser($username, $tablename = self::RNDUSERSC_NAME)    {
        if ($this->tableExists($tablename)) {
            $query = "select id from ".$tablename." where user=\"".$username."\"";
            $results = $this->db_conn->query($query) ||
                die ("Error sending the query '".$query."' to MySQL: ".$this->mysrnd_con->error."\n");
            return $results->num_rows > 0;
        }
        else    {
            return false;
        }
    }

    /**
     *  Constructor. For creating an instance we need to pass all the parameters for the MongoDB database where the data will be stored (user, password, host & database name).
     *  <ul>
     *  <li>The default user and password will be mysqldb
     *  <li>The default host will be localhost
     *  <li>The default database name will be InternetAccessLog
     *  </ul>
     * @param string $user
     * @param string $password
     * @param string $host
     * @param string $database
	*/
    function __construct($user = self::DEFAULT_USER, $password = self::DEFAULT_PASSWORD, $host = self::DEFAULT_HOST, $database = self::DEFAULT_DB)	{
        // Open a connection to MySQL
        try {
            $this->db_conn = new mysqli($host, $user, $password, $database);
            $this->db_databasename = $database;
        }
        catch (Exception $e) {
            die("Connection MySQL impossible: (".$e->getCode().") ".$e->getMessage()."\n");
        }

        // Stores the number of elements of each stored random elements collection
        $this->rnd_users_number = $this->recordNumber(self::DATA_RNDUSERSC_NAME);
        $this->rnd_ips_number = $this->recordNumber(self::DATA_RNDIPSC_NAME);
        $this->rnd_domains_number = $this->recordNumber(self::DATA_RNDDOMAINSC_NAME);
        $this->rnd_uris_number = $this->recordNumber(self::DATA_RNDURISC_NAME);
        // Load the data in RAM
        $this->loadDataInRAM();
    }

    /**
     *  Destructor. Close the open connection to MySQL database
 	 */
    function __destruct()	{
		$this->db_conn->close();
	}

    /**
     *  Save random users in MySQL.
     *  The parameters are the number of users two create and to booleans: if we want an unique index to be created for the user name (default is TRUE) and if we want that the user name is unique (default TRUE).
     *  If the user name is going to be unique the existence of the name is verified with a query before inserting a new one.
     *  The id will be autonumeric (1, 2, 3 ....)
	 *  @param integer $number
     *  @param boolean $use_index
     *  @param boolean $dont_repeat
     */
    function createUsers($number, $use_index = TRUE, $dont_repeat = TRUE)	{
        $id = $this->rnd_users_number + 1;   // Autonumeric

        // Table creation if it does not exists
        $this->createUsersTable(self::DATA_RNDUSERSC_NAME, $use_index);

        $i = 1;
        while ($i <= $number)	{
            $user = $this->getRandomUser();
            // We verify if the user is in the collection only if it is needed
            $insert = TRUE;
            if ($dont_repeat) {
                $query = "select id from ".self::DATA_RNDUSERSC_NAME." where user=\"".$user."\"";
                if ($result = $this->db_conn->query($query))   {
                    $result->num_rows > 0 &&
                        $insert = FALSE;
                }
                else    {
                    die ("Error sending the query '".$query."' to MySQL");
                }
            }
            if ($insert)   {
                $query = "insert into ".self::DATA_RNDUSERSC_NAME." (id, user) values (".$id.", \"".$user."\")";
				$this->db_conn->query($query) ||
					die ("Error sending the query '".$query."' to MySQL: ".$this->mysrnd_con->error."\n");
                $id++;
                $i++;
            }
        }
        // Update users numbers property
        $this->rnd_users_number =  $this->recordNumber(self::DATA_RNDUSERSC_NAME);

    }

    /**
     * Returns true if the "Random_UsersList" table has records
     * @returns boolean
     */
    function randomUser_exists()	{
	return $this->recordNumber(self::RNDUSERSC_NAME) > 0;
	}

    /**
     *  Helper function to create IPs table in database
     *  @param string $tablename
     *  @param boolean $useindex    Sets if a unique index for IP name must be created
     *  @access private
     */
    private function createIPsTable($tablename = self::DATA_RNDIPSC_NAME, $use_index = true) {
        // Table creation if it does not exists
        if (!$this->tableExists(self::DATA_RNDIPSC_NAME)) {
            if ($use_index) {
                $query = "CREATE TABLE ".self::DATA_RNDIPSC_NAME." (
                id INT NOT NULL PRIMARY KEY,
                ip VARCHAR(15),
                unique index ip_index (ip)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
            }
            else {
                $query = "CREATE TABLE ".self::DATA_RNDIPSC_NAME." (
                id INT NOT NULL PRIMARY KEY,
                ip VARCHAR(15)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
            }
            $this->db_conn->query($query) ||
                die ("Error sending the query '".$query."' to MySQL: ".$this->db_conn->error."\n");
        }
    }

    /**
     *  Save random IPs in MySQL.
     *  The parameters are the number of IPs to create and two booleans: if we want an unique index to be created for the IP (default is TRUE) and if we want that the IP is unique (default TRUE)
     *  If the IP is going to be unique the existence of itis verified with a query before inserting a new one.
     *  The id will be autonumeric (1, 2, 3 ....)
	 *  @param integer $number
     *  @param boolean $use_index
     *  @param boolean $dont_repeat
     */
    function createIPs($number, $use_index = TRUE, $dont_repeat = TRUE)	{
        $id = $this->rnd_ips_number + 1;   // Autonumeric

        // Table creation if it does not exists
        $this->createIPsTable(self::DATA_RNDIPSC_NAME, $use_index);

        $i = 1;
	while ($i <= $number)	{
            $ip = $this->getRandomIP();
            // We verify if IP is in the collection only if it is needed
            $insert = TRUE;
            if ($dont_repeat) {
                $query = "select id from ".self::DATA_RNDIPSC_NAME." where ip=\"".$ip."\"";
                if ($result = $this->db_conn->query($query))   {
                    $result->num_rows > 0 &&
                        $insert = FALSE;
                }
                else    {
                    die ("Error sending the query '".$query."' to MySQL");
                }
            }
            if ($insert)   {
                $query = "insert into ".self::DATA_RNDIPSC_NAME." (id, ip) values (".$id.", \"".$ip."\")";
				$this->db_conn->query($query) ||
					die ("Error sending the query '".$query."' to MySQL: ".$this->mysrnd_con->error."\n");
                $id++;
                $i++;
            }
        }
        // Update IPs number property
        $this->rnd_ips_number = $this->recordNumber(self::DATA_RNDIPSC_NAME);

    }

    /**
     * Returns true if the "Random_IPsList" table has records
     * @returns boolean
     */
    function randomIPs_exists()	{
        return $this->recordNumber(self::RNDIPSC_NAME) > 0;
	}

    /**
     *  Helper function to create Domains table in database
     *  @param string $tablename
     *  @param boolean $useindex    Sets if a unique index for IP name must be created
     *  @access private
     */
    private function createDomainsTable($tablename = self::DATA_RNDDOMAINSC_NAME, $use_index = true) {
        // Table creation if it does not exists
        if (!$this->tableExists(self::DATA_RNDDOMAINSC_NAME)) {
            if ($use_index) {
                $query = "CREATE TABLE ".self::DATA_RNDDOMAINSC_NAME." (
                id INT NOT NULL PRIMARY KEY,
                domain VARCHAR(255),
                unique index domain_index (domain)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
            }
            else {
                $query = "CREATE TABLE ".self::DATA_RNDDOMAINSC_NAME." (
                id INT NOT NULL PRIMARY KEY,
                domain VARCHAR(255)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
            }
            $this->db_conn->query($query) ||
                die ("Error sending the query '".$query."' to MySQL: ".$this->db_conn->error."\n");
        }
    }

    /**
     *  Save random domains in MySQL.
     *  The parameters are the number of domains to create and two booleans: if we want an unique index to be created for the domain (default is TRUE) and if we want that the domain is unique (default TRUE)
     *  If the domain is going to be unique the existence of itis verified with a query before inserting a new one.
     *  The id will be autonumeric (1, 2, 3 ....)
     *  @param integer $number
     *  @param boolean $use_index
     *  @param boolean $dont_repeat
     */
    function createDomains($number, $use_index = TRUE, $dont_repeat = TRUE)	{
	$id = $this->rnd_domains_number + 1;   // Autonumeric

        // Table creation if it does not exists
        $this->createIPsTable(self::DATA_RNDDOMAINSC_NAME, $use_index);

        $i = 1;
		while ($i <= $number)	{
            $domain = $this->getRandomDomain();
            // We verify if the domain is in the collection only if it is needed
            $insert = TRUE;
            if ($dont_repeat) {
                $query = "select id from ".self::DATA_RNDDOMAINSC_NAME." where domain=\"".$domain."\"";
                if ($result = $this->db_conn->query($query))   {
                    $result->num_rows > 0 &&
                        $insert = FALSE;
                }
                else    {
                    die ("Error sending the query '".$query."' to MySQL");
                }
            }
            if ($insert)   {
                $query = "insert into ".self::DATA_RNDDOMAINSC_NAME." (id, domain) values (".$id.", \"".$domain."\")";
				$this->db_conn->query($query) ||
					die ("Error sending the query '".$query."' to MySQL: ".$this->mysrnd_con->error."\n");
                $id++;
                $i++;
            }
        }
        // Update Domains number property
        $this->rnd_domains_number = $this->recordNumber(self::DATA_RNDDOMAINSC_NAME);
    }

    /**
     * Returns true if the "Random_DomainsList" table has records
     * @returns boolean
     */
    function randomDomains_exists()	{
        return $this->recordNumber(self::RNDDOMAINSC_NAME) > 0;
    }

    /**
     *  Helper function to create URIs table in database
     *  @param string $tablename
     *  @access private
     */
    private function createURIsTable($tablename = self::DATA_RNDURISC_NAME) {
        // Table creation if it does not exists
        if (!$this->tableExists(self::DATA_RNDURISC_NAME)) {
            $query = "CREATE TABLE ".self::DATA_RNDURISC_NAME." (
                id INT NOT NULL PRIMARY KEY,
                uri VARCHAR(100)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
            $this->db_conn->query($query) ||
                die ("Error sending the query '".$query."' to MySQL: ".$this->db_conn->error."\n");
        }
    }
    /**
     *  Save random URIs in MySQL.
     *  The parameter is the number of URIs to create.
     *  The id will be autonumeric (1, 2, 3 ....)
     *  @param integer $number
     */
    function createURIs($number)     {
        $id = $this->rnd_uris_number + 1;   // Autonumeric

        // Table creation if it does not exists
        $this->createIPsTable(self::DATA_RNDURISC_NAME);

        $i = 1;
        while ($i <= $number)   {
            $uri = $this->getRandomString(mt_rand(0,100));
            $query = "insert into ".self::DATA_RNDURISC_NAME." (id, uri) values (".$id.", \"".$uri."\")";
            $this->db_conn->query($query) ||
                die ("Error sending the query '".$query."' to MySQL: ".$this->mysrnd_con->error."\n");
            $id++;
            $i++;
        }
        // Update Domains number property
        $this->rnd_uris_number = $this->recordNumber(self::DATA_RNDURISC_NAME);
    }

    /**
     * Create in memory tables and loads user, ips and domains data from persitent ones
     */
    function loadDataInRAM()  {
        // Load users
        if ($this->tableExists(self::DATA_RNDUSERSC_NAME)) {
            $this->rnd_users = array();
            if ($results = $this->getResults("select * from ".self::DATA_RNDUSERSC_NAME))  {
                while ($user = $results->fetch_assoc()) {
                    $this->rnd_users[$user['id']] = $user['user'];
                }
            }
        }
        // Load ips
        if ($this->tableExists(self::DATA_RNDIPSC_NAME)) {
            $this->rnd_ips = array();
            if ($results = $this->getResults("select * from ".self::DATA_RNDIPSC_NAME))  {
                while ($ip = $results->fetch_assoc()) {
                    $this->rnd_ips[$ip['id']] = $ip['ip'];
                }
            }
        }
        // Load domains
        if ($this->tableExists(self::DATA_RNDDOMAINSC_NAME)) {
            $this->rnd_domains = array();
            if ($results = $this->getResults("select * from ".self::DATA_RNDDOMAINSC_NAME))  {
                while ($domain = $results->fetch_assoc()) {
                    $this->rnd_domains[$domain['id']] = $domain['domain'];
                }
            }
        }
        // Load URIs
        if ($this->tableExists(self::DATA_RNDURISC_NAME)) {
            $this->rnd_uris = array();
            if ($results = $this->getResults("select * from ".self::DATA_RNDURISC_NAME))  {
                while ($uri = $results->fetch_assoc()) {
                    $this->rnd_uris[$uri['id']] = $uri['uri'];
                }
            }
        }
    }

    /**
     *  Returns a random IP from the generated collection
     *  @returns string
     */
    function searchIP()	{
    	$position = mt_rand(1, $this->rnd_ips_number);
        return $this->rnd_ips[$position];
    }

    /**
     *  Returns a random user from the generated collection
     *  @return string
     */
    function searchUser()	{
	$position = mt_rand(1, $this->rnd_users_number);
        return $this->rnd_users[$position];
    }

    /**
     * Export user table in CSV format saving it in path passed as parameter (Users.csv under CSV directory by default)
     * @param string $filename
     * @access public
     */
    function exportUsersToCSV($filename = "CSV/Users.csv") {
        if (dirname($filename) == "CSV" and !file_exists("CSV"))        {
            mkdir("CSV");
        }

        ($fh = fopen($filename, "a")) || die("Not possible to open ".$filename." file");
        fwrite($fh, "id,user\n") || die("Not possible to write in  ".$filename." file");
        for ($i = 1; $i <= $this->rnd_users_number; $i++)  {
            fputcsv($fh, array($i,$this->rnd_users[$i])) || die("Not possible to write in  ".$filename." file");
        }
        fclose($fh);
    }

    /**
     * Import users data in a CSV file to a MySQL table (if the table already exist it will be deleted)
     * The MySQL user must have the global privilege FILE!
     * @param string $filename
     * @access public
     */
    function importUsersFromCSV($filename = "CSV/Users.csv")  {
        if ($filename == "CSV/Users.csv")  {
            $filename = getcwd()."/".$filename;
            }
        $this->dropTable(self::DATA_RNDUSERSC_NAME);
        $this->createUsersTable();
        $this->sendQuery("load data infile \"".$filename."\" into table ".self::DATA_RNDUSERSC_NAME." fields terminated by ',' ignore 1 lines");
    }

    /**
     * Export ip's table in CSV format saving it in path passed as parameter (IPs.csv under CSV directory by default)
     * @param string $filename
     * @access public
     */
    function exportIPsToCSV($filename = "CSV/IPs.csv") {
        if (dirname($filename) == "CSV" and !file_exists("CSV"))        {
            mkdir("CSV");
        }

        ($fh = fopen($filename, "a")) || die("Not possible to open ".$filename." file");
        fwrite($fh, "id,ip\n");
        for ($i = 1; $i <= $this->rnd_ips_number; $i++)  {
            fputcsv($fh, array($i,$this->rnd_ips[$i])) || die("Not possible to write in  ".$filename." file");
        }
        fclose($fh);
    }

    /**
     * Import IPs data in a CSV file to a MySQL table (if the table already exist it will be deleted)
     * The MySQL user must have the global privilege FILE!
     * @param string $filename
     * @access public
     */
    function importIPsFromCSV($filename = "CSV/IPs.csv")  {
        if ($filename == "CSV/IPs.csv")  {
            $filename = getcwd()."/".$filename;
            }
        $this->dropTable(self::DATA_RNDIPSC_NAME);
        $this->createIPsTable();
        $this->sendQuery("load data infile \"".$filename."\" into table ".self::DATA_RNDIPSC_NAME." fields terminated by ',' ignore 1 lines");
    }

    /**
     * Export domains table in CSV format saving it in path passed as parameter (Domains.csv under CSV directory by default)
     * @param string $filename
     * @access public
     */
    function exportDomainsToCSV($filename = "CSV/Domains.csv") {
        if (dirname($filename) == "CSV" and !file_exists("CSV"))        {
            mkdir("CSV");
        }

        ($fh = fopen($filename, "a")) || die("Not possible to open ".$filename." file");
        fwrite($fh, "id,domain\n");
        for ($i = 1; $i <= $this->rnd_domains_number; $i++)  {
            fputcsv($fh, array($i,$this->rnd_domains[$i])) || die("Not possible to write in  ".$filename." file");
        }
        fclose($fh);
    }

    /**
     * Import Domains data in a CSV file to a MySQL table (if the table already exist it will be deleted)
     * The MySQL user must have the global privilege FILE!
     * @param string $filename
     * @access public
     */
    function importDomainsFromCSV($filename = "CSV/Domains.csv")  {
        if ($filename == "CSV/Domains.csv")  {
            $filename = getcwd()."/".$filename;
            }
        $this->dropTable(self::DATA_RNDDOMAINSC_NAME);
        $this->createDomainsTable();
        $this->sendQuery("load data infile \"".$filename."\" into table ".self::DATA_RNDDOMAINSC_NAME." fields terminated by ',' ignore 1 lines");
    }

    /**
     * Export URIs table in CSV format saving it in path passed as parameter (URIs.csv under CSV directory by default)
     * @param string $filename
     * @access public
     */
    function exportURIsToCSV($filename = "CSV/URIs.csv") {
        if (dirname($filename) == "CSV" and !file_exists("CSV"))        {
            mkdir("CSV");
        }

        ($fh = fopen($filename, "a")) || die("Not possible to open ".$filename." file");
        fwrite($fh, "id,uri\n");
        for ($i = 1; $i <= $this->rnd_uris_number; $i++)  {
            fputcsv($fh, array($i,$this->rnd_uris[$i])) || die("Not possible to write in  ".$filename." file");
        }
        fclose($fh);
    }

    /**
     * Import URIs data in a CSV file to a MySQL table (if the table already exist it will be deleted)
     * The MySQL user must have the global privilege FILE!
     * @param string $filename
     * @access public
     */
    function importURIsFromCSV($filename = "CSV/URIs.csv")  {
        if ($filename == "CSV/URIs.csv")  {
            $filename = getcwd()."/".$filename;
            }
        $this->dropTable(self::DATA_RNDURISC_NAME);
        $this->createURIsTable();
        $this->sendQuery("load data infile \"".$filename."\" into table ".self::DATA_RNDURISC_NAME." fields terminated by ',' ignore 1 lines");
    }
    /**
     * Writes the first line (title) of a CSV file for non ftp log entry (NonFTP_Access_log.csv under CSV directory by default). If it exist it will be truncated. Returns the file handle
     * @param string filename
     * @returns filehandle
     * @access public
     */
    function createRandomNonFTPLogEntryCSV($filename = "CSV/NonFTP_Access_log.csv")     {
        if (dirname($filename) == "CSV" and !file_exists("CSV"))        {
            mkdir("CSV");
        }
        ($fh = fopen($filename, "w")) || die("Not possible to open ".$filename." file");
        fwrite($fh, "clientip,user,datetime,method,protocol,domain,uri,return_code,size\n");
        return $fh;
    }

    /**
     * Adds the non ftp log entry passed as parameter to a CSV file (NonFTP_Access_log.csv under CSV directory by default).
     * A third optional parameter is the file handle (to not open and close the file in a for loop which calls this function)
     * @param array $log_entry log entry as returned by {@link getRandomNonFTPLogEntry}
     * @param string $filename
     * @param filehandle $fh
     * @access public
     */
    function saveRandomNonFTPLogEntryToCSV($log_entry, $filename = "CSV/NonFTP_Access_log.csv", $fh = NULL)  {
        if (dirname($filename) == "CSV" and !file_exists("CSV"))        {
            mkdir("CSV");
        }

        if (is_null($fh))       {
            // A file handle is not given
            if (!file_exists($filename))    {
                $fh = $this->createRandomNonFTPLogEntryCSV($filename);
            }
            else {
                $fh = fopen($filename, "a");
            }
        }
        fputcsv($fh, $log_entry) || die("Not possible to write in  ".$filename." file");
        if (is_null($fh))       {
            fclose($fh);
        }

    }

    /**
     *  Returns a random HTTP method from the generated collection
     *  @returns string
     */
    function searchHTTPMethod()	{
	return $this->getRandomHTTPMethod();
	}

    /**
     *  Returns a random FTP method from the generated collection
     *  @returns string
     */
    function searchFTPMethod()	{
	return $this->getRandomFTPMethod();
	}

    /**
     *  Returns a random domain
     *  @returns string
     */
    function searchDomain() {
	$position = mt_rand(1, $this->rnd_domains_number);
        return $this->rnd_domains[$position];
    }

    /**
     *  Returns a random URI
     *  @returns string
     */
    function searchURI()	{
        $position = mt_rand(1, $this->rnd_uris_number);
        return $this->rnd_uris[$position];
	}

    /**
     *  Returns a random size
     *  @returns integer
     */
    function searchSize()	{
	return $this->getRandomSize();
	}

    /**
     *  Returns a random protocol
     *  @returns string
     */
    function searchProtocol()	{
	return $this->getRandomProtocol();
	}

    /**
     *  Returns a random return code
     *  @returns integer
     */
    function searchReturnCode()	{
	return $this->getRandomRetourCode();
	}

    /**
     *  Return a random log entry for non FTP access (http and tunnel)
     *  It has two optional arguments, initial and final timestamps, if we want to get a random time in log entry created
     *  @param integer $initial_timestamp
     *  @param integer $final_timestamp
     *  @returns array
     */
    function getRandomNonFTPLogEntry()	{
        if (func_num_args() == 2)	{
                $initial_timestamp = func_get_arg(0);
                $final_timestamp =  func_get_arg(1);
                $ts = mt_rand($initial_timestamp, $final_timestamp);
        }
        elseif (func_num_args() != 0)	{
                $arguments = func_get_args();
                die("Incorrect arguments number in getRrandomSORLogEntry function: ".implode(" ", $arguments)."\n");
        }
        else {
                $ts = time();
        }

        $document = array(
                'clientip' => $this->searchIP(),
                'user' => $this->searchUser(),
                'datetime' => $ts,
                'method' => $this->searchHTTPMethod(),
                'protocol' => $this->searchProtocol(),
                'domain' => $this->searchDomain(),
                'uri' => $this->searchURI(),
                'return_code' => $this->searchReturnCode(),
                'size' => $this->searchSize()	// Size is recorded in the database as string
        );

        return $document;
    }

    /**
     *  Update Users monthly report
     *  This function is private and is meant to be used each time an access log is processed to have real time statistics (only by month)
     *  @param string $user user name
     *  @param timestamp $timestamp date & time of access
     *  @param integer $volume size of data transferred
     *  @access private
     */
    private function saveUserReport($user, $timestamp, $volume)  {

        $table_name = self::USERS_REPORT_PREFIX.strftime("%Y%m", $timestamp);
        // Table creation if it does not exists
        if (!$this->tableExists($table_name)) {
            $query = "CREATE TABLE ".$table_name." (
                user CHAR(7) NOT NULL,
                nb INTEGER UNSIGNED NOT NULL,
                volume INTEGER UNSIGNED NOT NULL,
                unique index user_index (user)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

            $this->db_conn->query($query) ||
                die ("Error sending the query '".$query."' to MySQL: ".$this->db_conn->error."\n");

            // Insertion of a new user entry
            $query_insert = "insert into ".$table_name." (user, nb, volume) values (\"".$user."\", 1, ".$volume.")";
            $this->db_conn->query($query_insert) ||
                die ("Error sending the query '".$query_insert."' to MySQL: ".$this->db_conn->error."\n");
        }
        else {
        	$query = "select * from ".$table_name." where user=\"".$user."\"";
            if ($result = $this->db_conn->query($query))   {
                if ($result->num_rows > 0)  {
                    // There is a user entry for this month
                    $row = $result->fetch_assoc();
                    $new_nb = $row['nb'] + 1;
                    $new_volume = $row['volume'] + $volume;
                    $query_update = "update ".$table_name." set nb=".$new_nb.", volume=".$new_volume." where user=\"".$user."\"";
                    $this->db_conn->query($query_update) ||
                        die ("Error sending the query '".$query_update."' to MySQL: ".$this->db_conn->error."\n");
                }
                else  {
                    // Insertion of a new user entry
                    $query_insert = "insert into ".$table_name." (user, nb, volume) values (\"".$user."\", 1, ".$volume.")";
                    $this->db_conn->query($query_insert) ||
                        die ("Error sending the query '".$query_insert."' to MySQL: ".$this->db_conn->error."\n");
                }
            }
            else    {
                die ("Error sending the query '".$query."' to MySQL");
            }
        }
    }

    /**
     *  Update Domains monthly report
     *  This function is private and is meant to be used each time an access log is processed to have real time statistics (only by month)
     *  @param string $domain domain name
     *  @param timestamp $timestamp date & time of access
     *  @param integer $volume size of data transferred
     *  @access private
     */
    private function saveDomainReport($domain, $timestamp, $volume)  {

        $table_name = self::DOMAINS_REPORT_PREFIX.strftime("%Y%m", $timestamp);
        // Table creation if it does not exists
        if (!$this->tableExists($table_name)) {
            $query = "CREATE TABLE ".$table_name." (
                domain CHAR(255) NOT NULL,
                nb INTEGER UNSIGNED NOT NULL,
                volume INTEGER UNSIGNED NOT NULL,
                unique index domain_index (domain)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

            $this->db_conn->query($query) ||
                die ("Error sending the query '".$query."' to MySQL: ".$this->db_conn->error."\n");

            // Insertion of a new domain entry
            $query_insert = "insert into ".$table_name." (domain, nb, volume) values (\"".$domain."\", 1, ".$volume.")";
            $this->db_conn->query($query_insert) ||
                die ("Error sending the query '".$query_insert."' to MySQL: ".$this->db_conn->error."\n");
        }
        else {
        	$query = "select * from ".$table_name." where domain=\"".$domain."\"";
            if ($result = $this->db_conn->query($query))   {
                if ($result->num_rows > 0)  {
                    // There is a domain entry for this month
                    $row = $result->fetch_assoc();
                    $new_nb = $row['nb'] + 1;
                    $new_volume = $row['volume'] + $volume;
                    $query_update = "update ".$table_name." set nb=".$new_nb.", volume=".$new_volume." where domain=\"".$domain."\"";
                    $this->db_conn->query($query_update) ||
                        die ("Error sending the query '".$query_update."' to MySQL: ".$this->db_conn->error."\n");
                }
                else  {
                    // Insertion of a new domain entry
                    $query_insert = "insert into ".$table_name." (domain, nb, volume) values (\"".$domain."\", 1, ".$volume.")";
                    $this->db_conn->query($query_insert) ||
                        die ("Error sending the query '".$query_insert."' to MySQL: ".$this->db_conn->error."\n");
                }
            }
            else    {
                die ("Error sending the query '".$query."' to MySQL");
            }
        }
    }

    /**
     *  Receives a log entry and saves the data and, optionally, monthly and daily precalculated values in database.
     *  By default the reports are created. If the second argument is FALSE they will not be generated.
     *  A id field autonumeric will be created.
     *
     *  @param array $log_entry log entry as returned by {@link getRandomNonFTPLogEntry}
     *  @param boolean $create_reports
     */
    function saveRandomNonFTPLogEntry($log_entry, $create_reports=TRUE)    {

        // Table creation if it does not exists
        if (!$this->tableExists(self::NONFTPLOG_NAME)) {
            $query = "CREATE TABLE ".self::NONFTPLOG_NAME." (
                id int not null auto_increment,
                clientip VARCHAR(15) NOT NULL,
                user CHAR(7) NOT NULL,
                datetime TIMESTAMP NOT NULL,
                method VARCHAR(10) NOT NULL,
                protocol VARCHAR(10) NOT NULL,
                domain VARCHAR(255) NOT NULL,
                uri VARCHAR(100) NOT NULL,
                return_code SMALLINT UNSIGNED NOT NULL,
                size INTEGER UNSIGNED NOT NULL
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

            $this->db_conn->query($query) ||
                die ("Error sending the query '".$query."' to MySQL: ".$this->db_conn->error."\n");
        }

        $query = "insert into ".self::NONFTPLOG_NAME." (clientip, user, datetime, method, protocol, domain, uri, return_code, size) values (".
                "\"".$log_entry['clientip']."\", \"".$log_entry['user']."\", \"".date("Y:m:d H:i:s", $log_entry['datetime'])."\", \"".$log_entry['method']."\", ".
                "\"".$log_entry['protocol']."\", \"".$log_entry['domain']."\", \"".$log_entry['uri']."\", ".$log_entry['return_code'].", ".$log_entry['size'].")";
		$this->db_conn->query($query) ||
			die ("Error sending the query '".$query."' to MySQL: ".$this->mysrnd_con->error."\n");

		# Monthly reports data update
        if ($create_reports)    {
            $timestamp = $log_entry["datetime"];
            $this->saveUserReport($log_entry["user"], $timestamp, $log_entry['size']);
            $this->saveDomainReport($log_entry["domain"], $timestamp, $log_entry['size']);
            }

    }

    /**
     *  Return a random log entry for FTP access. It is very similar to HTTP and tunnel access but with less fields (there is no protocol and return code)
     *  It has two optional arguments, initial and final timestamps, if we want to get a random time in log entry created
     *  @param integer $initial_timestamp
     *  @param integer $final_timestamp
     *  @returns array
     */
    function getRandomFTPLogEntry()	{
        if (func_num_args() == 2)	{
                $initial_timestamp = func_get_arg(0);
                $final_timestamp =  func_get_arg(1);
                $ts = mt_rand($initial_timestamp, $final_timestamp);
        }
        elseif (func_num_args() != 0)	{
                $arguments = func_get_args();
                die("Incorrect arguments number in getRrandomSORLogEntry function: ".implode(" ", $arguments)."\n");
        }
        else {
                $ts = time();
        }

        $document = array(
                'clientip' => $this->searchIP(),
                'user' => $this->searchUser(),
                'datetime' => $ts,
                'method' => $this->searchFTPMethod(),
                'domain' => $this->searchDomain(),
                'uri' => $this->searchURI(),
                'size' => $this->searchSize()	// Size is recorded in the database as string
        );

        return $document;
    }

    /**
     *  Receives a FTP log entry and saves the data and, optionally, monthly and daily precalculated values in database.
     *  By default the reports are created. If the second argument is FALSE they will not be generated
     *  A id field autonumeric will be created.
     *
     *  @param array $log_entry log entry as returned by {@link getRandomNonFTPLogEntry}
     *  @param boolean $create_reports
     */
    function saveRandomFTPLogEntry($log_entry, $create_reports=TRUE)    {

        // Table creation if it does not exists
        if (!$this->tableExists(self::FTPLOG_NAME)) {
            $query = "CREATE TABLE ".self::FTPLOG_NAME." (
                id int not null auto_increment,
                clientip VARCHAR(15) NOT NULL,
                user CHAR(7) NOT NULL,
                datetime TIMESTAMP NOT NULL,
                method VARCHAR(10) NOT NULL,
                domain VARCHAR(255) NOT NULL,
                uri VARCHAR(100) NOT NULL,
                size INTEGER UNSIGNED NOT NULL
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

            $this->db_conn->query($query) ||
                die ("Error sending the query '".$query."' to MySQL: ".$this->db_conn->error."\n");
        }

        $query = "insert into ".self::FTPLOG_NAME." (clientip, user, datetime, method, domain, uri, size) values (".
                "\"".$log_entry['clientip']."\", \"".$log_entry['user']."\", \"".date("Y:m:d H:i:s", $log_entry['datetime'])."\", \"".$log_entry['method']."\", ".
                "\"".$log_entry['domain']."\", \"".$log_entry['uri']."\", ".$log_entry['size'].")";
		$this->db_conn->query($query) ||
			die ("Error sending the query '".$query."' to MySQL: ".$this->mysrnd_con->error."\n");

		# Monthly reports data update
        if ($create_reports)    {
            $timestamp = $log_entry["datetime"];
            $this->saveUserReport($log_entry["user"], $timestamp, $log_entry['size']);
            $this->saveDomainReport($log_entry["domain"], $timestamp, $log_entry['size']);
            }

    }


}
?>
