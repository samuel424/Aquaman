library(dplyr)
# this file must be replaced by qpcr_qpcr2AvgACt.R and AvgACt2FGE.R


# functions used are read from a different file
source("qpcr_functions.R")

# read file
read_file_path <- "in_process_old.txt"
qpcr <- tibble(read.table(read_file_path, header=T, sep=" ", skip=2))

# assigning house keeping gene and 'control' population
con <- file(read_file_path,"r")
heading <- readLines(con,n=2)
close(con)
house <- heading[1]
ctrl_name <- heading[2]

# listing the runs in the read file
runs <- unique(qpcr$Run)
runs <- runs[!(runs %in% NA)]

# keeping track of run-related info
qpcr_list <- list()
stress_list <- list()

# for each run return FGE by sample
for (i in 1:length(runs)) {
  single_run_data <- qpcr %>%
    filter(Run == runs[i])
  
  # when the house gene is known this assigns the other genes as stress genes
  stress <- unique(single_run_data$Gene)
  stress <- stress[!(stress %in% house | stress %in% NA)]
  nr_stress <- length(stress)
  
  # restructure data for computation
  single_run_data <- restructure(single_run_data, stress, nr_stress)
  # calculate A_ct
  single_run_data <- gen_A_ct(single_run_data, stress, nr_stress)
  
  # index sets for selecting the right columns that adjust for the nr genes used
  indx_ct <- 4:(4+nr_stress-1)
  indx_A_ct <- (tail(indx_ct, 1)+1):(tail(indx_ct, 1) + nr_stress)
  indx_AA_ct <- (tail(indx_A_ct, 1)+1):(tail(indx_A_ct, 1) + nr_stress)
  indx_FGE <- (tail(indx_AA_ct, 1)+1):(tail(indx_AA_ct, 1) + nr_stress)
  
  # calculating average delta ct for control population
  avg_A_ctrl <- get_avg_A_ct_ctrl(single_run_data, stress, nr_stress, indx_A_ct)
  # generate delta-delta ct (AA_ct)
  single_run_data <- gen_AA_ct(single_run_data, stress, nr_stress, avg_A_ctrl, indx_A_ct)
  # generate FGE
  single_run_data <- gen_FGE(single_run_data, stress, nr_stress, indx_FGE)
  
  # add dataframe to list of dataframes
  qpcr_list <- append(qpcr_list, list(single_run_data))
  stress_list <- append(stress_list, list(stress))
}