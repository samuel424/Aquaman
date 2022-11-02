# output two dataframes from input of qprc data from multiple runs:
# [1] average delta cts
# [2] FGE
# these calculations are based on a single data frame constructed with data from
# multiple runs where genes define columns, samples are listed rows where each
# sample can be listed multiple times.
# in the output dataframes the genes still define the columns but the rows are
# now also defined by the sample meaning that the delta values of the genes must
# be averaged based on sample.

# ???????????????????????????????????????????????????????????????????????
# [1] figure out the format of the requiered input from DB and simulation
# [2] avg_delta_ct for all populations, not just control. output as 
#     avg_delta_ct_pop2^-(avg_delta_ct_pop)

rm(list=ls())
graphics.off()

library(dplyr)
library(stats)

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

# pre-processes and restructures data from input file for computation
restructure <- function(qpcr, stress, nr_stress) {
  qpcr2 <- qpcr %>%
    # filtering out rows containing NA
    filter(!is.na(Ct)) %>%
    # average replicates defined by population, gene (and sample)
    group_by(Sample, Gene, Population) %>%
    summarize(Ct_mean = mean(Ct))
  
  qpcr <- qpcr2 %>%
    filter(Gene == house)
  
  for (i in 1:nr_stress) {
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
  
  #naming ct-columns after their stress genes
  ct_names <- c()
  for (i in 1:nr_stress) {
    ct_names <- c(ct_names, paste("Ct_", stress[i], sep=""))
  }
  col_names <- c("Sample", "Population", "Ct_house", ct_names)
  names(qpcr) <- col_names
  
  return(qpcr)
} 

gen_A_ct <- function (qpcr, stress, nr_stress, prefix) {
  # generating columns
  qpcr <- gen_colset(qpcr, stress, "delta_")
  # calculating delta ct values
  for (i in 1:nr_stress) {
    qpcr[3+nr_stress+i] <- qpcr[,3+i]-qpcr[,3]
  }
  return(qpcr)
}

get_avg_A_ct_ctrl <- function(qpcr, stress, nr_stress, indx) {
  # Average delta_ct for all populations, all stress genes
  pop <- unique(qpcr$Population)
  print(pop)
  avg_A_pop0 <- matrix(rep(NA, nr_stress*length(pop)), nrow=length(pop), ncol=nr_stress)
  for (i in 1:length(pop)) {
    avg_A_pop <- qpcr %>%
      filter(Population == pop[i])
    avg_A_pop0[i, 1:nr_stress] <- colMeans(avg_A_pop[indx]) 
  }
  rownames(avg_A_pop0) <- pop
  colnames(avg_A_pop0) <- stress
  
  # extracting avg_A that will be used for further calculations
  avg_A_ctrl <- avg_A_pop0[ctrl_name, ]
  return(avg_A_ctrl)
}

gen_AA_ct <- function(qpcr, stress, nr_stress, avg_A_ctrl, indx) {
  # generating columns
  qpcr <- gen_colset(qpcr, stress, "deltaX2_")
  # delta_delta_ct
  for (i in indx_dlt_ct) {
    qpcr[i+nr_stress] <- qpcr[i]-avg_A_ctrl[i+1-indx[1]]
  }
  return(qpcr)
}

gen_FGE <- function(qpcr, stress, nr_stress, indx) {
  # generating columns
  qpcr <- gen_colset(qpcr, stress, "FGE_")
  # fold gene expression by sample
  for (i in indx) {
    qpcr[i] <- 2^-(qpcr[,i-nr_stress])
  }
  return(qpcr)
}



# read file
qpcr <- tibble(read.csv("AvgDeltaCt_n_FGE_test.txt", header = T, sep=" "))
#qpcr <- tibble(read.csv("verification.txt", header = T, sep=" "))
reassign_pop <- FALSE
reassign_pop <- TRUE

################################################################
##############{replace later with input}########################
# we will ignore the population assignment done earlier to the data. but here we
# will need information about groupings of samples into populations and which of
# those groups is the 'ctrl' group until this input is ready we simulate a place
# holder

if (reassign_pop) {
  qpcr <- qpcr %>%
    select(!Population)
  
  sample_list <- unique(qpcr$Sample)
  sample_list <- sample_list[!(sample_list %in% "H20" | sample_list %in% NA)]
  nr_sample <- length(sample_list)
  ctrl_group <- sample_list[1:(nr_sample/2)]
  test_group <- sample_list[(nr_sample/2+1):nr_sample]
  
  # assigning samples their population
  Population <- rep(NA, nrow(qpcr))
  ctrl_indices <- c()
  test_indices <- c()
  
  # assumes an even number of different samples
  for (i in 1:length(sample_list)) {
    ctrl_indices <- append(ctrl_indices, which(qpcr$Sample == ctrl_group[i]))
    test_indices <- append(test_indices, which(qpcr$Sample == test_group[i]))
  }
  
  Population[ctrl_indices] = "ctrl"
  Population[test_indices] = "test"
  qpcr <- tibble(qpcr, Population)
}


# other requiered input
house <- "elfa"
ctrl_name <- "ctrl"

##############{replace later with input}########################
################################################################

runs <- unique(qpcr$run)
runs <- runs[!(runs %in% NA)]

qpcr <- qpcr %>%
  filter(run == runs[1]) 

# when the house gene is known this assigns the other genes as stress genes
stress <- unique(qpcr$Gene)
stress <- stress[!(stress %in% house | stress %in% NA)]
nr_stress <- length(stress)

# restructure data for computation
qpcr <- restructure(qpcr, stress, nr_stress)
# calculate A_ct
qpcr <- gen_A_ct(qpcr, stress, nr_stress)

# index sets for selecting the right columns that adjust for the nr genes used
indx_ct <- 4:(4+nr_stress-1)
indx_dlt_ct <- (tail(indx_ct, 1)+1):(tail(indx_ct, 1) + nr_stress)
indx_deltaX2 <- (tail(indx_dlt_ct, 1)+1):(tail(indx_dlt_ct, 1) + nr_stress)
indx_FGE <- (tail(indx_deltaX2, 1)+1):(tail(indx_deltaX2, 1) + nr_stress)

# calculating average delta ct for control population
avg_A_ctrl <- get_avg_A_ct_ctrl(qpcr, stress, nr_stress, indx_dlt_ct)

qpcr <- gen_AA_ct(qpcr, stress, nr_stress, avg_A_ctrl, indx_dlt_ct)
qpcr <- gen_FGE(qpcr, stress, nr_stress, indx_FGE)
                         
print(qpcr)
