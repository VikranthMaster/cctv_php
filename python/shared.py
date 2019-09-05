#!/usr/bin/python
import os.path
import os
import datetime
import shutil
import cv2
import numpy as np
import tarfile
import glob
import ffmpeg
import argparse

photo_root_dirs = ["/mnt/hdd/GatePhotos", "/mnt/hdd/StairsPhotos"]
video_root_dirs = ["/mnt/hdd/GateVideos", "/mnt/hdd/StairsVideos"]

def get_files(parent_dir, extension):
    return [x for x in os.listdir(parent_dir) if x.endswith(extension)]


def get_sub_dirs(root_dir):
    return [x for x in os.listdir(root_dir) if os.path.isdir(root_dir+"/"+x)]


def ensure_dir_exists(directory):
	if not os.path.exists(directory):
		os.mkdir(directory)


def replace_with_low_res(directory, files):
    for img in files:
        try:
            cv2_img = cv2.imread(os.path.join(directory,img))
            if str(cv2_img.shape) == "(360, 640, 3)":
                continue
            cv2_img = cv2.resize(cv2_img, (640, 360))
            cv2.imwrite(os.path.join(os.path.join(directory,"temp.jpg")), cv2_img)
            os.remove(os.path.join(directory,img))
            os.rename(os.path.join(os.path.join(directory,"temp.jpg")), os.path.join(directory,img))
        except:
            log_message("error reading:"+img)


def log_message(message):
    print(message.rstrip()+"\n")


def getPersonImages(root_dir):
    persons = []
    p_file = open(os.path.join(root_dir,"person.txt"),"r")
    while True:
        line = p_file.readline().strip()
        if line == '':
            break;
        if " " in line:
            line = line.split()[0].rstrip()
        persons.append(line)

    return persons


def getOtherImages(root_dir):
    others = []
    p_file = open(os.path.join(root_dir,"others.txt"),"r")
    while True:
        line = p_file.readline().strip()
        if line == '':
            break;

        others.append(line)

    return others

def addMarkerLine():
    log_message("*"*180)

def check_hdd():
    try:
        os.listdir("/mnt/hdd")
        os.listdir("/mnt/hdd/GatePhotos")
        os.listdir("/mnt/hdd/StairsPhotos")
        os.listdir("/mnt/hdd/tmp/GateCamera")
        os.listdir("/mnt/hdd/tmp/StairsCamera")
        log_message("Hard disk is accessible")
        return True
    except Exception as error:
        log_message("Hard disk is not accessible: "+ str(error))
        log_message("Rebooting Raspberry PI now....")
        os.system("sudo reboot")
        return False

def save_space_image(date_dir):
    log_message("Running save_space_image on date:"+date_dir)
    for hour_dir in get_sub_dirs(date_dir):
        all_images = get_files(os.path.join(date_dir, hour_dir), "jpg")
        non_person_imgs = all_images
        person_imgs = getPersonImages(os.path.join(date_dir,hour_dir))
        non_person_imgs = [x for x in all_images if x not in person_imgs]
        replace_with_low_res(os.path.join(date_dir,hour_dir),non_person_imgs)

def save_space_video(date_dir):
    log_message("Running save_space_video on date:"+date_dir)
    for hour_dir in get_sub_dirs(date_dir):
        person_imgs = os.path.join(date_dir,hour_dir)
        if len(person_imgs)==0:
            gate_video = os.path.join(date_dir.replace("Photos","Videos"),hour_dir)
            if os.path.exists(gate_video):
                shutil.rmtree(gate_video)

def save_video_space2(date_dir):
    log_message("Running save_video_space2 on date:"+date_dir)
    for hr_dir in get_sub_dirs(date_dir):
        for video in get_files(os.path.join(date_dir,hr_dir),"mp4"):
            exists = check_person_exists_in_video(date_dir, hr_dir, video)
            if exists==False:
                os.remove(os.path.join(date_dir,hr_dir,video))

def encode_videos(date_dir):
    log_message("Running encode_videos on date:"+date_dir)
    for hr_dir in get_sub_dirs(date_dir):
        for video in get_files(os.path.join(date_dir,hr_dir),"mp4"):
            log_message("Encode video: "+video)
            ffmpeg.input(os.path.join(date_dir,hr_dir,video)).output(os.path.join(date_dir,hr_dir,'out.mp4'),video_bitrate='256k').run()
            break

def check_person_exists_in_video(date_dir, hour, video):
    date_dir = date_dir.replace("Videos","Photos")
    date = os.path.split(date_dir)[-1]
    temp = video.split('[M]')[0].split('-')
    start_time = datetime.datetime.strptime(date + " " + temp[0], '%Y-%m-%d %H.%M.%S')
    end_time = datetime.datetime.strptime(date + " " + temp[1], '%Y-%m-%d %H.%M.%S')

    imgs = getPersonImages(os.path.join(date_dir,hour))
    imgs.sort()
    for i in range(len(imgs)):
        time = imgs[i].split('[M]')[0]
        if "_" in time:
            img_time = datetime.datetime.strptime(date + " " + time, '%Y-%m-%d %H_%M_%S')
        else:
            img_time = datetime.datetime.strptime(date + " " + time, '%Y-%m-%d %H.%M.%S')
        if img_time > start_time:
            return img_time < end_time
    return False

def mse(imageA, imageB):
    # the 'Mean Squared Error' between the two images is the
    # sum of the squared difference between the two images;
    # NOTE: the two images must have the same dimension
    err = np.sum((imageA.astype("float") - imageB.astype("float")) ** 2)
    err /= float(imageA.shape[0] * imageA.shape[1])

    # return the MSE, the lower the error, the more "similar"
    # the two images are
    return err

