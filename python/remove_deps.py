#!/usr/bin/python
from shared import *

def remove_duplicates(cur_dir):
    imgs = get_files(cur_dir,"jpg")
    imgs.sort()
    to_del = []
    for index in range(0,len(imgs)-1):
        one = cv2.imread(os.path.join(cur_dir,imgs[index]))
        two = cv2.imread(os.path.join(cur_dir,imgs[index+1]))

        # convert the images to grayscale
        one = cv2.cvtColor(one, cv2.COLOR_BGR2GRAY)
        two = cv2.cvtColor(two, cv2.COLOR_BGR2GRAY)

        diff = mse(one,two)
        if(index%500==0):
            print("Processed %d images"%(index))
        if(diff<120 or "thumbnails" in imgs[index]):
            to_del.append(imgs[index])

    for img in to_del:
        try:
            os.remove(os.path.join(cur_dir,img))
        except Exception as error:
            print("Error deleting duplicate image:"+img)
    print("Total items deleted: %d out of %d" % (len(to_del),len(imgs)))
    
    
parser = argparse.ArgumentParser()
parser.add_argument('input_directory',type=dir_path,help="Input Directory of images to be processed")
args = parser.parse_args()

input_dir = args.input_directory
remove_duplicates(input_dir)