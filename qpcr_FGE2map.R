# outputs FGE values for map from FGE input

rm(list=ls())
graphics.off()

# calculating FGE from qpcr data input
source("qpcr_AvgACt2FGE.R")
rm(list=setdiff(ls(), c("qpcr_list", "runs", "ctrl_name")))

# performing full joins on data from every run
qpcr <- qpcr_list[[1]]
if (length(qpcr_list) > 1) {
  for (i in 2:length(runs)) {
    qpcr <- qpcr %>%
      select(Population, starts_with("FGE_")) %>% # removes irrelevant columns, here and on the row below
      full_join(qpcr_list[[i]] %>% select(Population, starts_with("FGE_")))
  }
}

# calculating mean FGE for population, for every gene
out <- qpcr %>%
  #filter(Population != ctrl_name) %>% # remove control population from FGE output
  select(Population, starts_with("FGE_")) %>%
  group_by(Population) %>%
  summarize_all("mean", na.rm=T)

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

lastfilepath <- paste(folderpath, "qpcr_map_FGE.txt", sep = "")
write.table(out, lastfilepath, row.names = FALSE, quote=FALSE)

