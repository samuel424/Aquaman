
#cts <- rnorm(nrow(welldata), 25, 3) #Ct vector that you make

#use first row for hk/stess gene info?

#skip first row for dataframe generation
welldata <- read.table('_welldata.txt', skip = 1, header=TRUE, sep = '\t')
welldata$Ct <- cts

#write txt but in csv format
write.csv(welldata, 'ctdata.csv', row.names = FALSE)