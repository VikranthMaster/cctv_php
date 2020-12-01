import os
import datetime
import time
import mariadb
from shared import *

USER = "root"
PASSWORD = ""
HOST = "localhost"
PORT = 3306
DATABSE = "cctv"

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

def addDateToDB(date):
    # Connect to MariaDB Platform
    try:
        conn = getDBConnection()
    
        # Get Cursor
        cur = conn.cursor()
        cur.execute("INSERT IGNORE INTO Date(date) values(?)", (date))
        conn.commit()
        conn.close()
    
    except mariadb.Error as e:
        print("Error connecting to MariaDB Platform: {}".format(e))

addDateToDB("2020-12-12")

