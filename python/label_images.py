# Code adapted from Tensorflow Object Detection Framework
# https://github.com/tensorflow/models/blob/master/research/object_detection/object_detection_tutorial.ipynb
# Tensorflow Object Detection Detector

import time
import os.path
from shared import *
from detector import DetectorAPI

def setup_target_dir(root_dir,threshold):
    tar_dir = os.path.join(root_dir,"persons_"+threshold)
    ensure_dir_exists(tar_dir)
    lbl_csv = open(os.path.join(root_dir,"lbl_csv_"+threshold+".csv"),"w")
    lbl_csv.write("filename,width,height,class,xmin,ymin,xmax,ymax\n")
    return (tar_dir,lbl_csv)

def copyToDir(tar_dir, label_csv, img, x, box, score):
    cv2.rectangle(img,(box[1],box[0]),(box[3],box[2]),(255,0,0),2)
    font = cv2.FONT_HERSHEY_SIMPLEX
    cv2.putText(img, score, (box[3],box[2]),font, 0.8, (255, 0, 0), 2, cv2.LINE_AA)
    cv2.imwrite(os.path.join(tar_dir,x), img)
    label = "%d %d %d %d %s\n"%(box[1],box[0],box[3],box[2],score)
    print("Person found: "+ label)
    label_csv.write(x+",640,360,person,%d,%d,%d,%d\n"%(box[1],box[0],box[3],box[2]))


def runOnDirectory(root_dir):
    cur_dir = os.path.join(root_dir)
    
    noperson_dir = os.path.join(root_dir,"noperson")
    ensure_dir_exists(noperson_dir)

    (tar_dir_98, lbl_csv_98) = setup_target_dir(root_dir, '98')
    (tar_dir_90, lbl_csv_90) = setup_target_dir(root_dir, '90')
    (tar_dir_75, lbl_csv_75) = setup_target_dir(root_dir, '75')
    (tar_dir_60, lbl_csv_60) = setup_target_dir(root_dir, '60')
        
    
    log_message("Running on.. "+cur_dir)
    images = get_files(cur_dir, "jpg")
    count = 0
    for x in images:
        print("Image number: %d"%(count))
        
        count = count+1
        if os.path.exists(os.path.join(tar_dir_98,x)):
            print("Already processed")
            continue

        if os.path.exists(os.path.join(tar_dir_90,x)):
            print("Already processed")
            continue
            
        if os.path.exists(os.path.join(tar_dir_75,x)):
            print("Already processed")
            continue
            
        if os.path.exists(os.path.join(tar_dir_60,x)):
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
        noperson = True
        for i in range(len(boxes)):
            # Class 1 represents human
            if classes[i] == 1 and scores[i] > 0.98:
                copyToDir(tar_dir_98, lbl_csv_98, img, x, boxes[i], str(round(scores[i]*100, 2)))
                noperson = False
            elif classes[i] == 1 and scores[i] > 0.90:
                copyToDir(tar_dir_90, lbl_csv_90, img, x, boxes[i], str(round(scores[i]*100, 2)))
                noperson = False
            elif classes[i] == 1 and scores[i] > 0.75:
                copyToDir(tar_dir_75, lbl_csv_75, img, x, boxes[i], str(round(scores[i]*100, 2)))
                noperson = False
            elif classes[i] == 1 and scores[i] > 0.60:
                copyToDir(tar_dir_60, lbl_csv_60, img, x, boxes[i], str(round(scores[i]*100, 2)))
                noperson = False
        if noperson==True:
            shutil.move(os.path.join(root_dir,x),noperson_dir)

    lbl_csv_98.close()
    lbl_csv_90.close()
    lbl_csv_75.close()
    lbl_csv_60.close()
    return len(images)

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
<<<<<<< HEAD
log_message(str)

=======
log_message(str)
>>>>>>> f150279449910b32a378fbcbfa5db33f952fdcb7
