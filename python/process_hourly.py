#!/usr/bin/python
from shared2 import *

def get_files(parent_dir, extension):
    return [x for x in os.listdir(parent_dir) if x.endswith(extension)]

def remove_duplicates(camera, date, hr, hrDir):
    imgs = getImages(camera, hrDir)
    imgs.sort()
    to_del = []
    for index in range(0,len(imgs)-1):
        one = cv2.imread(imgs[index])
        one = cv2.resize(one, (640, 360))
        two = cv2.imread(imgs[index+1])
        two = cv2.resize(two, (640, 360))

        # convert the images to grayscale
        one = cv2.cvtColor(one, cv2.COLOR_BGR2GRAY)
        two = cv2.cvtColor(two, cv2.COLOR_BGR2GRAY)

        diff = mse(one,two)
        if(diff<100):
            to_del.append(imgs[index])

    for img in to_del:
        thmImg = getThumbImgPath(camera, date, hr, img)
        try:
            os.remove(thmImg)
            os.remove(img)
        except Exception as error:
            print("Error deleting duplicate image:"+img)
    print("Total items to be deleted: %d out of %d" % (len(to_del),len(imgs)))

def isProcessHourlyDoneDt(camera, date):
    cacheDir = getCacheDirDt(camera,date)
    ensure_dir_exists(cacheDir)
    dtDoneFile = os.path.join(cacheDir, "hourly.done")
    return os.path.exists(dtDoneFile)

def markProcessHourlyDoneDt(camera, date):
    cacheDir = getCacheDirDt(camera,date)
    ensure_dir_exists(cacheDir)
    dtDoneFile = os.path.join(cacheDir, "hourly.done")
    if(date!=getCurrentDate()):
        # Creates a new file
        print("Marking the date as processed\n")
        with open(dtDoneFile, 'w') as fp: 
            pass

def isProcessHourlyDoneHr(camera, date, hr):
    cacheDir = getCacheDirHr(camera,date, hr)
    ensure_dir_exists(cacheDir)
    hrDoneFile = os.path.join(cacheDir, "hourly.done")
    return os.path.exists(hrDoneFile)
         
def markProcessHourlyDoneHr(camera, date, hr):
    cacheDir = getCacheDirHr(camera,date, hr)
    ensure_dir_exists(cacheDir)
    hrDoneFile = os.path.join(cacheDir, "hourly.done")
    if(hr[:2]!=getCurrentHour()):
        # Creates a new file
        print("Marking the hour as processed\n")
        with open(hrDoneFile, 'w') as fp: 
            pass

def processHourlyDate(camera, date):
    if isProcessHourlyDoneDt(camera,date):
       return
    print("\nProcessHourly Date:" + str(dt))

    cacheDir = getCacheDirDt(camera,date)

    for hrDir in getHourDirs(camera,date):
        hr = os.path.basename(hrDir)

        if isProcessHourlyDoneHr(camera,date, hr):
            continue

        print("ProcessHourly Hour:"+ hr)
        remove_duplicates(camera, date, hr, hrDir)
        
        markProcessHourlyDoneHr(camera, date, hr)
    markProcessHourlyDoneDt(camera,date)
        
# Execution starts here...
addMarkerLine()
log_message("Running process_hourly at: " + datetime.datetime.now().strftime("%Y-%m-%d %H:%M"))

for cam in (Cameras):
    print("\nProcessing Camera:" + str(cam))
    for dt in getDateDirs(cam):
        processHourlyDate(cam,dt)    
