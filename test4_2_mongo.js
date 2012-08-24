/**
 *  Test 4.2:  MongoDB analyse query:  Gets the 5 domains most visited for each month and the number of visits for each one.
 *  
 *  This queries are done over the InternetAccessLogs database directly, which contains example data for 90 millions of records for Non FTP access logs and 4,5 millions of record for FTP access logs
 *  This script makes part of a list of scripts to compare MySQL 5.0.26 with MyISAM tables versus MongoDB 2.2.0rc0.
 *  This script must be run with:
 *    mongo -u mongodb -p mongodb localhost/InternetAccessLog test4_2_mongo.js
 *
 *  @author José Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20120823
 */
 
/* We use mapreduce to get the visits grouped by month and domain */
m = function () {
    var key = { month: (this.datetime.getMonth()+1), domain: this.domain };
    emit(key, 1 );
}

r = function (key, values)    {
    total = 0;
    for (var i in values)    {
        total += Number(i);
    }
    
    return total;
}

res = db.NonFTP_Access_log.mapReduce(m, r, { out: { replace : "NonFTP_Access_log_month_domain_visits" } } );
db.NonFTP_Access_log_month_domain_visits.ensureIndex({ "value": 1});

/* Search the distinct month number stored */
db.NonFTP_Access_log_month_domain_visits.distinct("_id.month").forEach(function(n) {
    /* For each month we get the 5 highest visits values */
    topvalues = db.NonFTP_Access_log_month_domain_visits.aggregate(
        { $project: { month: "$_id.month", value:1 } },
        { $match: { month: n } },
        { $sort: { value: -1 } },
        { $limit: 5 },
        { $group: { _id: { month:1, value: 1 } } }, /* don't repeat pairs month,value */
        { $project: { _id: 0, value: "$_id.value" } }
    ); 

    /* For each pair month, top value we search and show the month, domain and visits */
    topvalues.result.forEach(function(tv)   {
        db.NonFTP_Access_log_month_domain_visits.aggregate(
            { $project: { month: "$_id.month", value: 1 } },
            { $match: { month: n } },
            { $match: { value: tv.value } },
            { $project: { _id: 0, month: n, domain: "$_id.domain", value: 1 } }
        ).result.forEach(printjson);
    });
});


