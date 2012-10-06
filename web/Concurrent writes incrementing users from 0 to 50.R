# Change the PATH of csv files to yours
loadtest_mongodb <- "C:/User/E198869/Documents/MongoDB/MongoDB_MySQL_loadtests/3 Concurrent writes incrementing users from 0 to 50 - MongoDB.csv"
loadtest_mysql <- "C:/User/E198869/Documents/MongoDB/MongoDB_MySQL_loadtests/3 Concurrent writes incrementing users from 0 to 50 - MySQL.csv"

# fields: timestamp, elapsed, bytes
data_mongodb_csv <- read.csv(loadtest_mongodb, header=T)
data_mysql_csv <- read.csv(loadtest_mysql, header=T)

# fields in data frame: ts (timestamp), rt (response time)
data_mongodb_df <- data.frame(ts=data_mongodb_csv$timeStamp, rt=data_mongodb_csv$elapsed)
data_mysql_df <- data.frame(ts=data_mysql_csv$timeStamp, rt=data_mysql_csv$elapsed)

data_mongodb <- aggregate(data_mongodb_df$rt, by = list(ts=data_mongodb_df$ts), FUN="mean")
data_mysql <- aggregate(data_mysql_df$rt, by = list(ts=data_mysql_df$ts), FUN="mean")

# graphical comparison
rt_mysql <-data.frame(second=c(1:length(data_mysql$x)), rt=data_mysql$x)
plot(rt ~ second,
	data=rt_mysql,
	xlab="Second",
	ylab="Response time mean in ms",
	type="p",
	col="darkblue")	
title(main="Concurrent writes incrementing users from 0 to 50")
mtext("Incrementing by five each five seconds. Each thread is kept for 10 minutes", line=0.5, cex=0.8)
loess_mysql <- loess(rt ~ second, data=rt_mysql)
lines(loess_mysql$x, loess_mysql$fitted, col="darkblue", lwd=2)

rt_mongodb <-data.frame(second=c(1:length(data_mongodb$x)), rt=data_mongodb$x)
points(data_mongodb$x, col="darkgreen")
loess_mongodb <- loess(rt ~ second, data=rt_mongodb)
lines(loess_mongodb$x, loess_mongodb$fitted, col="darkgreen", lwd=2)
legend("topleft", inset=.05, c("MySQL", "MongoDB"), bty="n", fill=c("darkblue", "darkgreen"))

