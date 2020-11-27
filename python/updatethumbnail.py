import os
import datetime
import time
import mariadb
from shared2 import *

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
    
        cur.execute("""
			select Photo.UID, c.name as Camera, cd.date as Date, hour(time) as Hour, c.cacheDir as Cache, c.rootDir as Root, filepath as FilePath 
			from Photo 
			join CameraDate as cd on cameraDateID=cd.UID
			join Camera as c on cd.cameraID=c.UID
                        where thumbnail is NULL
			order by time desc;
		   """)

        photos = []
        for UID, Camera, Date, Hour, Cache, Root, FilePath in cur:
            photos.append((UID, Camera, Date, Hour, Cache, Root, FilePath))

        for UID, Camera, Date, Hour, Cache, Root, FilePath in photos:
            cam = Cameras.Gate
            if (Camera=="Gate"):
                Hour = FilePath.split("/")[0]
            else:
                Hour = FilePath.split("/")[2]
                cam = Cameras.Stairs
            thumbpath = getThumbImgPath(cam,str(Date),Hour,os.path.join(Root,FilePath))
            thumbpath = os.path.relpath(thumbpath, os.path.join(Cache,str(Date)))
            #print ("Camera={}, Date={}, Hour={}, Cache={}, Root={}, File={}".format(Camera,Date,Hour,Cache,Root,FilePath))
            #print ("UID={}, Thummpath={}".format(UID, thumbpath))
            cur.execute("UPDATE Photo SET thumbnail = ? WHERE UID=?",(thumbpath,UID))

        print("Total Image={}".format(len(photos)))
        conn.commit()
        conn.close()
    
    except mariadb.Error as e:
        print("Error connecting to MariaDB Platform: {}".format(e))

addFootage()
