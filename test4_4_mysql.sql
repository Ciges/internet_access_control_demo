/**
 *  Test 4.3:  MySQL analyse query:  Gets the 10 domains most visited in second half of august
 * 
 *  This queries are done over the InternetAccessLogs database directly, which contains example data for 90 millions of records for Non FTP access logs and 4,5 millions of record for FTP access logs
 *  This script makes part of a list of scripts to compare MySQL 5.0.26 with MyISAM tables versus MongoDB 2.2.0rc0.
 *  This script must be run with:
 *    cat test4_2_mysql.sql | mysql -u mysqldb --password="mysqldb" InternetAccessLog
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20120824
 */

 select distinct(domain) from NonFTP_Access_log where datetime between "2012-08-15 00:00:00" and "2012-08-29 23:59:59" order by domain;
 