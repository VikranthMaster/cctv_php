#!/usr/bin/python

from shared import *
import argparse

parser = argparse.ArgumentParser()
parser.add_argument('input_file',type=argparse.FileType('r'),help="Image to be processed")
args = parser.parse_args()

print("~ Filename: {}".format(args.input_file))