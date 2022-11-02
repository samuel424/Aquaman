# outputs FGE from delta Ct input.

source("qpcr_qpcr2AvgACt.R")
rm(list=setdiff(ls(), c("qpcr_list", "stress_list","ACt", "nr_stress", "runs",
                        "ctrl_name", "gen_colset", "gen_AA_ct", "gen_FGE", "gen_log_FGE")))


avg_ACt_ctrl <- ACt %>%
  filter(Population == ctrl_name) %>%
  select(starts_with("ACt_"))

# calculating average delta ct for control population
AACt_list <- list()
for (i in 1:length(qpcr_list)) {
  stress <- stress_list[[i]]
  nr_stress <- length(stress)
  single_run_data <- qpcr_list[[i]]
  
  # index sets for selecting the right columns that adjust for the nr genes used
  indx_ct <- 4:(4+nr_stress-1)
  indx_A_ct <- (tail(indx_ct, 1)+1):(tail(indx_ct, 1) + nr_stress)
  indx_AA_ct <- (tail(indx_A_ct, 1)+1):(tail(indx_A_ct, 1) + nr_stress)
  indx_FGE <- (tail(indx_AA_ct, 1)+1):(tail(indx_AA_ct, 1) + nr_stress)
  
  single_run_data <- gen_AA_ct(single_run_data, stress, nr_stress, avg_ACt_ctrl, indx_A_ct)

  single_run_data <- gen_FGE(single_run_data, stress, nr_stress, indx_FGE)
  
  # add dataframe to list of dataframes
  AACt_list <- append(AACt_list, list(single_run_data))
}

qpcr_list <- AACt_list


