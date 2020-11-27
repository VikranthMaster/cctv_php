#!/usr/bin/python
import os.path
from shared import *
import enum

ROOT_DIR = "/mnt/hdd/tmp"
CACHE_DIR = "/mnt/hdd/tmp/cache" 

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

def findFiles(rootDir, ext):
    imgs = []
    for img in  glob.iglob(rootDir+ '/**/*.' + ext, recursive=True):
        imgs.append(img)
        
    return imgs

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

def getThumbImgPath(camera, date, hr, img):
    targetImgName = getThumbImgName(camera,img)
    targetImgPath = os.path.join(getCacheDirDt(camera,date),hr,targetImgName)
    return targetImgPath
    
def generateThumbNail(camera, date, hr, img):
    targetImgName = getThumbImgName(camera,img)
    targetImgPath = os.path.join(getCacheDirDt(camera,date),hr,targetImgName)
    
    if os.path.exists(targetImgPath):
        return

    print("Generating thumbnail for:"+str(img)+" at:"+ targetImgPath)

    try:
        cv2_img = cv2.imread(img)
        cv2_img = cv2.resize(cv2_img, (640, 360))
        cv2.imwrite(targetImgPath, cv2_img)
    except:
        log_message("Error saving thumbnail:"+img)
            

def getCacheDirDt(camera, date):
    rootDir = None
    if camera==Cameras.Gate:
        rootDir = GATE_CACHE_DIR
    else:
        rootDir = STAIRS_CACHE_DIR
    return os.path.join(rootDir,date)

def getCacheDirHr(camera, date, hr):
    return os.path.join(getCacheDirDt(camera, date), hr)

def getCurrentDate():
    curDate = datetime.datetime.now()
    dateStr = curDate.strftime("%Y-%m-%d")
    return dateStr

def getCurrentHour():
    curDate = datetime.datetime.now()
    hrStr = curDate.strftime("%H")
    return hrStr
