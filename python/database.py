import os
import datetime
import time
import mariadb
from shared import *

USER = "sgudla"   # "root"
PASSWORD = "Gsrini@1234"  # "password"
HOST = "192.168.1.103"
PORT = 3306
DATABSE = "cctv2"

def getDBConnection():
    # Connect to MariaDB Platform
    try:
        connection = mariadb.connect(
            user=USER,
            password=PASSWORD,
            host=HOST,
            port=PORT,
            database=DATABSE)
        
        return connection
    except mariadb.Error as e:
        print("Error connecting to MariaDB Platform: {}".format(e))

def getCameraRecords():
    try:
        conn = getDBConnection()
    
        # Get Cursor
        cur = conn.cursor()
        cur.execute("SELECT name, rootdir, cachedir FROM Camera")
        result = cur.fetchall()
        conn.close()
        return result 
    
    except mariadb.Error as e:
        print("Error adding date to DB: {}".format(e))

def addDateToDB(date):
    try:
        conn = getDBConnection()
    
        # Get Cursor
        cur = conn.cursor()
        cur.execute("INSERT IGNORE INTO Date(date) values(?)", (date,))
        conn.commit()
        conn.close()
    
    except mariadb.Error as e:
        print("Error adding date to DB: {}".format(e))

def addCameraDateToDB(camera, date):
    addDateToDB(date)
    try:
        conn = getDBConnection()
    
        # Get Cursor
        cur = conn.cursor()
        cur.execute(""" 
                        INSERT IGNORE INTO CameraDate(cameraID, dateID) VALUES(
				                (SELECT UID from Camera where name=?),
				                (SELECT UID from Date where date=?))
                    """, (camera, date))
        conn.commit()
        conn.close()
    
    except mariadb.Error as e:
        print("Error adding CameraDate to DB: {}".format(e))


def addTemperatureToDB(date, time, temp):
    addDateToDB(date)
    try:
        conn = getDBConnection()

        # Get Cursor
        cur = conn.cursor()
        cur.execute(""" 
                        INSERT INTO Temperature(dateID, time, temp) VALUES(
				                (SELECT UID from Date where date=?), ?, ?)
                    """, (date, time, temp))
        conn.commit()
        conn.close()

    except mariadb.Error as e:
        print("Error adding temperature to DB: {}".format(e))

addCameraDateToDB("Gate", "2010-11-12")
addTemperatureToDB("2010-11-11", "11:30:01", 23.5)
print(getCameraRecords())
