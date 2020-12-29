#!/usr/bin/python

from shared2 import *
from detector import DetectorAPI
import time

parser = argparse.ArgumentParser()
parser.add_argument('input_file',type=argparse.FileType('r'),help="Image to be processed")
parser.add_argument('inference_graph_file',type=argparse.FileType('r'),help="Inference graph to be used")
args = parser.parse_args()

model_path = args.inference_graph_file.name
img_path = args.input_file.name
print("Model={}".format(model_path))
print("Image={}".format(img_path))

odapi = DetectorAPI(path_to_ckpt=model_path)
threshold = 0.6
img = cv2.imread(img_path)
img = cv2.resize(img, (640,360))

start_time = time.time()

boxes, scores, classes, num = odapi.processFrame(img)
label = ""
for i in range(len(boxes)):
    box = boxes[i]
    # Class 1 represents human
    if classes[i] == 1 and scores[i] > threshold:
        label = label + "%d %d %d %d %s\n"%(box[0],box[1],box[2],box[3],str(round(scores[i]*100, 2)))
        print("Person found in image '%s': %s"%(img_path,label))

total_time = time.time() - start_time
stri = ("First Image: Person detect took %d minutes and %d seconds\n")%(total_time/60, total_time%60)
print(stri)

img = cv2.imread(img_path)
img = cv2.resize(img, (640,360))

start_time = time.time()

boxes, scores, classes, num = odapi.processFrame(img)
label = ""
for i in range(len(boxes)):
    box = boxes[i]
    # Class 1 represents human
    if classes[i] == 1 and scores[i] > threshold:
        label = label + "%d %d %d %d %s\n"%(box[0],box[1],box[2],box[3],str(round(scores[i]*100, 2)))
        print("Person found in image '%s': %s"%(img_path,label))


total_time = time.time() - start_time
stri = ("Second Image: Person detect took %d minutes and %d seconds\n")%(total_time/60, total_time%60)
print(stri)
