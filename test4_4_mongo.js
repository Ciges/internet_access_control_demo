/**
 *  Test 4.4:  MongoDB analyse query:  Gets the 10 users with most Internet use (in hits number)
 *  
 *  This queries are done over the InternetAccessLogs database directly, which contains example data for 90 millions of records for Non FTP access logs and 4,5 millions of record for FTP access logs
 *  This script makes part of a list of scripts to compare MySQL 5.0.26 with MyISAM tables versus MongoDB 2.2.0rc0.
 *  This script must be run with:
 *    mongo -u mongodb -p mongodb localhost/InternetAccessLog test4_4_mongo.js
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20120824
 */
 
/* Counts each user hit */
m = function () {
    emit(this.user, 1 );
}

r = function (key, values)    {
    total = 0;
    for (var i in values)    {
        total += Number(i);
    }
    
    return total;
}

/* Store of visits per user statistics on NonFTP_Access_log_users_visits collection */
res = db.NonFTP_Access_log.mapReduce(m, r, { out: { replace : "NonFTP_Access_log_users_visits" } } );
db.NonFTP_Access_log_users_visits.ensureIndex({ "value": 1});

/* Gets the 10 highest values for the user hits */
topvalues = db.NonFTP_Access_log_users_visits.aggregate(
    { $project: { _id:0, value:1 } },
    { $group: { _id: { value: 1 } } }, /* don't repeat values */
    { $sort: { value: -1 } },
    { $limit: 10 },
    { $project: { _id: 0, value: "$_id.value" } }
    );
    
