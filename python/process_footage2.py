#!/usr/bin/python
import os.path
from shared import *

ROOT_DIR = "/mnt/hdd/tmp"
CACHE_DIR = "/mnt/hdd/cache" 

GATE_DIR = os.path.join(ROOT_DIR, "GateCamera")
STAIRS_DIR = os.path.join(ROOT_DIR, "StairsCamera")

GATE_CACHE_DIR = os.path.join(CACHE_DIR, "GateCamera")
STAIRS_CACHE_DIR = os.path.join(CACHE_DIR, "StairsCamera")

# Using enum class create enumerations
class Cameras(enum.Enum):
   Gate = 1
   Stairs = 2

def getPhotoDir(camera):
    if camera==Cameras.Gate:
        return GATE_DIR
    else:
        return STAIRS_DIR

def getDateDirs(camera):
    camDir = getPhotoDir(camera)
    return get_sub_dirs(camDir)

def getHourDirs(camera, date):
    dateDir = getDateDirs(camera)
    hourDirs = []
    dateDirFull = os.path.join(getPhotoDir(camera), date)
    if camera == Cameras.Gate:
        hourDirs = get_sub_dirs(dateDirFull)
        hourDirs= list(map(lambda hrDir: os.path.join(dateDirFull,hrDir), hourDirs))
    else:
        tmpDirs = get_sub_dirs(dateDirFull)
        for tmp in tmpDirs:
            tmpFull = os.path.join(dateDirFull, tmp, "jpg")
            if not os.path.exists(tmpFull):
                continue
            for hrDir in get_sub_dirs(tmpFull):
                hourDirs.append(os.path.join(tmpFull,hrDir))
    return hourDirs

def getImages(cam, hoursDir):
    imgs = []
    if cam==Cameras.Gate:
        imgs = get_files(os.path.join(hoursDir, "jpg"),"jpg")
        imgs= list(map(lambda img: os.path.join(hoursDir,"jpg",img), imgs))
    else:
        for img in  glob.iglob(hoursDir + '/**/*.jpg', recursive=True):
            imgs.append(img)
        
    return imgs

def getThumbImgName(camera, img):
    if camera == Cameras.Gate:
        return os.path.basename(img)
    else:
        sp = img.split("/")
        return sp[-3]+"."+sp[-2]+"."+sp[-1]

def generateThumbNail(camera, date, hr, img):
    targetImgName = getThumbImgName(camera,img)
    targetImgPath = os.path.join(getCacheDir(camera,date),hr,targetImgName)
    
    if os.path.exists(targetImgPath):
        return

    print("Generating thumbnail for:"+str(img)+" at:"+ targetImgPath)

    try:
        cv2_img = cv2.imread(img)
        cv2_img = cv2.resize(cv2_img, (640, 360))
        cv2.imwrite(targetImgPath, cv2_img)
    except:
            log_message("Error saving thumbnail:"+img)
            

def getCacheDir(camera, date):
    rootDir = None
    if camera==Cameras.Gate:
        rootDir = GATE_CACHE_DIR
    else:
        rootDir = STAIRS_CACHE_DIR
    return os.path.join(rootDir,date)

def processDate(camera, date):
    curDate = datetime.datetime.now()
    dateStr = curDate.strftime("%Y-%m-%d")
    hrStr = curDate.strftime("%H")
    
    cacheDir = getCacheDir(camera,date)
    ensure_dir_exists(cacheDir)
    dtDoneFile = os.path.join(cacheDir, "done")
    if os.path.exists(dtDoneFile):
        return

    print("\nProcess Date:" + str(dt))

    for hrDir in getHourDirs(camera,date):
        hr = os.path.basename(hrDir)

        cacheHrDir = os.path.join(getCacheDir(camera,date),hr)
        ensure_dir_exists(cacheHrDir)
        
        hrDoneFile = os.path.join(cacheHrDir, "done")
        if os.path.exists(hrDoneFile):
            return


        print("Process Hour:"+ hrStr)
        
        imgs = getImages(camera,hrDir)
        for img in imgs:
            generateThumbNail(camera, date, hr, img)

        if(not hr.startswith(hrStr)):
            # Creates a new file 
            with open(hrDoneFile, 'w') as fp: 
                pass
        
    if(date!=curDate):
        # Creates a new file 
        with open(dtDoneFile, 'w') as fp: 
            pass
        
# Execution starts here...
addMarkerLine()
log_message("Running process_footage at: " + datetime.datetime.now().strftime("%Y-%m-%d %H:%M"))

for cam in (Cameras):
    print("\nProcessing Camera:" + str(cam))
    for dt in getDateDirs(cam):
        processDate(cam,dt)    


#if not check_hdd():
#    exit(0)


