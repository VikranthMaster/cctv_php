# Code adapted from Tensorflow Object Detection Framework
# https://github.com/tensorflow/models/blob/master/research/object_detection/object_detection_tutorial.ipynb
# Tensorflow Object Detection Detector

import time
import os.path
from shared import *
from detector import DetectorAPI
def remove_duplicates(cur_dir):
    imgs = get_files(cur_dir,"jpg")
    imgs.sort()
    to_del = []
    for index in range(0,len(imgs)-1):
        one = cv2.imread(os.path.join(cur_dir,imgs[index]))
        one = cv2.resize(one, (640, 360))
        two = cv2.imread(os.path.join(cur_dir,imgs[index+1]))
        two = cv2.resize(two, (640, 360))

        # convert the images to grayscale
        one = cv2.cvtColor(one, cv2.COLOR_BGR2GRAY)
        two = cv2.cvtColor(two, cv2.COLOR_BGR2GRAY)

        diff = mse(one,two)
        if(diff<100):
            to_del.append(imgs[index])

    for img in to_del:
        try:
            os.remove(os.path.join(cur_dir,img))
            os.remove(os.path.join(cur_dir,"thumbnails",img))
        except Exception as error:
            print("Error deleting duplicate image:"+img)
    print("Total items to be deleted: %d out of %d" % (len(to_del),len(imgs)))

def writeListToFile(list, file):
    f = open(file, "w")
    for elem in list:
        f.write(elem+"\n")
    f.close()

def runOnDirectory(root_dir,date,hour):
    cur_dir = os.path.join(root_dir,date,hour)

    if not os.path.exists(cur_dir):
        log_message("Directory does not exists: "+cur_dir)
        return 0

    log_message("Running on.. "+cur_dir)
    remove_duplicates(cur_dir)
    images = get_files(cur_dir, "jpg")
    p_images = []

    for x in images:
        try:
            img = cv2.imread(cur_dir+"/"+x)
            img = cv2.resize(img, (640, 360))
        except Exception as e:
            print("Error reading file:"+x)
            continue
        boxes, scores, classes, num = odapi.processFrame(img)

        for i in range(len(boxes)):
            # Class 1 represents human
            if classes[i] == 1 and scores[i] > threshold:
                p_images.append(x)
                break

    person_txt = os.path.join(cur_dir,"person.txt")
    writeListToFile(p_images,person_txt)

    others_txt = os.path.join(cur_dir,"others.txt")
    other_list = []
    for img in images:
        if img not in p_images:
            other_list.append(img)
    writeListToFile(other_list,others_txt)
    backup_hour(cur_dir)
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
    total = total + runOnDirectory(photo_root,date,hour)

total_time = time.time() - start_time
str = ("Person detect ran at %s on %d images and took %d minutes and %d seconds\n")%(now.strftime("%Y-%m-%d %H:%M"),total,total_time/60, total_time%60)
log_message(str)
addMarkerLine()
