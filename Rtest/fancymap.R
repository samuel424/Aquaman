#entering coordinates

#library(ggplot2)
#library(ggspatial)
#library(rnaturalearth)
#library(rnaturalearthdata)
#library(ggrepel)
#library(sf)


#args <- commandArgs(trailingOnly = TRUE)

#longi <- as.numeric(args[1])
#lati <- as.numeric(args[2])

#world <- ne_countries(scale = "medium", returnclass = "sf")
#class(world)
#sites<- data.frame(longitude = c(longi), latitude = c(lati))
#p <- ggplot(data = world) +
  #theme_bw()+
  #geom_sf()+
  #geom_point(data = sites, aes(x = longitude, y = latitude), size = 1, shape = 21, fill = "red") +
  #coord_sf(xlim = c(11, 27), ylim = c(54, 70), expand = FALSE)+
  #geom_text_repel(data = sites, aes(x = longitude, y = latitude, label = city), size=10, fontface = "bold", nudge_x = c(4), nudge_y = c(1), segment.size=0.5)+
  #annotation_scale(location = "bl", width_hint = 0.5, height=unit(0.5, "in"), text_cex = 1, text_pad = unit(0.5, "in"), pad_x = unit(1, "in"), pad_y = unit(0.5, "in")) +
  #annotation_north_arrow(location = "tl", width=unit(0.5, "in"), height=unit(0.5, "in"), which_north = "true", pad_x = unit(0.5, "in"), pad_y = unit(0.5, "in"), style = north_arrow_fancy_orienteering)

#png(filename="fancymap.png", width=500, height=500)
#print(p)
#dev.off() 

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

png("fancymap.png", width = 600, height = 400)

print(p)
dev.off()