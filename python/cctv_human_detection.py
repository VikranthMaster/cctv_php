# Code adapted from Tensorflow Object Detection Framework
# https://github.com/tensorflow/models/blob/master/research/object_detection/object_detection_tutorial.ipynb
# Tensorflow Object Detection Detector

import time
import os.path
from shared import *
from detector import DetectorAPI

if not check_hdd():
    exit(0)

start_time = time.time()
now = datetime.datetime.now()
lasthour = now-datetime.timedelta(hours=1)
date = lasthour.strftime("%Y-%m-%d")
    
model_path = '/home/pi/person_detect_models/latest/frozen_inference_graph.pb'
odapi = DetectorAPI(path_to_ckpt=model_path)
threshold = 0.6
hour = '%02dhour'%(lasthour.hour)

total = 0
for photo_root in photo_root_dirs:
    cur_dir = os.path.join(photo_root,date,hour)
    if not os.path.exists(cur_dir):
        log_message("Directory does not exists: "+cur_dir)
        continue

    remove_duplicates(cur_dir)
    total = total + runPersonDetect(photo_root,date,hour,odapi,threshold)
    backup_hour(cur_dir)

total_time = time.time() - start_time
str = ("Person detect ran at %s on %d images and took %d minutes and %d seconds\n")%(now.strftime("%Y-%m-%d %H:%M"),total,total_time/60, total_time%60)
log_message(str)
addMarkerLine()
