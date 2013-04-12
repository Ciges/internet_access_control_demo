# Obtiene una gráfica comparando los tiempos de respuesta de insertar 60 millones de registros en una tabla MySQL sin particionar y otra particionada.
# Los valores CSV se le pasan en archivos de texto con dos campos
#

# Leemos los datos correspondientes a la inserción de 60 millones de registros
# En estos ficheros CSV cada entrada corresponde al tiempo en milisegundos usado por cada 100 inserciones
csv10MM <- read.csv("unpartitioned_test2/test_mysqlpart_insert_10M_test2_unpartitioned.csv")
csv20MM <- read.csv("unpartitioned_test2/test_mysqlpart_insert_20M_test2_unpartitioned.csv")
csv30MM <- read.csv("unpartitioned_test2/test_mysqlpart_insert_30M_test2_unpartitioned.csv")
# Nos quedamos sólo con los tiempos de respuesta
csv_unpartitioned <- c(csv10MM$elapsed, csv20MM$elapsed, csv30MM$elapsed);
csv10MM <- read.csv("partitioned_test2/test_mysqlpart_insert_10M_test2_partitioned.csv")
csv20MM <- read.csv("partitioned_test2/test_mysqlpart_insert_20M_test2_partitioned.csv")
csv30MM <- read.csv("partitioned_test2/test_mysqlpart_insert_30M_test2_partitioned.csv")
csv_partitioned <- c(csv10MM$elapsed, csv20MM$elapsed, csv30MM$elapsed);

# Reducimos los 600.000 valores a 600 quedándonos con las medianas
aux <- matrix(csv_unpartitioned,nrow=1000)
rt_unpartitioned <- colMeans(aux)
aux <- matrix(csv_partitioned,nrow=1000)
rt_partitioned <- colMeans(aux)

# Representación gráfica (cada valor representa 100.000 inserciones)

# Usamos Cairo para una mayor calidad de la imagen y el texto
library("Cairo")
CairoX11(display=Sys.getenv("DISPLAY"), width = 7, height = 7,
         pointsize = 20, gamma = getOption("gamma"), bg = "transparent",
         canvas = "white", xpos = NA, ypos = NA)

data_unpartitioned <- data.frame(records = seq(100, 60000, 100), rt = rt_unpartitioned);
plot(rt ~ records,
     data_unpartitioned,
     xlab="Nº de registros insertados (en miles, hasta 60 millones)",
     ylab="Tiempo de respuesta en milisegundos (mediana)",
     type="p",
     col="orange",
     ylim=c(60,100)
     )
grid()
title(main="Inserción de 60 millones de registros")
mtext("Tablas MyISAM", line=0.5, cex=0.8)
loess_unpartitioned <- loess(rt ~ records, data=data_unpartitioned)
lines(loess_unpartitioned$x, loess_unpartitioned$fitted, col="red", lwd=2)

data_partitioned <- data.frame(records = seq(100, 60000, 100), rt = rt_partitioned);
points(data_partitioned, col="green")
loess_partitioned <- loess(rt ~ records, data=data_partitioned)
lines(loess_partitioned$x, loess_partitioned$fitted, col="darkgreen", lwd=2)

legend("bottomleft", inset=.05, c("Particionada", "No particionada"), bty="n", fill=c("darkgreen", "red"))