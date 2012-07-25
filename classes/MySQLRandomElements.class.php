<?php
/**
 *  MySQLRandomElements.class.php
 *  File with the class used to generate random elements and save then in MySQL (users, URL's and IP's)
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page {@link http://www.ciges.net}
 *  @license http://www.gnu.org/copyleft/gpl.html GNU GPLv3
 *  @version 20120725
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
 
    /**#@+
	 * Default names for random data collections
     */
	const RNDUSERSC_NAME = "Random_UsersList";
	const RNDIPSC_NAME = "Random_IPsList";
	const RNDDOMAINSC_NAME = "Random_DomainsList";
    const NONFTPLOG_NAME = "NonFTP_Access_log";
    const FTPLOG_NAME = "FTP_Access_log";
    /**#@-*/
    
    /**#@+
     *  Default prefixes for monthly reports
     */
    const USERS_REPORT_PREFIX = "Users_Monthly_Report_";
	const DOMAINS_REPORT_PREFIX = "Domains_Monthly_Report_";
	/**#@-*/
    
    /**#@+
     * Constants for default connection values
     */
    const DEFAULT_USER = "mysqldb";
    const DEFAULT_PASSWORD = "mysqldb";
    const DEFAULT_HOST = "localhost";
    const DEFAULT_DB = "InternetAccessLog";
	/**#@-*/
    
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
    
    /**#@+
     * Number of element of each created collection in MySQL (for cache purposes)
     * @access private
     * @var string
     */
	private $rnd_users_number;
	private $rnd_ips_number;
	private $rnd_domains_number;
    private $nonftp_log_recordnumber;
    private $ftp_log_recordnumber;
    /**#@-*/
    
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
		catch (MongoConnectionException $e) {
			die("Connection MySQL impossible: (".$e->getCode().") ".$e->getMessage()."\n");
		}
		
		// Stores the number of elements of each stored random elements collection
		$this->rnd_users_number =  $this->recordNumber(self::RNDUSERSC_NAME);
		$this->rnd_ips_number = $this->recordNumber(self::RNDIPSC_NAME);
		$this->rnd_domains_number = $this->recordNumber(self::RNDDOMAINSC_NAME);
        $this->nonftp_log_recordnumber = $this->recordNumber(self::NONFTPLOG_NAME);
        $this->ftp_log_recordnumber = $this->recordNumber(self::FTPLOG_NAME);   
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
        if (!$this->tableExists(self::RNDUSERSC_NAME)) {
            if ($use_index) {
                $query = "CREATE TABLE ".self::RNDUSERSC_NAME." (
                id INT NOT NULL PRIMARY KEY,
                user CHAR(7),
                unique index user_index (user)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
            }
            else {
                $query = "CREATE TABLE ".self::RNDUSERSC_NAME." (
                id INT NOT NULL PRIMARY KEY,
                user CHAR(7)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
            }
            $this->db_conn->query($query) ||
				die ("Error sending the query '".$query."' to MySQL: ".$this->db_conn->error."\n");
        }
        
        $i = 1;
		while ($i <= $number)	{
            $user = $this->getRandomUser();
            // We verify if the user is in the collection only if it is needed
            $insert = TRUE;
            if ($dont_repeat) {
                $query = "select id from ".self::RNDUSERSC_NAME." where user=\"".$user."\"";
                if ($result = $this->db_conn->query($query))   {
                    $result->num_rows > 0 &&
                        $insert = FALSE;
                }
                else    {
                    die ("Error sending the query '".$query."' to MySQL");
                }
            }
            if ($insert)   {
                $query = "insert into ".self::RNDUSERSC_NAME." (id, user) values (".$id.", \"".$user."\")";
				$this->db_conn->query($query) ||
					die ("Error sending the query '".$query."' to MySQL: ".$this->mysrnd_con->error."\n");
                $id++;
                $i++;
            }
        }
            
    }
    
    /** 
     * Returns true if the "Random_UsersList" table has records
     * @returns boolean
     */
	function randomUser_exists()	{
		return $this->recordNumber(self::RNDUSERSC_NAME) > 0;
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
        if (!$this->tableExists(self::RNDIPSC_NAME)) {
            if ($use_index) {
                $query = "CREATE TABLE ".self::RNDIPSC_NAME." (
                id INT NOT NULL PRIMARY KEY,
                ip VARCHAR(15),
                unique index ip_index (ip)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
            }
            else {
                $query = "CREATE TABLE ".self::RNDIPSC_NAME." (
                id INT NOT NULL PRIMARY KEY,
                ip VARCHAR(15)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
            }
            $this->db_conn->query($query) ||
				die ("Error sending the query '".$query."' to MySQL: ".$this->db_conn->error."\n");
        }
        
        $i = 1;
		while ($i <= $number)	{
            $ip = $this->getRandomIP();
            // We verify if IP is in the collection only if it is needed
            $insert = TRUE;
            if ($dont_repeat) {
                $query = "select id from ".self::RNDIPSC_NAME." where ip=\"".$ip."\"";
                if ($result = $this->db_conn->query($query))   {
                    $result->num_rows > 0 &&
                        $insert = FALSE;
                }
                else    {
                    die ("Error sending the query '".$query."' to MySQL");
                }
            }
            if ($insert)   {
                $query = "insert into ".self::RNDIPSC_NAME." (id, ip) values (".$id.", \"".$ip."\")";
				$this->db_conn->query($query) ||
					die ("Error sending the query '".$query."' to MySQL: ".$this->mysrnd_con->error."\n");
                $id++;
                $i++;
            }
        }
            
    }
    
    /** 
     * Returns true if the "Random_IPsList" table has records
     * @returns boolean
     */
	function randomIPs_exists()	{
		return $this->recordNumber(self::RNDIPSC_NAME) > 0;
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
        if (!$this->tableExists(self::RNDDOMAINSC_NAME)) {
            if ($use_index) {
                $query = "CREATE TABLE ".self::RNDDOMAINSC_NAME." (
                id INT NOT NULL PRIMARY KEY,
                domain VARCHAR(255),
                unique index domain_index (domain)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
            }
            else {
                $query = "CREATE TABLE ".self::RNDDOMAINSC_NAME." (
                id INT NOT NULL PRIMARY KEY,
                domain VARCHAR(255)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
            }
            $this->db_conn->query($query) ||
				die ("Error sending the query '".$query."' to MySQL: ".$this->db_conn->error."\n");
        }
        
        $i = 1;
		while ($i <= $number)	{
            $domain = $this->getRandomDomain();
            // We verify if the domain is in the collection only if it is needed
            $insert = TRUE;
            if ($dont_repeat) {
                $query = "select id from ".self::RNDDOMAINSC_NAME." where domain=\"".$domain."\"";
                if ($result = $this->db_conn->query($query))   {
                    $result->num_rows > 0 &&
                        $insert = FALSE;
                }
                else    {
                    die ("Error sending the query '".$query."' to MySQL");
                }
            }
            if ($insert)   {
                $query = "insert into ".self::RNDDOMAINSC_NAME." (id, domain) values (".$id.", \"".$domain."\")";
				$this->db_conn->query($query) ||
					die ("Error sending the query '".$query."' to MySQL: ".$this->mysrnd_con->error."\n");
                $id++;
                $i++;
            }
        }
            
    }
    
    /** 
     * Returns true if the "Random_DomainsList" table has records
     * @returns boolean
     */
	function randomDomains_exists()	{
		return $this->recordNumber(self::RNDDOMAINSC_NAME) > 0;
	}  
 
    /**
     *  Returns a random IP from the generated collection
     *  @returns string
     */
	function searchIP()	{
    	$position = mt_rand(1, $this->rnd_ips_number);
        $query = "select ip from ".self::RNDIPSC_NAME." where id=".$position;
        if ($result = $this->db_conn->query($query))   {
            $row = $result->fetch_assoc();
            return $row["ip"];
        }
        else    {
            die ("Error sending the query '".$query."' to MySQL");
        }
	}
    
    /**
     *  Returns a random user from the generated collection
     *  @return string
     */
	function searchUser()	{
		$position = mt_rand(1, $this->rnd_users_number);
        $query = "select user from ".self::RNDUSERSC_NAME." where id=".$position;
        if ($result = $this->db_conn->query($query))   {
            $row = $result->fetch_assoc();
            return $row["user"];
        }
        else    {
            die ("Error sending the query '".$query."' to MySQL");
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
        $query = "select domain from ".self::RNDDOMAINSC_NAME." where id=".$position;
        if ($result = $this->db_conn->query($query))   {
            $row = $result->fetch_assoc();
            return $row["domain"];
        }
        else    {
            die ("Error sending the query '".$query."' to MySQL");
        }
	}
	
    /**
     *  Returns a random URI
     *  @returns string
     */
	function searchURI()	{
        return $this->getRandomString(mt_rand(0,100));
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
     *  By default the reports are created. If the second argument is FALSE they will not be generated
     *  The id for the document in Mongo is created as an integer autonumeric.
     *
     *  @param array $log_entry log entry as returned by {@link getRandomNonFTPLogEntry}
     *  @param boolean $create_reports
     */
    function saveRandomNonFTPLogEntry($log_entry, $create_reports=TRUE)    {
        
        // Table creation if it does not exists
        if (!$this->tableExists(self::NONFTPLOG_NAME)) {
            $query = "CREATE TABLE ".self::NONFTPLOG_NAME." (
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
     *  The id for the document in Mongo is created as an integer autonumeric.
     *
     *  @param array $log_entry log entry as returned by {@link getRandomNonFTPLogEntry}
     *  @param boolean $create_reports
     */
    function saveRandomFTPLogEntry($log_entry, $create_reports=TRUE)    {
        
        // Table creation if it does not exists
        if (!$this->tableExists(self::FTPLOG_NAME)) {
            $query = "CREATE TABLE ".self::FTPLOG_NAME." (
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