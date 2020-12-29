import os
import datetime
from shared2 import *
from database import *

def getVideoTimeStamp(camID, date, file):
    base = os.path.basename(file).split('[')[0].replace(".", ":")
    sp = base.split("-")
    fmt = "%H:%M:%S"
    duration = datetime.datetime.strptime(sp[1], fmt)-datetime.datetime.strptime(sp[0],fmt)
    timestamp = sp[0]
    return (timestamp, duration.total_seconds())

def getPhotoTimeStamp(camID, date, file):
    if camID==1:
        base = os.path.basename(file)
        base = base.split('[')[0].replace(".", ":")
        return base
    else:
        file = file.split('[')[0]
        sp = file.split("/")
        time = sp[-3]+":"+sp[-2]+":"+sp[-1]
        return time

def addFootage():
    # Connect to MariaDB Platform
    try:
        conn = getDBConnection()
    
        # Get Cursor
        cur = conn.cursor()
    
        cur.execute("""
                        select c.UID as cameraID, cd.UID as camDateID, d.date, c.rootdir, c.cachedir
	                    from CameraDate as cd
                        join Date as d on cd.dateID=d.UID
                        join Camera as c on c.UID=cd.cameraID
                        order by date desc;
		            """)

        camDates = []
        for cameraID, camDateID, date, rootDir, cacheDir in cur:
            camDates.append((cameraID, camDateID, str(date), rootDir, cacheDir))

        curDate = getCurrentDate()

        for camID, camDateID, date, rootDir, cacheDir in camDates:
            print ("CamDateID={}, Date={}, Root={}".format(camDateID, date, rootDir))
            photos = findFiles(os.path.join(rootDir, date), "jpg")
            exist = False
            for photo in photos:
                relPath = os.path.relpath(photo, os.path.join(rootDir,date))
                size = os.path.getsize(photo)
                timestamp = getPhotoTimeStamp(camID, date, relPath)
                thumbnailPath = os.path.join(cacheDir, date, str(timestamp).replace(":","_")+".jpg")
                if not os.path.exists(thumbnailPath):
                    try:
                        cv2_img = cv2.imread(photo)
                        cv2_img = cv2.resize(cv2_img, (640, 360))
                        #print ("Save {} file to {}".format(photo,thumbnailPath))
                        cv2.imwrite(thumbnailPath, cv2_img)
                    except:
                        log_message("Error saving thumbnail:"+photo)
                else:
                    exist = True
                    thumbsize = os.path.getsize(thumbnailPath)
                    break
                cur.execute("INSERT IGNORE INTO Photo(cameraDateID, filepath, time, size, thumbSize) values(?,?,?,?,?)", (camDateID, relPath, timestamp, size, thumbsize))
                cur.execute("UPDATE Photo SET thumbSize=? WHERE cameraDateID=? AND time=?", (thumbsize, camDateID,timestamp))
               
            print ("{} photos added on {}".format(len(photos), date))
            if not exist:
                break

        conn.commit()
        conn.close()
    
    except mariadb.Error as e:
        print("Error connecting to MariaDB Platform: {}".format(e))

addFootage()
