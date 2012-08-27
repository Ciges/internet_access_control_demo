/**
 *  Test 4.6:  MySQL analyse query: gets the 5 domains most visited for each month and the number of visits for each one.
 *  This queries are done over the InternetAccessLogs database directly, which contains example data for 90 millions of records for Non FTP access logs and 4,5 millions of record for FTP access logs
 *  This script makes part of a list of scripts to compare MySQL 5.0.26 with MyISAM tables versus MongoDB 2.2.0rc0.
 *  This script must be run with:
 *    cat test4_6_mysql.sql | mysql -u mysqldb --password="mysqldb" InternetAccessLog
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20120824
 */
 
/* Get the visits grouped by month and domain */
drop table if exists NonFTP_Access_log_month_domain;
create table NonFTP_Access_log_month_domain (
    `month` tinyint unsigned not null,
    `domain` varchar(255) NOT NULL,
    `value` int unsigned not null,
    KEY `month_index` (`month`),
    KEY `value_index` (`value`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8
    select month(datetime) as month, domain, count(*) as value from NonFTP_Access_log group by month, domain;


delimiter $$
drop procedure if exists five_domains_most_visited_by_month$$
create procedure five_domains_most_visited_by_month()
    months_block: begin
        declare month_val tinyint;
        declare no_more_months boolean;
        /* Search the distinct month number stored */
        declare months_cur cursor for
            select distinct(month) from NonFTP_Access_log_month_domain;    
        declare continue handler for not found set no_more_months = TRUE;
            
        open months_cur;
        loop_months: loop
            fetch months_cur into month_val;
            if no_more_months then
                close months_cur;
                leave loop_months;
            end if;

            /* For each month we get the 5 highest visits values */
            visits_block: begin
                declare visits_val int;
                declare no_more_visits boolean;
                declare visits_cur cursor for
                    select distinct(value) from NonFTP_Access_log_month_domain where month=month_val order by value desc limit 5;
                declare continue handler for not found set no_more_visits = TRUE;
                
                open visits_cur;
                loop_visits: loop 
                    fetch visits_cur into visits_val;
                    if no_more_visits then
                        close visits_cur;
                        leave loop_visits;
                    end if;

                /* For each pair month, top value we search and show the month, domain and visits */
                select month, value as visits, domain from NonFTP_Access_log_month_domain where month=month_val and value=visits_val;
                  
                end loop loop_visits;
                  
            end visits_block;
            
        end loop loop_months;

    end months_block$$
delimiter ;

call five_domains_most_visited_by_month();