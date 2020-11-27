import os
import datetime
import time
import mariadb

def addTempToDB(time, temp):
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
    
        #insert information 
        cur.execute("INSERT INTO Temperature VALUES (?, ?)", (time, temp)) 

        #cur.execute("SELECT time, temp FROM Temperature")
        #for time, temp in cur:
        #    print ("T={}, Tp={}".format(time, temp))

        conn.commit()
        conn.close()
    
    except mariadb.Error as e:
        print("Error connecting to MariaDB Platform: {}".format(e))

def measure_temp():
        temp = os.popen("vcgencmd measure_temp").readline()
        temp = temp.replace("temp=","")
        return temp[:-3]

cur_time = datetime.datetime.now()
temp = measure_temp()
addTempToDB(cur_time,temp)

