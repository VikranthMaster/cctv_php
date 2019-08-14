#!/usr/bin/python
from shared import *

parser = argparse.ArgumentParser()
parser.add_argument('input_directory',type=dir_path,help="Input Directory of images to be extracted")
parser.add_argument('output_directory',type=dir_path,help="Output directory")
args = parser.parse_args()

input_dir = args.input_directory
output_dir = args.output_directory

print("Gathering photos from "+ input_dir)
for img in  glob.iglob(input_dir + '/**/*.jpg', recursive=True):
    size = os.path.getsize(img)
    if size<100000:
        continue
    dest_file = os.path.relpath(img,"/mnt/hdd").replace('/','_')
    ffmpeg.input(img).filter('scale',640,-1).output(os.path.join(output_dir,dest_file)).run()

