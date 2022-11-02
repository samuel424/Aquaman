# calculates p-value between samples based on qPCR and other DB input


# to do:
# generate realistic data for testing purposes.
# [1] number of different stress genes should vary
# [2] which gene is the house keeping gene should vary
# [3] every example (meaning row) should be assigned to one of two populations
# [4] not all wells must be included in the analysis
# [5] number of samples can vary
# [6] number of replicates from each sample can vary
# assumptions that are allowed
# [1] the experiment is one test population agains one control population
# [2] for now i am assuming that every time you want to find out if a population
#     is stressed you run a new control group together with the test group and 
#     there is no option to compare to previously existing control group data


# update read function when qpcr data can be extracted from the DB

# delete when the rest is finished
rm(list=ls())
graphics.off()

library(ggplot2)
library(dplyr)
library(stats)

raw_qpcr <- read.csv("example_data.csv", header = T)

qpcr_pre_divide <- raw_qpcr %>%
  # remove junk
  filter(!Content == "Neg Ctrl", !Cq==is.na(Cq)) %>%
  select(Sample, Target, Cq) %>%
  # get replicate average
  group_by(Target, Sample) %>%
  summarize(cq_rep_mean = mean(Cq)) %>%
  # faking population origin for now because db is not ready to input this yet
  mutate(origin = ifelse(cq_rep_mean > mean(cq_rep_mean), "farmed", "wild"))

# splitting the dataframe based on gene name and performing equivalent
# calculations on each
divide_house <- qpcr_pre_divide %>%
  filter(Target == "wsp") %>%
  arrange(Sample) %>%
  transmute(sample = Sample,
            origin = origin,
            cq_house = cq_rep_mean,
            house = Target)

divide_stress <- qpcr_pre_divide %>%
  filter(Target == "RpL32") %>%
  arrange(Sample) %>%
  transmute(sample = Sample,
            origin = origin,
            cq_stress = cq_rep_mean,
            stress = Target)

qpcr <- data.frame(divide_stress, divide_house) %>%
  select(sample, origin, cq_house, cq_stress)

# delta-delta-ct method
# normalization
# qpcr <- read.table("delta_ct_test.txt", header = TRUE)

# delta_ct
qpcr_delta_cq <- qpcr %>%
  mutate(delta_cq = cq_stress - cq_house)

# Average of the control samples (farmed fish)
avg_delta_cq <- qpcr_delta_cq %>%
  filter(origin == "farmed")
avg_delta_cq <- mean(avg_delta_cq['delta_cq'][, 1])

# Calculating the Ct relative to the average of Ct normal cells
# aka calculating delta-delta ct and fold gene expression
qpcr_deltaX2_FGE <- qpcr_delta_cq %>%
  mutate(deltaX2_cq = delta_cq - avg_delta_cq) %>%
  # fold gene expression each sample
  mutate(fold_gene_expression = 2^-deltaX2_cq)

# average wild
avg_wild <- qpcr_deltaX2_FGE %>%
  filter(origin == "wild")
avg_wild <- mean(avg_wild['fold_gene_expression'][, 1])

# average farmed
avg_farmed <- qpcr_deltaX2_FGE %>%
  filter(origin == "farmed")
avg_farmed <- mean(avg_farmed['fold_gene_expression'][, 1])

# fold change
avg_change_wild_div_farmed = avg_wild / avg_farmed

qpcr_log10 <- qpcr_deltaX2_FGE %>%
  mutate(log_10 = log10(fold_gene_expression))

# extracting 'x' input for t-test
tt_wild <- qpcr_log10 %>%
  filter(origin == "wild")
tt_wild <- tt_wild['log_10'][, 1]

# extracting 'y' input for t-test
tt_farmed <- qpcr_log10 %>%
  filter(origin == "farmed")
tt_farmed <- tt_farmed['log_10'][, 1]

# performing t-test and extracting p-value
p_value <- t.test(tt_wild, tt_farmed)$p.value

#print(raw_qpcr)
#print(qpcr_pre_divide, n = nrow(qpcr_pre_divide))
#print(qpcr_pre_divide)
#print(divide_house)
#print(divide_stress)

print(qpcr)
#print(qpcr_delta_cq)
#print(qpcr_deltaX2_FGE)
#print(avg_delta_cq)
#print(avg_wild)
#print(avg_farmed)
#print(avg_change_wild_div_farmed)
#print(qpcr_log10)
#print(tt_wild)
#print(tt_farmed)
#print(p_value)

# creating figure
fig <- ggplot(qpcr_log10, aes(log_10)) +
  geom_histogram(bins = 50, color="black", fill="blue") +
  theme_classic()

# senting figure back to php file
png("graph.png", width = 600, height = 400)
print(fig)
dev.off()

