# Obtiene una gráfica comparando los tiempos de respuesta de insertar 1 millón de registros con 50 usuarios simultáneos en una tabla MySQL sin particionar y otra particionada (el CSV ha sido exportado desde el JMeter)
# Los valores CSV se le pasan en archivos de texto con dos campos
#

# Nos quedamos sólo con los tiempos de respuesta
csv <- read.csv("test_mysqlpart_50users_20m_write_test4.csv")
csv_unpartitioned <- c(csv$elapsed);
csv <- read.csv("test_mysqlpart_50users_20m_write_test4_PARTITIONED.csv")
csv_partitioned <- c(csv$elapsed);

# Reducimos el millón de valores a 1000 quedándonos con las medianas
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

data_unpartitioned <- data.frame(records = seq(1, 1000), rt = rt_unpartitioned);
plot(rt ~ records,
    data_unpartitioned,
    xlab="Nº de registros insertados (en miles, hasta 1 millón)",
    ylab="Tiempo de respuesta en milisegundos (mediana)",
    type="p",
    col="orange" #
    #ylim=c(0,150)
    )
grid()
title(main="Inserción de 1 millón de registros (50 usuarios)")
mtext("Tablas MyISAM", line=0.5, cex=0.8)
loess_unpartitioned <- loess(rt ~ records, data=data_unpartitioned)
lines(loess_unpartitioned$x, loess_unpartitioned$fitted, col="red", lwd=2)

data_partitioned <- data.frame(records = seq(1, 1000), rt = rt_partitioned);
points(data_partitioned, col="green")
loess_partitioned <- loess(rt ~ records, data=data_partitioned)
lines(loess_partitioned$x, loess_partitioned$fitted, col="darkgreen", lwd=2)

legend("bottomleft", inset=.05, c("Particionada", "No particionada"), bty="n", fill=c("darkgreen", "red"))