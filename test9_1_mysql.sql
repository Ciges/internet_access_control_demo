/**
 *  Test 9.1:  MySQL analyse query:  Gets the 10 domains most visited and the number of visits for each one.
 *  
 *  This queries are done over the InternetAccessLogs database directly, which contains example data for 90 millions of records for Non FTP access logs and 4,5 millions of record for FTP access logs
 *  This script makes part of a list of scripts to compare MySQL 5.0.26 with MyISAM tables versus MongoDB 2.2.0rc0.
 *  This script must be run with:
 *    cat test9_1_mysql.sql | mysql -u mysqldb --password="mysqldb" InternetAccessLog
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20120822
 */
 
/* Counts each domain url */
drop table if exists NonFTP_Access_log_domain_visits;
create table NonFTP_Access_log_domain_visits (
    `domain` varchar(255) NOT NULL,
    `value` int unsigned not null,
    PRIMARY KEY  (`domain`),
    KEY `value_index` (`value`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8
    select domain, count(*) as value from NonFTP_Access_log group by domain;
select * from NonFTP_Access_log_domain_visits order by value desc limit 10;
