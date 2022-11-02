rm(list = ls())
graphics.off()

qpcr <- tibble(read.table("in_process.txt", header=T, sep=" ", skip=2))
print(qpcr, n=nrow(qpcr))


con <- file("in_process.txt","r")
heading <- readLines(con,n=2)
close(con)

house <- heading[1]
ctrl_name <- heading[2]


print(heading)
print(house)
print(ctrl_name)







