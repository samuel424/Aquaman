library('tibble')
# function to get p-value from simple regression
lmp <- function (modelobject) {
  if (class(modelobject) != "lm") stop("Not an object of class 'lm' ")
  f <- summary(modelobject)$fstatistic
  p <- pf(f[1],f[2],f[3],lower.tail=F)
  attributes(p) <- NULL
  return(p)
}

# get logarithm argument from exec command and userID for path construction
args<-commandArgs(TRUE)
do_log<-as.numeric(args[1])
noise <- as.numeric(args[2])
user<-args[3]

# construct data path and read gene expression file
datapath <- paste("regdata", user, "/", sep="")
fgefile <- paste(datapath, "qpcr_map_FGE.txt", sep = "")
fge <- tibble(read.table(fgefile, header=T, sep=" "))

# convert to logarithm if requested
if (do_log == 1){
  logtag <- 'log10_'
  lognames = c(colnames(fge)[1])
  for (i in 2:ncol(fge)){
    fge[,i] <- log10(fge[,i])
    newcolname <- paste(logtag, colnames(fge)[i], sep="")
    lognames <- c(lognames, newcolname)
  }
  colnames(fge) <- lognames
}else{
  logtag <- ''
}

# round the numbers
for (i in 2:ncol(fge)){
  fge[,i] <- round(fge[,i], digits = 6)
}

# read chemical data
chemfile <- paste(datapath, "_chem_data.txt", sep = "")
chem <- tibble(read.table(chemfile, header=T, sep="\t"))

# merge chemical and gene data
combined <- merge(chem, fge, by = "Population")

#reorder columns if noise
if (noise){
  combined <- combined[, c(1, 2, 6:ncol(combined), 3:5)]
  fge_start = 6
  extracols = 3
} else{
  fge_start = 3
  extracols = 0
}


# output table for later display in website
outfile <- paste(datapath, "results/graph_data.txt", sep = "")
write.table(combined, file = outfile, sep=',', row.names = FALSE, quote = FALSE)

# get chemical name 
chemname <- colnames(combined)[2]

# prepare result matrix
result <- data.frame(matrix(ncol = 6, nrow = 0))
colnames(result) <- c('Gene', 'Intercept', 'Slope', 'r-squared', 'pvalue', 'png_filename')

# do a regression analysis for each gene
for (i in 3:(ncol(combined)-extracols)){
  
  # construct gene tag (log + name)
  mname <- colnames(combined)[i]
  
  # do the regression
  model <- lm(combined[,i] ~ combined[,2], data=combined)
  
  # get regression line coefficients
  coeff <- round(as.numeric(model[[1]]), digits = 3)
  
  # get the rsquared value
  rsq <- round(summary(model)$r.squared, digits = 6)
  
  # get the pvalue
  pvalue <- round(lmp(model), digits = 6)
  
  # construct plot file name
  plotname <- paste('plot_', mname, '.png', sep = '')
  plotfile <- paste(datapath, 'results/', plotname, sep="")
  
  # add result to results matrix
  result[nrow(result)+1,] = c(mname,coeff[1], coeff[2], rsq, pvalue, plotfile)
  
  # output png file
  png(plotfile, width = 600, height = 400)
  plot(combined[,2], combined[,i], pch = 16, col = "blue", xlab = chemname, ylab = mname)
  abline(model)
  graphics.off()
}

# output results matrix
matrixfile <- paste(datapath, "results/lm_coeff.txt", sep="")
write.table(result, file = matrixfile, sep='\t', row.names = FALSE, quote = FALSE)

