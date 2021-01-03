from shared import *
from database import *
import shutil

while True:
    total, used, free = shutil.disk_usage("/mnt/hdd")
    perc = used/total*100
    print ("Used percentage is : {}".format(perc))
    if perc < 85:
        break

    try:
        conn = getDBConnection()

        # Get Cursor
        cur = conn.cursor()
        cur.execute("""
                      SELECT cd.UID as ID, rootdir as RootDir, cachedir as CacheDir, date as Date
                        from CameraDate as cd
                        join Camera as c on c.UID = cd.cameraID
                        join Date as dt on dt.UID = cd.dateID     
                        order by Date asc limit 2;
                        """)

        camDates = []
        for ID, RootDir, CacheDir, Date in cur:
            camDates.append((ID, RootDir, CacheDir, str(Date)))

        for ID, RootDir, CacheDir, Date in camDates:
            try:
                photodir = os.path.join(RootDir, Date)
                thumbdir = os.path.join(CacheDir, Date)
                print("Deleting: " + photodir)
                print("Deleting: " + thumbdir)
                if os.path.exists(photodir):
                    shutil.rmtree(photodir)
                if os.path.exists(thumbdir):
                    shutil.rmtree(thumbdir)
            except:
                print("Error deleting directory")

            print ("Delete Detections...")
            cur.execute("""
                        delete from Detection where UID in 
                            (SELECT d.UID 
                                from Detection as d
                                join Photo p on p.UID = d.photoID
                                where p.cameraDateID = ?);
                        """, (ID,))


            print ("Delete Photos...")
            cur.execute("delete from Photo where cameraDateID=?;", (ID,))

            print ("Delete Video...")
            cur.execute("delete from Video where cameraDateID=?;", (ID,))

            print ("Delete CameraDate...")
            cur.execute("delete from CameraDate where UID=?;", (ID,))

        print(camDates)
        conn.commit()
        conn.close()

    except mariadb.Error as e:
        print("Error connecting to MariaDB Platform: {}".format(e))
