<?php
/**
 *  MySQLRandomElements.class.php
 *  File with the class used to generate random elements and save then in MySQL (users, URL's and IP's)
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page {@link http://www.ciges.net}
 *  @license http://www.gnu.org/copyleft/gpl.html GNU GPLv3
 *  @version 20120719
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
     *  This function returns the number of records for the table passed as parameter
     *  @param string tablename
     *  @returns integer
     *  @access private
     */
	private function recordNumber($tablename)	{
		$query = "show table status where Name=\"".$tablename."\"";
		if ($result = $this->db_conn->query($query))	{
			$row = $result->fetch_assoc();
			return (int) $row["Rows"];
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
        
	}
    
    /**
     *  Destructor. Close the open connection to MySQL database
 	 */
	function __destruct()	{
		$this->db_conn->close();
	}
    
    /**
     *  Verify if a random user exists in the database
     
    
    /**
     *  Save random users in MySQL.  
     *  The parameters are the number of users to create and to booleans: if we want an unique index to be created for the user name (default is TRUE) and if we want that the user name is unique (default TRUE). 
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
 }
 
 ?>