rm(list = ls())
graphics.off()

source("test_df_list.R")
qpcr1 <- qpcr_list[[1]]
qpcr2 <- qpcr_list[[2]]
qpcr3 <- qpcr_list[[3]]
qpcr4 <- qpcr_list[[4]]
qpcr5 <- qpcr_list[[5]]
qpcr6 <- qpcr_list[[6]]

qpcr <- qpcr1 %>%
  full_join(qpcr2) %>%
  full_join(qpcr3) %>%
  full_join(qpcr4) %>%
  full_join(qpcr5) %>%
  full_join(qpcr6) %>%
  arrange(Sample)
  
print(qpcr_list)
print(qpcr)

