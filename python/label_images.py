# Code adapted from Tensorflow Object Detection Framework
# https://github.com/tensorflow/models/blob/master/research/object_detection/object_detection_tutorial.ipynb
# Tensorflow Object Detection Detector

import time
import os.path
from shared import *
from detector import DetectorAPI
import numpy as np
import pandas as pd
np.random.seed(1)

def runOnDirectory(root_dir):
    cur_dir = os.path.join(root_dir)
    tar_dir = os.path.join(root_dir,"persons")
    noperson_dir = os.path.join(root_dir,"noperson")
    label_csv = open(os.path.join(root_dir,"labels.csv"),"w")
    label_csv.write("filename,width,height,class,xmin,ymin,xmax,ymax\n")

    ensure_dir_exists(tar_dir)
    ensure_dir_exists(noperson_dir)

    log_message("Running on.. "+cur_dir)
    images = get_files(cur_dir, "jpg")
    count = 0
    for x in images:
        print("Image number: %d"%(count))
        count = count+1
        if os.path.exists(os.path.join(tar_dir,x)):
            print("Already processed")
            continue

        try:
            img = cv2.imread(cur_dir+"/"+x)
            img = cv2.resize(img, (640, 360))
        except Exception as e:
            print("Error reading file:"+x)
            continue

        boxes, scores, classes, num = odapi.processFrame(img)

        # Visualization of the results of a detection.

        label = ""
        for i in range(len(boxes)):
            # Class 1 represents human
            if classes[i] == 1 and scores[i] > threshold:
                box = boxes[i]
                cv2.rectangle(img,(box[1],box[0]),(box[3],box[2]),(255,0,0),2)
                font = cv2.FONT_HERSHEY_SIMPLEX
                cv2.putText(img, str(round(scores[i]*100, 2)), (box[3],box[2]),font, 0.8, (255, 0, 0), 2, cv2.LINE_AA)
                cv2.imwrite(os.path.join(tar_dir,x), img)
                label = label + "%d %d %d %d %s\n"%(box[1],box[0],box[3],box[2],str(round(scores[i]*100, 2)))
                print("Person found: "+ label)
                label_csv.write(x+",640,360,person,%d,%d,%d,%d\n"%(box[1],box[0],box[3],box[2]))
        if label=="":
            shutil.move(os.path.join(root_dir,x),noperson_dir)

    label_csv.close()
    return len(images)

def split_labels(label_file):
    full_labels = pd.read_csv(label_file)
    print(full_labels.head())
    grouped = full_labels.groupby('filename')
    print(grouped.apply(lambda x: len(x)).value_counts())

    gb = full_labels.groupby('filename')
    grouped_list = [gb.get_group(x) for x in gb.groups]
    total = len(grouped_list)
    print("Total records are: %d"%(total))
    train_size = (int)(total*0.75)
    print("Train records are: %d"%(train_size))
    print("Test records are: %d"%(total-train_size))

    train_index = np.random.choice(len(grouped_list), size=train_size, replace=False)
    test_index = np.setdiff1d(list(range(train_size)), train_index)

    train = pd.concat([grouped_list[i] for i in train_index])
    test = pd.concat([grouped_list[i] for i in test_index])

    train.to_csv(os.path.join(os.path.dirname(label_file),'train_labels.csv'), index=None)
    test.to_csv(os.path.join(os.path.dirname(label_file),'test_labels.csv'), index=None)

parser = argparse.ArgumentParser()
parser.add_argument('input_directory',type=dir_path,help="Directory to be processed")
parser.add_argument('inference_graph_file',type=argparse.FileType('r'),help="Inference graph to be used")
args = parser.parse_args()

model_path = args.inference_graph_file.name
directory_path = args.input_directory

start_time = time.time()

odapi = DetectorAPI(path_to_ckpt=model_path)
threshold = 0.9

total = runOnDirectory(directory_path)

total_time = time.time() - start_time
str = ("Person detect ran at %d images and took %d minutes and %d seconds\n")%(total,total_time/60, total_time%60)
log_message(str)

split_labels(os.path.join(directory_path,"labels.csv"))


