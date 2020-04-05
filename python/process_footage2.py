#!/usr/bin/python
from shared2 import *

def isProcessDoneDt(camera, date):
    cacheDir = getCacheDirDt(camera,date)
    ensure_dir_exists(cacheDir)
    dtDoneFile = os.path.join(cacheDir, "process.done")
    return os.path.exists(dtDoneFile)

def markProcessDoneDt(camera, date):
    cacheDir = getCacheDirDt(camera,date)
    ensure_dir_exists(cacheDir)
    dtDoneFile = os.path.join(cacheDir, "process.done")
    if(date!=getCurrentDate()):
        # Creates a new file
        print("Marking the date as processed\n")
        with open(dtDoneFile, 'w') as fp: 
            pass

def isProcessDoneHr(camera, date, hr):
    cacheDir = getCacheDirHr(camera,date, hr)
    ensure_dir_exists(cacheDir)
    hrDoneFile = os.path.join(cacheDir, "process.done")
    return os.path.exists(hrDoneFile)
         
def markProcessDoneHr(camera, date, hr):
    cacheDir = getCacheDirHr(camera,date, hr)
    ensure_dir_exists(cacheDir)
    hrDoneFile = os.path.join(cacheDir, "process.done")
    if(hr[:2]!=getCurrentHour()):
        # Creates a new file
        print("Marking the hour as processed\n")
        with open(hrDoneFile, 'w') as fp: 
            pass

def processDate(camera, date):
    if isProcessDoneDt(camera,date):
       return
    print("\nProcess Date:" + str(dt))

    cacheDir = getCacheDirDt(camera,date)

    for hrDir in getHourDirs(camera,date):
        hr = os.path.basename(hrDir)

        if isProcessDoneHr(camera,date, hr):
            continue

        print("Process Hour:"+ hr)
        
        imgs = getImages(camera,hrDir)
        for img in imgs:
            generateThumbNail(camera, date, hr, img)

        markProcessDoneHr(camera, date, hr)
    markProcessDoneDt(camera,date)
        
# Execution starts here...
addMarkerLine()
log_message("Running process_footage at: " + datetime.datetime.now().strftime("%Y-%m-%d %H:%M"))

for cam in (Cameras):
    print("\nProcessing Camera:" + str(cam))
    for dt in getDateDirs(cam):
        processDate(cam,dt)    
