#!/usr/bin/python
import os.path
import glob
from shared import *

gate_root_dir = "/mnt/hdd/tmp/GateCamera/"
gate_target_imgs_dir = photo_root_dirs[0]
gate_target_vids_dir = video_root_dirs[0]

stairs_root_dir = "/mnt/hdd/tmp/StairsCamera/"
stairs_target_imgs_dir = photo_root_dirs[1]
stairs_target_vids_dir = video_root_dirs[1]

updated_dirs = set()


def get_hour_dir(img,camera):
    if camera.lower()=="gate":
        return img.split('/')[6]
    if camera.lower()=="stairs":
        return img.split('/')[8]+"hour"


def get_stairs_minute(img):
    return img.split('/')[9]


def get_target_file_name(img, camera):
    if camera.lower()=="gate":
        return os.path.split(img)[1]
    if camera.lower()=="stairs":
        hr = get_hour_dir(img, camera)
        return hr[:2] + "_" + get_stairs_minute(img) + "_" + os.path.split(img)[1]


def move_images(source_dir, target_dir, date, camera):
    imgs = []
    for img in  glob.iglob(source_dir + '/**/*.jpg', recursive=True):
        imgs.append(img)

    #print("No. of images to be moved are :"+str(len(imgs)))
    if len(imgs)!=0:
        ensure_dir_exists(os.path.join(target_dir, date))
        for img in imgs:
            hr = get_hour_dir(img,camera)
            ensure_dir_exists(os.path.join(target_dir, date, hr))
            thumbnail_dir = os.path.join(target_dir, date, hr, "thumbnails")
            ensure_dir_exists(thumbnail_dir)
            file_name = get_target_file_name(img, camera)
            dest_path = os.path.join(target_dir, date, hr, file_name)
            try:
                ffmpeg.input(img).output(dest_path).run()
                os.remove(dest_path)
                updated_dirs.add(target_dir+":"+date+":"+hr)
            except Exception as ex:
                log_message("Error moving file:"+img)
                log_message(str(ex))
                continue

            try:
                ffmpeg.input(dest_path).filter('scale',400,-1).output(os.path.join(thumbnail_dir,file_name)).run()
            except Exception as ex:
                log_message("Error saving thumbnail image for:"+img)
                log_message(str(ex))
                continue


def move_videos(source_dir, target_dir, date, camera):
    vids = []
    for vid in  glob.iglob(source_dir + '/**/*.mp4', recursive=True):
        vids.append(vid)

    #print("No. of videos to be moved are :"+str(len(vids)))
    if len(vids)!=0:
        ensure_dir_exists(os.path.join(target_dir, date))
        for vid in vids:
            hr = get_hour_dir(vid,camera)
            ensure_dir_exists(os.path.join(target_dir, date, hr))
            try:
                shutil.move(vid, os.path.join(target_dir, date, hr, os.path.split(vid)[1]))
                updated_dirs.add(target_dir+":"+date+":"+hr)
            except Exception as ex:
                log_message("Error moving file:"+vid)
                log_message(str(ex))
                continue

# Execution starts here...
addMarkerLine()
log_message("Running process_footage at: " + datetime.datetime.now().strftime("%Y-%m-%d %H:%M"))

if not check_hdd():
    exit(0)

gate_date_dirs = get_sub_dirs(gate_root_dir)
log_message("Moving Gate Footage in :"+ str(gate_date_dirs))
for date in gate_date_dirs:
    source_dir = gate_root_dir + date
    move_images(source_dir, gate_target_imgs_dir, date, "gate")
    move_videos(source_dir, gate_target_vids_dir, date, "gate")

stairs_date_dirs = get_sub_dirs(stairs_root_dir)
log_message("Moving Stairs Footage in :"+ str(stairs_date_dirs))
for date in stairs_date_dirs:
    source_dir = stairs_root_dir + date
    move_images(source_dir, stairs_target_imgs_dir, date, "stairs")
    move_videos(source_dir, stairs_target_vids_dir, date, "stairs")

log_message("Updated directories:"+str(updated_dirs))

