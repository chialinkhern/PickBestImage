# Title     : functions
# Objective : general-purpose functions for this project
# Created by: chialinkhern
# Created on: 2/26/21

# TODO: this is a mess- clean it up

rbindall = function(indir, outdir){
  originalwd = getwd()
  setwd(indir)
  print(getwd())
  files = list.files(pattern="*.csv")
  # print(files)
  trials = do.call(rbind, lapply(files, function(x) df = read.csv(x, stringsAsFactors=FALSE)))
  trials$X = NULL # no idea why X appears as a column; this is an easy fix
  setwd(originalwd)
  write.csv(trials, outdir, row.names=FALSE)
}

rbindall("data/", "analyses/out/trials.csv")
trials = read.csv("analyses/out/trials.csv")
attach(trials)
counts = aggregate(rts~obj_names+images_picked, FUN=function(x){length(x)})
colnames(counts) = c("obj_names", "images_picked", "counts")

counts$proportion = 0
attach(counts)

for (obj_name in obj_names){
  # print(obj_name)
  obj_df = counts[obj_names==obj_name,]
  total_counts = sum(obj_df$counts)
  for (image_picked in obj_df$images_picked){
    image_count = obj_df[obj_df$images_picked==image_picked,]$counts
    proportion = image_count/total_counts
    # print(proportion)
    counts[obj_names==obj_name & images_picked==image_picked,]$proportion = proportion
  }
}

write.csv(counts, "analyses/out/images.csv", row.names=FALSE)