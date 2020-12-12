import os
import mariadb
from shared2 import *
from database import *

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

def addDetections():
    # Connect to MariaDB Platform
    try:
        conn = getDBConnection()

        # Get Cursor
        cur = conn.cursor()
    
        cur.execute("""
                        select c.UID as cameraID, cd.UID as camDateID, d.date, c.cachedir, cd.processed
	                    from CameraDate as cd
                        join Date as d on cd.dateID=d.UID
                        join Camera as c on c.UID=cd.cameraID;
		   """)

        dates = []
        for cameraID, camDateID, date, cachedir, processed in cur:
            if processed==1:
                continue
            dates.append((cameraID, camDateID, date, cachedir))

        r = cur.fetchall()
        cur.close()
        cur = conn.cursor()
        for CamID, CamDateID, Date, Cache in dates:
            print ("Date={}, Cache={}".format(str(Date),Cache))
            files = findFiles(os.path.join(Cache,str(Date)),"txt")
            for per in files:
                if not per.endswith("person.txt"):
                   continue
                f = open(per,'r')
                for line in f.readlines():
                    print(line)
                    sp = line.split()
                    ts = getPhotoTimeStamp(CamID, str(Date),sp[0])
                    print("CamDateID={}, Timestamp={}".format(CamDateID,ts))
                    cur.execute("SELECT UID from Photo WHERE cameraDateID=? and time=?", (CamDateID,ts))
                    r = cur.fetchall()
                    cur.close()
                    cur = conn.cursor()
                    photoID = r[0][0]
                    print("PhotID={}".format(photoID))
                    print("File={}, x1={}, y1={}, x2={}, y2={}, prob={}".format(sp[0],sp[1],sp[2],sp[3],sp[4],sp[5]))
                    cur.execute("INSERT IGNORE INTO Detection(photoID, objectID, x1, y1, x2, y2, probability) values (?,?,?,?,?,?,?)",(photoID, 1, sp[1], sp[2], sp[3], sp[4], sp[5]))
                print (per)
                break

            break
            print ("UID={}, Thummpath={}".format(UID, thumbpath))
            # cur.execute("UPDATE Photo SET thumbnail = ? WHERE UID=?",(thumbpath,UID))

        conn.commit()
        conn.close()
    
    except mariadb.Error as e:
        print("Error connecting to MariaDB Platform: {}".format(e))

addDetections()
