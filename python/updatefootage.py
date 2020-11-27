import os
import datetime
import time
import mariadb
from shared2 import *

def getVideoTimeStamp(camID, date, file):
    base = os.path.basename(file).split('[')[0].replace(".", ":")
    sp = base.split("-")
    fmt = "%H:%M:%S"
    duration = datetime.datetime.strptime(sp[1], fmt)-datetime.datetime.strptime(sp[0],fmt)
    timestamp = str(date) + " " + sp[0]
    return (timestamp, duration.total_seconds())

def getPhotoTimeStamp(camID, date, file):
    if camID==1:
        base = os.path.basename(file)
        base = base.split('[')[0].replace(".", ":")
        return str(date) + " " +base
    else:
        file = file.split('[')[0]
        sp = file.split("/")
        time = sp[-3]+":"+sp[-2]+":"+sp[-1]
        return str(date) + " " + time

def addFootage():
    # Connect to MariaDB Platform
    try:
        conn = mariadb.connect(
            user="root",
            password="password",
            host="localhost",
            port=3306,
            database="cctv")
    
        # Get Cursor
        cur = conn.cursor()
    
        cur.execute("""SELECT cameraID, CameraDate.UID as camDateID, date, rootDir, fetched 
			FROM Camera JOIN CameraDate ON Camera.UID=cameraID
		   """)

        camDates = []
        for cameraID, camDateID, date, rootDir, fetched in cur:
            if fetched==1:
                continue
            camDates.append((cameraID, camDateID, str(date), rootDir))

        curDate = getCurrentDate()
        #print ("CurrentDate is : {}".format(curDate))

        for camID, camDateID, date, rootDir in camDates:
            if curDate!=date:
                #print ("This is not current date: {}".format(date))
                cur.execute("UPDATE CameraDate SET fetched = TRUE WHERE UID=?", (camDateID,))
                
            #print ("CamDateID={}, Date={}, Root={}".format(camDateID, date, rootDir))
            photos = findFiles(os.path.join(rootDir, date), "jpg")
            videos = findFiles(os.path.join(rootDir, date), "mp4")
            for photo in photos:
                relPath = os.path.relpath(photo, os.path.join(rootDir,date))
                timestamp = getPhotoTimeStamp(camID, date, relPath)
                cur.execute("INSERT IGNORE INTO Photo(cameraDateID, filepath, time) values(?,?,?)", (camDateID, relPath, timestamp))
               
            for video in videos:
                relPath = os.path.relpath(video, os.path.join(rootDir,date))
                timestamp, duration = getVideoTimeStamp(camID, date, relPath)
                cur.execute("INSERT IGNORE INTO Video(cameraDateID, filepath, time, duration) values(?,?,?,?)", (camDateID, relPath, timestamp, duration))

            #print ("{} photos added on {}".format(len(photos), date))
            #print ("{} videos added on {}".format(len(videos), date))

        conn.commit()
        conn.close()
    
    except mariadb.Error as e:
        print("Error connecting to MariaDB Platform: {}".format(e))

addFootage()
