/**
 *  Test 4.2:  MongoDB analyse query:  Gets the 10 domains most visited in second half of august
 *  
 *  This queries are done over the InternetAccessLogs database directly, which contains example data for 90 millions of records for Non FTP access logs and 4,5 millions of record for FTP access logs
 *  This script makes part of a list of scripts to compare MySQL 5.0.26 with MyISAM tables versus MongoDB 2.2.0rc0.
 *  This script must be run with:
 *    mongo -u mongodb -p mongodb localhost/InternetAccessLog test4_3_mongo.js
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20120824
 */

/* Counts each url hit */
m = function () {
    emit(this.domain, 1);
}

r = function (key, values)    {
    total = 0;
    for (var i in values)    {
        total += Number(i);
    }
    
    return total;
}
 
var start=new Date(2012, 7, 15);
var end=new Date(2012, 7, 31);
/* Store of visits per domain statistics on NonFTP_Access_log_domain_visits_201208_1531 collection */
/*db.NonFTP_Access_log.mapReduce(m, r, { out: { replace: "NonFTP_Access_log_domain_visits_201208_1531" }, query: { datetime: { $gte: start, $lte: end } } } );
db.NonFTP_Access_log_domain_visits_201208_1531.ensureIndex({ "value": 1});
*/
res = db.NonFTP_Access_log_domain_visits_201208_1531.mapReduce("function() { emit(this.value, 1); }", "function(key, values) { return 1; }", { out: "aux" });
min = db.aux.aggregate(
    { $sort: { _id: -1 } },
    { $limit: 10 },
    { $sort: { _id: 1 } },
    { $limit: 1 },
    { $project: { _id: 1 } }
    ).result;
min;

/* Get the minimal of the 10 highest number of visits */ 
/*minvalue = db.NonFTP_Access_log_domain_visits_201208_1531.aggregate(
    { $project: { _id:0, value:1 } },
    { $group: { _id: { value: 1 } } },*/ /* don't repeat values */
/*    { $project: { _id:0, value:"$_id.value" } },
    { $sort: { value: -1 } },
    { $limit: 10 },*/
    /* From the highest 10 value we get the lowest */
/*    { $sort: { value: 1 } },
    { $limit: 1 }
    ).result.forEach(printjson);
*/    
/* Get the domains with a number of visits betwenn the 10 highest */
/*db.NonFTP_Access_log_domain_visits_201208_1531.aggregate(
    { $match: { month: n } },
    );
*/

