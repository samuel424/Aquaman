library(ggplot2)
library(ggspatial)
library(tidyverse)

args <- commandArgs(TRUE)
 
X <- as.numeric(args[1])
Y <- as.numeric(args[2])

world <- map_data("world")
sites<- data.frame(longitude = c(X), latitude = c(Y))

p <- ggplot() +
  geom_map(
    data = world, map = world,
    aes(long, lat, map_id = region),
    color = "black", fill = "lightgray", size = 0.5
  ) +
  geom_point(
    data = sites,
    aes(longitude, latitude, color='red', size = 2),
  ) +
  theme(legend.position = "None")

png("map.png", width = 600, height = 400)

print(p)
dev.off()
