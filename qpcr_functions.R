# functions used throughout qpcr processing

library(dplyr)

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

# adds delta ct values to dataframe
gen_A_ct <- function (qpcr, stress, nr_stress) {
  # generating columns
  qpcr <- gen_colset(qpcr, stress, "ACt_")
  # calculating delta ct values
  for (i in 1:nr_stress) {
    qpcr[3+nr_stress+i] <- qpcr[,3+i]-qpcr[,3]
  }
  return(qpcr)
}

# calculates the average delta ct value for the control population
get_avg_A_ct_ctrl <- function(qpcr, stress, nr_stress, indx) {
  # Average delta_ct for all populations, all stress genes
  pop <- unique(qpcr$Population)
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

# generates delta-delta ct columns
gen_AA_ct <- function(qpcr, stress, nr_stress, avg_A_ctrl, indx) {
  # generating columns
  qpcr <- gen_colset(qpcr, stress, "AA_")
  # delta_delta_ct
  for (i in indx_A_ct) {
    qpcr[i+nr_stress] <- qpcr[i]-as.numeric(avg_A_ctrl[i+1-indx[1]])
  }
  return(qpcr)
}

# generates FGE columns
gen_FGE <- function(qpcr, stress, nr_stress, indx) {
  # generating columns
  qpcr <- gen_colset(qpcr, stress, "FGE_")
  # fold gene expression by sample
  for (i in indx) {
    qpcr[i] <- 2^-(qpcr[,i-nr_stress])
  }
  return(qpcr)
}

# adds columns with log10 transformed FGE values
gen_log_FGE <- function(qpcr, stress, indx) {
  nr_stress <- length(stress)
  qpcr <- gen_colset(qpcr, stress, "log10_")
  for (i in 1:nr_stress) {
    qpcr[indx[i]] <- log10(qpcr[indx[i]-nr_stress])
  }
  return(qpcr)
}
