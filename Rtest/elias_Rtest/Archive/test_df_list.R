rm(list=ls())
graphics.off()

library(dplyr)

# functions used are read from a different file
source("qpcr_functions.R")

# read file
qpcr <- tibble(read.csv("in_process.txt", header = T, sep=" "))
#qpcr <- tibble(read.csv("verification.txt", header = T, sep=" "))

#{replace later with input}
house <- "elfa"
ctrl_name <- "ctrl"

# listing the runs in the read file
runs <- unique(qpcr$run)
runs <- runs[!(runs %in% NA)]
qpcr_list <- list()

# for each run return FGE by sample
for (i in 1:length(runs)) {
  single_run_data <- qpcr %>%
    filter(run == runs[i])
  
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
  indx_dlt_ct <- (tail(indx_ct, 1)+1):(tail(indx_ct, 1) + nr_stress)
  indx_deltaX2 <- (tail(indx_dlt_ct, 1)+1):(tail(indx_dlt_ct, 1) + nr_stress)
  indx_FGE <- (tail(indx_deltaX2, 1)+1):(tail(indx_deltaX2, 1) + nr_stress)
  
  # calculating average delta ct for control population
  avg_A_ctrl <- get_avg_A_ct_ctrl(single_run_data, stress, nr_stress, indx_dlt_ct)
  # generate delta-delta ct (AA_ct)
  single_run_data <- gen_AA_ct(single_run_data, stress, nr_stress, avg_A_ctrl, indx_dlt_ct)
  # generate FGE
  single_run_data <- gen_FGE(single_run_data, stress, nr_stress, indx_FGE)
  
  # add dataframe to list of dataframes
  
  # keep only relevant columns
  single_run_data <- keep_relevant_columns(single_run_data)
  
  qpcr_list <- append(qpcr_list, list(single_run_data))
}


# performing full joins on data from every run
qpcr <- qpcr_list[[1]]
if (length(qpcr_list) > 1) {
  for (i in 2:length(runs)) {
    qpcr <- qpcr %>%
      full_join(qpcr_list[[i]])
  }
}

# calculating mean FGE for population, for every gene
out <- qpcr %>%
  select(Population, starts_with("FGE_")) %>%
  group_by(Population) %>%
  summarize_all("mean", na.rm=T)

print(out)
#write.table(out, "in_map.txt", row.names = FALSE, quote=FALSE)













