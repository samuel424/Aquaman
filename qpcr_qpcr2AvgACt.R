# takes raw qpcr data, restructures and outputs delta Ct values. also prints
# to file average delta Ct values corresponding to gene and population

# functions used are read from a different file
source("qpcr_functions.R")

# commandArgs for userID and analysis type (different folders)
args <- commandArgs(TRUE)
user <- args[1]
analysis <- args[2]

if (analysis == 1){
  folder <- "mapdata"
}
if (analysis == 2){
  folder <- "regdata"
}

folderpath <- paste(folder, user, "/", sep="")

# read file
read_file_path <- paste(folderpath, "qpcr_process.txt", sep = "")
#read_file_path <- "qpcr_verification.txt"
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
  
  # add dataframe to list of dataframes
  qpcr_list <- append(qpcr_list, list(single_run_data))
  stress_list <- append(stress_list, list(stress))
}

# performing full joins on data from every run
qpcr <- qpcr_list[[1]]
if (length(qpcr_list) > 1) {
  for (i in 2:length(runs)) {
    qpcr <- qpcr %>%
      select(Population, starts_with("ACt_")) %>% # removes irrelevant columns, here and on the row below
      full_join(qpcr_list[[i]] %>% select(Population, starts_with("ACt_")))
  }
}

# calculating mean ACt for population, for every gene
ACt <- qpcr %>%
  select(Population, starts_with("ACt_")) %>%
  group_by(Population) %>%
  summarize_all("mean", na.rm=T)

# write to file
outfilepath <- paste(folderpath, "qpcr_map_avg_ACt.txt", sep = "")
write.table(ACt, outfilepath, row.names = FALSE, quote=FALSE)






