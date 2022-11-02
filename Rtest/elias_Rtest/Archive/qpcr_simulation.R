# notes:
# [1] pos_ctrl should have ct (m=30, sd=2), neg_ctrl should not

#qpcr <- read.table("_welldata.txt", skip=1,header=T)





con <- file("_welldata.txt","r")
house <-  readLines(con,n=1)
close(con)
print(house)
print(header)


read.table("_welldata.txt", header = FALSE, skip=2, sep = "\t", 
             fill = TRUE)























