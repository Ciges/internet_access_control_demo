/**
 *  Test 9.1:  MongoDB analyse query:  Gets the 10 domains most visited and the number of visits for each one.
 *  
 *  This queries are done over the InternetAccessLogs database directly, which contains example data for 90 millions of records for Non FTP access logs and 4,5 millions of record for FTP access logs
 *  This script makes part of a list of scripts to compare MySQL 5.0.26 with MyISAM tables versus MongoDB 2.2.0rc0.
 *  This script must be run with:
 *    mongo -u mongodb -p mongodb localhost/InternetAccessLog test9_1_mongo.js
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20120827
 */
 
db.NonFTP_Access_log.aggregate(
    { $group: {
        _id: "$domain",
        visits: { $sum: 1 }
        }},
    { $sort: { visits: -1 } },
    { $limit: 10 }
    ).result.forEach(printjson);

