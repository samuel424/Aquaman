library(ggplot2)
library(ggspatial)
library(tidyverse)
library(dplyr)

args <- commandArgs(TRUE)

user <- args[1]

#from Elias files to find our input for the map dot size
folderpath <- paste("mapdata", user, "/", sep="")
lastfilepath <- paste(folderpath, "qpcr_map_FGE.txt", sep = "")


mapinput <- read.delim(lastfilepath, header=TRUE, sep = " ")

#Make a function to normalize the values
dotsize <- function(x) {
  sqrt(x/pi)
}

dot_values = lapply(mapinput[2], dotsize)


datapath <- paste("mapdata", user, "/", sep="")
filename <- paste(datapath, "coordinates.csv", sep="")

matrix <- read.csv(filename,header=FALSE)

long <- c()
lat <- c()
for (i in colnames(matrix)){
  long <- append(long,matrix[1,i])
  lat <- append(lat,matrix[2,i])
}

world <- map_data("world")
sites<- data.frame(longitude = c(long), latitude = c(lat), size = c(dot_values))

# output table for later display in website
outfile <- paste(datapath, "results/map_sites_table.txt", sep = "")
write.table(sites, file = outfile, sep=',', row.names = FALSE, quote = FALSE)

genename <- colnames(sites[3])
names(sites)[names(sites)==genename] <- "normalized_gene_expression"


# adjust map layout to coordinates long first for x, lat for y
x1 <- min(long) -3
x2 <- max(long) +3

y1 <- min(lat) -3
y2 <- max(lat) +3

mapfile <- paste(folderpath, "fancymap.png", sep="")
png(filename=mapfile, width = 600, height = 400)

p <- ggplot() +
  geom_map(
    data = world, map = world,
    aes(x = 1, y = 1, map_id = region),
    color = "brown", fill = "lightgreen", size = 0.5
  ) +
  coord_sf(xlim = c(x1,x2), ylim = c(y1, y2), expand = FALSE

  )+
  geom_point(
    data = sites,
    aes(x = longitude, y = latitude, size = normalized_gene_expression )
  )+
  theme_light(
  )+
  labs(title = "Gene expression of different locations",
       subtitle = "The dot size is equal to sqrt(FGE/pi)",
       x = "longitude", y = "latitude")


p


