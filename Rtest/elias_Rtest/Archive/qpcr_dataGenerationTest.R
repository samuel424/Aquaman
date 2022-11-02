# testing generation of realistic test data for purposes

# assumptions
# [1] the nr samples of a given type, e.g sample_1 is always even due to the fact
#     that each has as many 'test' samples as 'control' samples and that there is
#     always only two populations

rm(list = ls())
graphics.off()

library(dplyr)

# functions

# generates wells of a 96 qpcr plate
gen_wells <- function(){
  nr_columns <- 12
  Well <- rep(0, 8 * nr_columns)
  for (i in 1:nr_columns) {
    Well[i] <- c(paste("A", i, sep=""))
    Well[i+12] <- c(paste("B", i, sep=""))
    Well[i+2*12] <- c(paste("C", i, sep=""))
    Well[i+3*12] <- c(paste("D", i, sep=""))
    Well[i+4*12] <- c(paste("E", i, sep=""))
    Well[i+5*12] <- c(paste("F", i, sep=""))
    Well[i+6*12] <- c(paste("G", i, sep=""))
    Well[i+7*12] <- c(paste("H", i, sep=""))
  }
  return(data.frame(Well))
}

# generates column of sample labels for the qPCR data file
gen_samples <- function(nr_used_wells, nr_controls, nr_samples, sample_group_size) {
  # creates number labels before convergint to string labels
  sample_int_label <- rep(0, nr_used_wells)
  sample <- sample_int_label
  for (i in 1:nr_samples) {
    sample_int_label[(1+(i-1)*sample_group_size):(i*sample_group_size)] = i
  }
  for (i in 1:length(sample_int_label)) {
    sample[i] <- paste("sample_", sample_int_label[i], sep="")
  }
  sample <- append(sample, rep("H20", nr_controls))
  # remaining wells get NA values
  sample <- append(sample, rep(NA, 96 - (nr_used_wells + nr_controls)))
  return(sample)
}

with_run <- data.frame()

for (run_nr in 1:5) {

# possible genes to select
houses <- c("elfa")
stresses <- c("hsp70", "sod1", "igf1")
              
# deciding parameters of experiment
nr_samples <- 0
nr_replicates <- 0
nr_stress_genes <- 0
nr_genes <- 0
nr_controls <- 0
nr_used_wells <- 100
sample_group_size <- 0

# to make sure nr wells needed fit on a plate with 96 wells
while (nr_used_wells + nr_controls > 96) {
  nr_samples <- 2 * round(runif(1, 1, 8))
  nr_replicates <- round(runif(1, 2, 4))
  nr_stress_genes <- round(runif(1, 1, length(stresses)))
  nr_genes <- nr_stress_genes + 1
  nr_controls <- 2 * nr_genes
  sample_group_size <- nr_replicates * nr_genes
  nr_used_wells <- nr_samples * sample_group_size # note does not including controls
}

# selecting genes as stress genes and house gene
house <- sample(houses, 1)
stress <- sample(stresses, nr_stress_genes)

# generating sample column
Sample <- gen_samples(nr_used_wells, nr_controls, nr_samples, sample_group_size)

# generating well column
Well <- gen_wells()

# generating gene column
all_genes_used <- c(house, stress)
print(all_genes_used)
Gene <- c(rep(c(rep(house, nr_replicates), rep(stress, nr_replicates)), nr_samples), rep(all_genes_used,2))
# Na values for correcting the difference in row length
Gene <- append(Gene, rep(NA, 96 - (nr_used_wells+nr_controls)))


# assigning samples to population (will need changes if more that two populations requiered)
sample_types <- unique(Sample)
sample_types <- sample_types[grep("sample_", sample_types)]
sample_types <- unique(sample_types)
# randomizing order so that control and test samples are assigned at random
sample_types <- sample(sample_types, length(sample_types))
pop0 <- sample_types[1:(length(sample_types)/2)]
pop1 <- sample_types[(length(sample_types)/2+1):length(sample_types)]

Population <- rep(NA, 96)

to_wells <- tibble(Sample, Gene, Population)

to_wells <- to_wells %>%
  mutate(Population = ifelse(Sample %in% pop0, "ctrl", Population)) %>%
  mutate(Population = ifelse(Sample %in% pop1, "test", Population))

# generating ct values
Ct <- rep(0, 96)
for (i in 1:nrow(to_wells)) {
  pop <- to_wells$Population[i]
  gene <- to_wells$Gene[i]
  test_pop_multiplier <- 0.27 # impact on ct values of stress genes in stress inducing environments
                              # could be expanded on with input about specific stress inducing factors, e.g noise, metals, etc...
  
  stress1_multiplier <- 1.4 # these relate stress gene expression to that of housekeeping gene independent of environment
  stress2_multiplier <- 0.4
  stress3_multiplier <- 0.6
  stress4_multiplier <- 2.2
  ct_expect <- 23
  sd <- 1
  if (!is.na(pop)) {
    if (pop == "ctrl") {
      # parameters for stress gene ct value in control population
      if (gene == "hsp70") {
        ct_expect <- stress1_multiplier * ct_expect
        sd <- 3
      } else if (gene == "sod1") {
        ct_expect <- stress2_multiplier * ct_expect
        sd <- 1.7
      } else if (gene == "igf1") {
        ct_expect <- ct_expect
        sd <- 1.2
      } else if (gene == "stress_4") {
        ct_expect <- stress4_multiplier * ct_expect
        sd <- 2.1
      }
    } else if (pop == "test") {
      # parameters for stress gene ct value in test population
      if (gene == "hsp70") {
        ct_expect <- test_pop_multiplier * stress1_multiplier * ct_expect
        sd <- 2.5
      } else if (gene == "sod1") {
        ct_expect <- test_pop_multiplier * stress2_multiplier * ct_expect
        sd <- 0.6
      } else if (gene == "igf1") {
        ct_expect <- ct_expect
        sd <- 1.2
      } else if (gene == "stress_4") {
        ct_expect <- test_pop_multiplier * stress4_multiplier * ct_expect
        sd <- 1.9
      }
    }

    ct_value <- rnorm(n=1, mean = ct_expect, sd=sd)
    
  } else {
    ct_value <- NA
  }
  Ct[i] <- ct_value
}

# adding ct values to the wells they belong to
to_wells <- tibble(to_wells, Ct)

# shuffling row order simulating assignment of samples to wells by a research team with
# no systematic thinking. the content of each well is still accurate but now it can not
# be deduced by well position
to_wells <- tibble(to_wells, index=sample(1:nrow(to_wells), nrow(to_wells))) %>%
  arrange(index) %>%
  select(!index)

# pipetting samples into the wells
plate <- tibble(Run = rep(paste("run_", run_nr, sep="")), Well, to_wells)

with_run <- rbind(with_run, plate)

}


# because populations need to be consistent for samples between runs
qpcr <- with_run %>%
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




#writing to file
write.table(with_run, "in_process.txt", row.names = FALSE, quote=FALSE)


#print(plate, n=nrow(plate))
#paste(nr_used_wells, sample_group_size, nr_samples, nr_genes, nr_replicates)

# ignore
#ct_level_test <- plate %>%
#  filter(!Ct == "NA") %>%
#  group_by(Gene, Population) %>%
#  mutate(m_Ct = mean(Ct)) %>%
#  arrange(Gene, Population)
#print(ct_level_test, n=nrow(ct_level_test))


