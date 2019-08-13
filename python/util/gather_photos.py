#!/usr/bin/python
import os.path
import glob
from shared import *

root_dir = "/mnt/hdd/StairsPhotos"
target_dir = "/mnt/hdd/train"
for img in  glob.iglob(root_dir + '/**/*.jpg', recursive=True):
    size = os.path.getsize(img)
    if size<100000:
        continue
    dest_file = os.path.relpath(img,"/mnt/hdd").replace('/','_')
    ffmpeg.input(img).filter('scale',640,-1).output(os.path.join(target_dir,dest_file)).run()
