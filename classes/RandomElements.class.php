<?php
/**
 *  RandomElements.class.php
 *  File with the class used to generate random elements (users, URL's ...)
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page {@link http://www.ciges.net}
 *  @license http://www.gnu.org/copyleft/gpl.html GNU GPLv3
 *  @version 20120718
 *
 *  @package InternetAccessLog
 *  @filesource
 */
/**
 *  This class is used to generate random elements (users, IP's and URL's) to make tests (populate tables in MySQL or collections in MongoDB)
 *  @package InternetAccessLog
 */
class RandomElements {
    /**
     * List of possible HTTP Methods
     * @access private
     * @var array
     */
	private $listOfHTTPMethods = array("CONNECT", "GET", "HEAD", "MKCOL", "OPTIONS", "POST", "PROPFIN", "PUT");
    /**
     * List of possible FTP Methods
     * @access private
     * @var array
     */
	private $listOfFTPMethods = array("NOOP", "PWD", "RETR", "SIZE", "STOR", "TYPE", "USER");
    /**
     * List of possible protocolos
     * @access private
     * @var array
     */
	private $listOfProtocols = array("ftp", "http", "tunn");
    /**
     * List of possible first level internet domains
     * @access private
     * @var array
     */
	private $listOfInternetDomains = array(".ar", ".asia", ".biz", ".bo", ".cat", ".cl", ".co", ".cn", ".com", ".cr", ".do", ".ec", ".edu", ".es", ".eu", ".fm", ".fr", ".gov", ".gt", ".hn", ".info", ".int", ".jobs", ".lat", ".mil", ".mobi", ".museum", ".mx", ".ni", ".name", ".net", ".nl", ".org", ".pe", ".pro", ".py", ".ru", ".sv", ".tel", ".tk", ".travel", ".tv", ".ua", ".uy", ".ve", ".web", ".ws", ".xxx", "r", ".asia", ".biz", ".bo", ".cat", ".cl", ".co", ".cn", ".com", ".cr", ".do", ".ec", ".edu", ".es", ".eu", ".fm", ".fr", ".gov", ".gt", ".hn", ".info", ".int", ".jobs", ".lat", ".mil", ".mobi", ".museum", ".mx", ".ni", ".name", ".net", ".nl", ".org", ".pe", ".pro", ".py", ".ru", ".sv", ".tel", ".tk", ".travel", ".tv", ".ua", ".uy", ".ve", ".web", ".ws", ".xxx");
    /**
     * List of possible return codes
     * @access private
     * @var array
     */
	private $listOfRetourCodes = array(200, 201, 207, 301, 304, 403, 404, 405, 500, 502);

    /**
     * Returns a random user, composed of one letter and 6 numbers
     * @return string
     */
    function getRandomUser() {
		$letter = "abcdefghijklmnopqrstuvwxyz";
		$user = $letter{mt_rand(0, 25)}.sprintf("%06d", mt_rand(1,999999));
		
		return ucfirst($user);
	}
    
    /**
     * Returns a random IP (string with 4 numbers between 0 and 255). No check for validity is made
     * @return string
     */
	function getRandomIP()	{
		$ip = mt_rand(0,255).".".mt_rand(0,255).".".mt_rand(0,255).".".mt_rand(0,255);
		return $ip;
	}
    
    /**
     * Returns a semi random HTTP method (90% will be GET)
     * @return string
     */
	function getRandomHTTPMethod()	{
		if (mt_rand(0,9) == 9)	{
			return $this->listOfHTTPMethods[array_rand($this->listOfHTTPMethods)];
		}
		else
			{
			return "GET";
		}
		return ;
	}
    
    /** 
     * Returns a semi random HTTP method (90% will be RETR)
     * @return string
     */
	function getRandomFTPMethod()	{
		if (mt_rand(0,9) == 9)	{
			return $this->listOfFTPMethods[array_rand($this->listOfFTPMethods)];
		}
		else
			{
			return "RETR";
		}
		return ;
	}
    
    /**
     * Returns a random protocol (90% will be http)
     * @return string
     */
	function getRandomProtocol()	{
		if (mt_rand(0,9) == 9)	{
			return $this->listOfProtocols[array_rand($this->listOfProtocols)];
		}
		else
			{
			return "http";
		}
	}
    
    /**
     * Returns a random Internet domain (a www followed by a random string ended by a valid internet 
     * @return string
     */
	function getRandomDomain()	{
		$word = range('a', 'z');
		$len = mt_rand(1, count($word));
		shuffle($word);
		$domain .= "www.".substr(implode($word), 0, $len).$this->listOfInternetDomains[mt_rand(0,count($this->listOfInternetDomains)-1)];
		
		return $domain;
	}
    
    /**
     * Returns a random string of the length demanded (10 characters by default)
     * @param integer $length
     * @return string
     */
	function getRandomString($length = 10)	{
		$res = array_merge(range('a', 'z'), range('A', 'Z'), range('0', '9'), array("/"));
		$len = mt_rand(0, $length);
		shuffle($res);
		
		return substr(implode($res), 0, $length);
	}
	
    /**
     * Returns a semirandom return code (90% are 200 return code)
     * @return integer
     */
	function getRandomRetourCode()	{
		if (mt_rand(0,9) == 9)	{
			return $this->listOfRetourCodes[array_rand($this->listOfRetourCodes)];
		}
		else
			{
			return 200;
		}
	}
	
    /**
     * Return a random size between 0 and 50K
     * @return integer
     */
	function getRandomSize()	{
		return mt_rand(0,50*1024);
	}
    
}
 
?>