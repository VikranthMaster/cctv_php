import os
import datetime
import time
import mariadb
from shared import *

def addDates():
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
    
        cur.execute("SELECT UID, rootDir FROM Camera")
        cameras = []
        for UID, rootDir in cur:
            cameras.append((UID, rootDir))

        for camID, rootDir in cameras:
            for date in get_sub_dirs(rootDir):
                cur.execute("INSERT IGNORE INTO CameraDate(cameraID, date) values(?,?)", (camID, date))

        conn.commit()
        conn.close()
    
    except mariadb.Error as e:
        print("Error connecting to MariaDB Platform: {}".format(e))

addDates();

