# Code adapted from Tensorflow Object Detection Framework
# https://github.com/tensorflow/models/blob/master/research/object_detection/object_detection_tutorial.ipynb
# Tensorflow Object Detection Detector

import time
import os.path
from shared import *
from detector import DetectorAPI

def runPersonDetect(root_dir,date,hour, odapi,threshold ):
    cur_dir = os.path.join(root_dir,date,hour)

    log_message("Running person detect on.. "+cur_dir)
    images = get_files(cur_dir, "jpg")
    p_images = []
    p_only_images = []

    for x in images:
        try:
            img = cv2.imread(cur_dir+"/"+x)
            img = cv2.resize(img, (640, 360))
        except Exception as e:
            print("Error reading file:"+x)
            continue
        boxes, scores, classes, num = odapi.processFrame(img)
        label = ""
        for i in range(len(boxes)):
            box = boxes[i]
            # Class 1 represents human
            if classes[i] == 1 and scores[i] > threshold:
                label = label + "%d %d %d %d %s "%(box[0],box[1],box[2],box[3],str(round(scores[i]*100, 2)))

        if label != "":
            p_images.append(str(x)+" "+label)
            p_only_images.append(x)

    person_txt = os.path.join(cur_dir,"person.txt")
    writeListToFile(p_images,person_txt)

    others_txt = os.path.join(cur_dir,"others.txt")
    other_list = []
    for img in images:
        if img not in p_only_images:
            other_list.append(img)
    writeListToFile(other_list,others_txt)

    return len(images)

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
