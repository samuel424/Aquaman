rm(list=ls())
graphics.off()

library(dplyr)
library(stats)

# to do:
# [1] figure out the format of the requiered input from DB and simulation
# [2] avg_delta_ct for all populations, not just control. output as 
#     avg_delta_ct_pop2^-(avg_delta_ct_pop)

# generates columns to fill with calculations i.e, dlt_ct, dlt2_ct, FGE or log 
gen_colset <- function(qpcr, stress, prefix){
  qpcr_names <- names(qpcr)
  empty <- rep(NA, nrow(qpcr))
  col_names <- c()
  for (i in 1:length(stress)) {
    col_names <- c(col_names, paste(prefix, stress[i], sep=""))
  }
  for (i in 1:length(stress)) {
    qpcr <- data.frame(qpcr, empty)
  }
  names(qpcr) <- c(qpcr_names, col_names)
  return(qpcr)
}


# read file
qpcr1 <- tibble(read.csv("qpcr_data.txt", header = T, sep=" "))

##############################
# other requiered input
house <- "elfa"

# when the house gene is known this assigns the other genes as stress genes
stress <- unique(qpcr1$Gene)
stress <- stress[!(stress %in% house | stress %in% NA)]






# index sets for selecting the right columns that adjust for the nr genes used
indx_ct <- 4:(4+length(stress)-1)
indx_dlt_ct <- (tail(indx_ct, 1)+1):(tail(indx_ct, 1) + length(stress))
indx_deltaX2 <- (tail(indx_dlt_ct, 1)+1):(tail(indx_dlt_ct, 1) + length(stress))
indx_FGE <- (tail(indx_deltaX2, 1)+1):(tail(indx_deltaX2, 1) + length(stress))
indx_log10 <- (tail(indx_FGE, 1)+1):(tail(indx_FGE, 1) + length(stress))


qpcr2 <- qpcr1 %>%
  # filtering out rows containing NA
  filter(!is.na(Ct)) %>%
  # average replicates defined by population, gene (and sample)
  group_by(Sample, Gene, Population) %>%
  summarize(Ct_mean = mean(Ct))

print(qpcr2)

qpcr <- qpcr2 %>%
  filter(Gene == house)

print(qpcr)

for (i in 1:length(stress)) {
  data <- qpcr2 %>%
    filter(Gene == stress[i])
  qpcr <- data.frame(qpcr, data) %>%
    select(!Sample.1) %>%
    select(!Gene.1) %>%
    select(!Population.1)
}



# removing house gene column
qpcr <- qpcr %>%
  select(!Gene)

print(qpcr)

# naming ct-columns after their stress genes
ct_names <- c()
for (i in 1:length(stress)) {
  ct_names <- c(ct_names, paste("Ct_", stress[i], sep=""))
}
col_names <- c("Sample", "Population", "Ct_house", ct_names)
names(qpcr) <- col_names

# generating columns for calculations
qpcr <- gen_colset(qpcr, stress, "delta_")
qpcr <- gen_colset(qpcr, stress, "deltaX2_")
qpcr <- gen_colset(qpcr, stress, "FGE_")
qpcr <- gen_colset(qpcr, stress, "log10_")

print(qpcr)
# calculating delta ct values
for (i in 1:length(stress)) {
  qpcr[3+length(stress)+i] <- qpcr[,3+i]-qpcr[,3]
}

print(qpcr)

# Average delta_ct for all populations, all stress genes. some are for further
# calculations, others for output for map
pop <- unique(qpcr$Population)
avg_A_pop0 <- matrix(rep(NA, length(stress)*length(pop)), nrow=length(pop), ncol=length(stress))
for (i in 1:length(pop)) {
  avg_A_pop <- qpcr %>%
    filter(Population == pop[i])
  avg_A_pop0[i, 1:length(stress)] <- colMeans(avg_A_pop[indx_ct]) 
}
rownames(avg_A_pop0) <- pop
colnames(avg_A_pop0) <- stress

# extracting avg_A that will be used for fucther calculations
ctrl_name <- "ctrl" #later update to be input to file
avg_A_ctrl <- avg_A_pop0[ctrl_name, ]  


# delta_delta_ct
for (i in indx_dlt_ct) {
  qpcr[i+length(stress)] <- qpcr[i]-avg_A_ctrl[i+1-indx_dlt_ct[1]]
}

# fold gene expression by sample
for (i in indx_FGE) {
  qpcr[i] <- 2^-(qpcr[,i-length(stress)])
}


# overall fold change
avg_FGE_pop0 <- matrix(rep(NA, length(stress)*length(pop)), nrow=length(pop), ncol=length(stress))
for (i in 1:length(pop)) {
  avg_FGE_pop <- qpcr %>%
    filter(Population == pop[i])
  avg_FGE_pop0[i, 1:length(stress)] <- colMeans(avg_FGE_pop[indx_FGE]) 
}
rownames(avg_FGE_pop0) <- pop
colnames(avg_FGE_pop0) <- stress 

avg_FGE_ctrl <- avg_FGE_pop0[ctrl_name, ]
# fold change by populations
fold_A_pop0 <- matrix(rep(NA, length(stress)*(length(pop))), nrow=length(pop), ncol=length(stress))
for (i in 1:length(pop)) {
    fold_A_pop0[i, ] = avg_FGE_pop0[i, ] / avg_FGE_ctrl
}
fold_A_pop0 <- data.frame(fold_A_pop0)
rownames(fold_A_pop0) <- pop
colnames(fold_A_pop0) <- stress 
fold_A_pop0 <- fold_A_pop0[!(row.names(fold_A_pop0) %in% "ctrl"), ]

# log10 transformation
for (i in indx_log10) {
  qpcr[, i] <- log10(qpcr[,i-length(stress)])
}

# extracting input for ttest



print(qpcr)

print(avg_A_pop0)
print(avg_A_ctrl)
print(avg_FGE_pop0)
print(avg_FGE_ctrl)
print(fold_A_pop0)



