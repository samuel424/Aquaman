
# Script that generates realistic Ct data
# Generates a random normal distribution for every gene in the analysis
# Samples data from the n dist, appends to welldata
# Exports txt file in the correct format


# Import txt file as a data frame
welldata <- read.table('_welldata.txt', skip = 1, header=TRUE, sep = '\t')

# Add empty column for future Ct values
ct_temp <- vector(mode = "numeric", length = nrow(welldata))
welldata$Ct <- ct_temp

# Make a table of targets and number of occurrences 
# (each target occurs equal number of times)
targets <- data.frame(table(welldata$Target))

# target names
t <- targets$Var1

# number of occurences
nt <- targets$Freq

# samples = occurences minus controls
reps = nt[1]-2

# Make a list for storing vectors of Ct values
ctlist = list()

# Generate 4x the needed Ct values for sampling in a vector
# add vector to list
for (x in 1:length(t)){
  mean = runif(1, 22, 30)
  sd = runif(1,0.3,2)
  ct <- rnorm(4*reps, mean, sd)
  ctlist[[x]] <- ct
}

# Generate a vector of Ct values for positive controls
pccts <- rnorm(4*length(t), 25, 0.2)

# Target counter for loop
g <- 0

# For every row, sample a Ct value from the relevant distribution
# Positive control (coming first for every gene) advances target counter
# Update Ct value for the row in the data frame
for(i in 1:nrow(welldata)) {
  row <- welldata[i,]
  if (row[4] == 'Pos Ctrl'){
    row[7] <- sample(pccts, 1)
    g <- g + 1
  }
  if (row[4] == 'Neg Ctrl'){
    row[7] <- 0.00
  }
  if (row[4] == 'Unkn'){
    cts <- ctlist[[g]]
    row[7] <- sample(cts, 1)
  }
  welldata[i,] <- row
}

# Write the data frame to a file
write.table(welldata, file = "complete_ct.txt", sep=',', row.names = FALSE, quote = FALSE)