def make_tarfile(output_filename, source_dir):
    with tarfile.open(output_filename, "w:gz") as tar:
        tar.add(source_dir, arcname=os.path.basename(source_dir))

def backup_hour(hour_dir):
    print("Backing up :"+hour_dir)
    target_dir = os.path.join(hour_dir,os.path.basename(hour_dir))
    if os.path.exists(target_dir):
        shutil.rmtree(target_dir)

    ensure_dir_exists(target_dir)

    person_list = os.path.join(hour_dir,"person.txt")
    other_list = os.path.join(hour_dir,"others.txt")

    if not os.path.exists(person_list) or not os.path.exists(other_list):
        return

    files_to_copy = []
    files_to_copy.append(person_list)
    files_to_copy.append(other_list)

    # Add person images to list.
    f = open(person_list)
    while 1:
        line = f.readline().rstrip()
        if not line:
            break
        if " " in line:
            line = line.split()[0].rstrip()
        files_to_copy.append(os.path.join(hour_dir,line))
    f.close()

    # Add other images to list.
    f = open(other_list)
    while 1:
        line = f.readline().rstrip()
        if not line:
            break
        files_to_copy.append(os.path.join(hour_dir,"thumbnails",line))
    f.close()

    for file in files_to_copy:
        shutil.copy2(file,target_dir)

    tarfile = os.path.join(hour_dir,os.path.basename(hour_dir)+".tar.gz")
    if os.path.exists(tarfile):
        os.remove(tarfile)

    make_tarfile(tarfile,target_dir)
    shutil.rmtree(target_dir)

def backup(root_dir):
    print("Backing up :"+root_dir)
    hours = get_sub_dirs(root_dir)
    for hour_dir in hours:
        backup_hour(os.path.join(root_dir,hour_dir))

def delete_old_footage():
    expiry_date_dictionary = {
        "/mnt/hdd/GatePhotos": 45,
        "/mnt/hdd/StairsPhotos": 45,
        "/mnt/hdd/GateVideos": 45,
        "/mnt/hdd/StairsVideos": 45,
        "/mnt/hdd/tmp/GateCamera": 0,
        "/mnt/hdd/tmp/StairsCamera": 0
    }

    today = datetime.datetime.now().date()
    print("Deleting old footage...")
    for root_dir in expiry_date_dictionary:
        expiry_date = expiry_date_dictionary[root_dir]
        for dt_dir in get_sub_dirs(root_dir):
            if dt_dir == "train":
                continue
            dt = datetime.datetime.strptime(dt_dir, "%Y-%m-%d").date()
            elapsed_days = (today-dt).days
            if elapsed_days > expiry_date:
                try:
                    print("Deleting: "+root_dir+"/"+dt_dir)
                    shutil.rmtree(root_dir+"/"+dt_dir)
                except:
                    print("Error deleting directory:"+root_dir+"/"+dt_dir)


def save_backups():
    date = datetime.datetime.now() - datetime.timedelta(days=1)
    date = date.strftime("%Y-%m-%d")

    for photo_dir in photo_root_dirs:
        backup(os.path.join(photo_dir,date))

def delete_backup():
    date = datetime.datetime.now() - datetime.timedelta(days=2)
    date = date.strftime("%Y-%m-%d")
    print("Deleting backups on "+date)
    for tar in glob.iglob(os.path.join(photo_root_dirs[0],date) + '/**/*.tar.gz', recursive=True):
        os.remove(tar)

    for tar in glob.iglob(os.path.join(photo_root_dirs[1],date) + '/**/*.tar.gz', recursive=True):
        os.remove(tar)

def save_space_main():
    if not check_hdd():
        exit(0)

    past_time = datetime.datetime.now() - datetime.timedelta(days=1)
    past_date = past_time.strftime("%Y-%m-%d")

    for photo_root in photo_root_dirs:
        date_dir = os.path.join(photo_root, past_date)
        if os.path.exists(date_dir):
            save_space_video(date_dir)
            #save_space_image(date_dir)

    for video_root in video_root_dirs:
        date_dir = os.path.join(video_root,past_date)
        if os.path.exists(date_dir):
            save_video_space2(date_dir)

def dir_path(path):
    if os.path.isdir(path):
        return path
    else:
        raise argparse.ArgumentTypeError("'%s' is not a valid path"%(path))

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
    if os.path.exists(file):
        os.remove(file)
    f = open(file, "w")
    for elem in list:
        f.write(elem+"\n")
    f.close()


def runPersonDetect(root_dir,date,hour, odapi,threshold ):
    cur_dir = os.path.join(root_dir,date,hour)

    log_message("Running person detect on.. "+cur_dir)
    images = get_files(cur_dir, "jpg")
    p_images = []
    p_only_images = []

    for x in images:
        try:
            img = cv2.imread(cur_dir+"/"+x)
            img = cv2.resize(img, (640, 360))
        except Exception as e:
            print("Error reading file:"+x)
            continue
        boxes, scores, classes, num = odapi.processFrame(img)
        label = ""
        for i in range(len(boxes)):
            box = boxes[i]
            # Class 1 represents human
            if classes[i] == 1 and scores[i] > threshold:
                label = label + "%d %d %d %d %s "%(box[0],box[1],box[2],box[3],str(round(scores[i]*100, 2)))

        if label != "":
            p_images.append(str(x)+" "+label)
            p_only_images.append(x)

    person_txt = os.path.join(cur_dir,"person.txt")
    writeListToFile(p_images,person_txt)

    others_txt = os.path.join(cur_dir,"others.txt")
    other_list = []
    for img in images:
        if img not in p_only_images:
            other_list.append(img)
    writeListToFile(other_list,others_txt)

    return len(images)