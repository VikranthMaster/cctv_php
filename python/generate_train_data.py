# Code adapted from Tensorflow Object Detection Framework
# https://github.com/tensorflow/models/blob/master/research/object_detection/object_detection_tutorial.ipynb
# Tensorflow Object Detection Detector

import time
import os.path
import sys
from shared import *
from detector import DetectorAPI

def get_files(parent_dir, extension):
    return [x for x in os.listdir(parent_dir) if x.endswith(extension)]

def runOnDirectory(root_dir):
    print("Running on.. "+root_dir)
    cur_dir = os.path.join(root_dir)
    tar_dir = os.path.join(root_dir,"persons")
    ensure_dir_exists(tar_dir)
    images = get_files(cur_dir, "jpg")
    for x in images:
        print(x)
        
        try:
            img = cv2.imread(cur_dir+"/"+x)
            img = cv2.resize(img, (480,270))
        except Exception as e:
            print("Error reading file:"+x)
            continue

        boxes, scores, classes, num = odapi.processFrame(img)

        # Visualization of the results of a detection.

        for i in range(len(boxes)):
            # Class 1 represents human
            if classes[i] == 1 and scores[i] > threshold:
                box = boxes[i]
                cv2.rectangle(img,(box[1],box[0]),(box[3],box[2]),(255,0,0),2)
                cv2.imwrite(os.path.join(tar_dir,x), img)
    return len(images)

if __name__ == "__main__":
    start_time = time.time()
    now = datetime.datetime.now()
    date = now.strftime("%Y-%m-%d")
    
    if now.hour==0:
        sys.exit(0)
    
#    model_path = '/home/pi/Downloads/ssdlite_mobilenet_v2_coco_2018_05_09/frozen_inference_graph.pb'
#    model_path = '/home/pi/Downloads/faster_rcnn_resnet50_coco_2018_01_28/frozen_inference_graph.pb'
#    model_path = '/home/pi/Downloads/ssd_resnet50_v1_fpn_shared_box_predictor_640x640_coco14_sync_2018_07_03/frozen_inference_graph.pb'
    model_path = '/home/pi/Downloads/faster_rcnn_resnet101_coco_2018_01_28/frozen_inference_graph.pb'

    odapi = DetectorAPI(path_to_ckpt=model_path)
    threshold = 0.5
          
    total = runOnDirectory("/mnt/hdd/test")
    
    total_time = time.time() - start_time
    str = ("Person detect ran at %s on %d images and took %d minutes and %d seconds\n")%(now.strftime("%Y-%m-%d %H:%M"),total,total_time/60, total_time%60)
    print(str)
  
