# Code adapted from Tensorflow Object Detection Framework
# https://github.com/tensorflow/models/blob/master/research/object_detection/object_detection_tutorial.ipynb
# Tensorflow Object Detection Detector

import time
import os.path
from shared import *
import numpy as np
import pandas as pd
np.random.seed(1)


def split_labels(label_file, split):
    full_labels = pd.read_csv(label_file)
    print(full_labels.head())
    grouped = full_labels.groupby('filename')
    print(grouped.apply(lambda x: len(x)).value_counts())

    gb = full_labels.groupby('filename')
    grouped_list = [gb.get_group(x) for x in gb.groups]
    total = len(grouped_list)
    print("Total records are: %d"%(total))
    train_size = (int)(total*split/100)
  
    train_index = np.random.choice(len(grouped_list), size=train_size, replace=False)
    test_index = np.setdiff1d(list(range(total)), train_index)

    train = pd.concat([grouped_list[i] for i in train_index])
    test = pd.concat([grouped_list[i] for i in test_index])
    
    train_csv = os.path.join(os.path.dirname(label_file),'train_labels.csv')
    test_csv = os.path.join(os.path.dirname(label_file),'test_labels.csv')
    
    print("%d train records are written to %s"%(len(train), train_csv))
    print("%d test records are written to %s"%(len(test),test_csv))

    train.to_csv(train_csv, index=None)
    test.to_csv(test_csv, index=None)

parser = argparse.ArgumentParser()
parser.add_argument('label_file',type=argparse.FileType('r'),help="Label file to be split")
parser.add_argument('split',type=int,help="Label file to be split")

args = parser.parse_args()

label_file = args.label_file.name
split_labels(label_file, args.split)


