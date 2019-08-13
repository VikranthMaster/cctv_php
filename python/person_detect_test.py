#!/usr/bin/python

from shared import *
from detector import *
from detector import DetectorAPI
import argparse

parser = argparse.ArgumentParser()
parser.add_argument('input_file',type=argparse.FileType('r'),help="Image to be processed")
parser.add_argument('inference_graph_file',type=argparse.FileType('r'),help="Inference graph to be used")
args = parser.parse_args()

model_path = args.inference_graph_file.name
img_path = args.input_file.name

odapi = DetectorAPI(path_to_ckpt=model_path)
threshold = 0.5
img = cv2.imread(img_path)
boxes, scores, classes, num = odapi.processFrame(img)
print("Processed image")
label = ""
for i in range(len(boxes)):
    box = boxes[i]
    # Class 1 represents human
    if classes[i] == 1 and scores[i] > threshold:
        label = label + "%d %d %d %d %s\n"%(box[0],box[1],box[2],box[3],str(round(scores[i]*100, 2)))
        print("Person found in image '%s': %s"%(img_path,label))
