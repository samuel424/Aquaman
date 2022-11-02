# calculates p-values from FGE input. writes p-values to file

rm(list = ls())
graphics.off()

source("qpcr_AvgACt2FGE.R")
rm(list=setdiff(ls(), c("qpcr_list", "stress_list", "ctrl_name", "gen_colset", "gen_log_FGE")))

library(stats)

loglist <- list()
for (i in 1:length(qpcr_list)) {

  qpcr <- qpcr_list[[i]]
  stress <- stress_list[[i]]

  # getting the new column indices
  indx_ct <- 4:(4+length(stress)-1)
  indx_dlt_ct <- (tail(indx_ct, 1)+1):(tail(indx_ct, 1) + length(stress))
  indx_deltaX2 <- (tail(indx_dlt_ct, 1)+1):(tail(indx_dlt_ct, 1) + length(stress))
  indx_FGE <- (tail(indx_deltaX2, 1)+1):(tail(indx_deltaX2, 1) + length(stress))
  indx_log10 <- (tail(indx_FGE, 1)+1):(tail(indx_FGE, 1) + length(stress))
  qpcr <- gen_log_FGE(qpcr, stress, indx_log10)
  
  loglist <- append(loglist, list(qpcr))
}

qpcr_list <- loglist

# performing full joins on data from every run
qpcr <- qpcr_list[[1]]
if (length(qpcr_list) > 1) {
  for (i in 2:length(qpcr_list)) {
    qpcr <- qpcr %>%
      select(Population, starts_with("log10_")) %>% # removes irrelevant columns, here and on the row below
      full_join(qpcr_list[[i]] %>% select(Population, starts_with("log10_"))) %>%
      arrange(Population)
  }
} else {
  qpcr <- qpcr %>%
    select(Population, starts_with("log10_"))
}

# creating vector of populations excluding ctrl
not_ctrl <- unique(qpcr$Population) 
not_ctrl <- not_ctrl[!(not_ctrl %in% NA) & !(not_ctrl %in% ctrl_name)]

heading <- names(qpcr)[!(names(qpcr) %in% "Population")]

# log10 columns belonging to populations excluding ctrl
not_ctrl_log10 <- list()
for (i in 1:length(not_ctrl)) {
  log10_values_by_pop <- qpcr %>%
    filter(Population == not_ctrl[i])
  not_ctrl_log10 <- append(not_ctrl_log10, list(log10_values_by_pop))
}

# log10 columns belonging to ctrl
log10_values_ctrl <- qpcr %>%
  filter(Population == ctrl_name) 

# create new heading for p-value output
column_names <- c("Population")
for (i in 1:length(heading)) {
  column_names <- append(column_names, sub(pattern = "log10", replacement = "p", heading[i]))
}

# creating output dataframe filled with NA
empty <- rep(NA, length(not_ctrl))
out <- data.frame(not_ctrl)
for (i in 1:length(heading)) {
  out <- data.frame(out, empty)
}
names(out) <- column_names

# replacing NA values with calculated p-values where applicable
for (i in 1:length(not_ctrl)) {
  for (j in 1:length(heading)) {
    in_ctrl <- not_ctrl_log10[[i]][heading[j]]
    in_not_ctrl <- log10_values_ctrl[heading[j]]
    if (length(in_ctrl[!is.na(in_ctrl)]) < 1 | length(in_not_ctrl[!is.na(in_not_ctrl)]) < 1) {
      next
    } else {
      p_value <- t.test(in_ctrl, in_not_ctrl)$p.value
      out[i, j+1] <- p_value
    }
  }
}

# writing file
write.table(out, "qpcr_p-values.txt", row.names = FALSE, quote=FALSE)
