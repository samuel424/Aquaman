#! C:\MAMP\bin\R-4.0.3\bin
x <- rnorm(6,0,1)
png(filename="test.png", width=500, height=500)
hist(x, col="orange")