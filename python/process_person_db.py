#!/usr/bin/python
from shared2 import *
from detector import DetectorAPI
from database import *

# Execution starts here...
log_message("Running process_person at: " + datetime.datetime.now().strftime("%Y-%m-%d %H:%M"))

model_path = '/home/pi/person_detect_models/latest/frozen_inference_graph.pb'
odapi = DetectorAPI(path_to_ckpt=model_path)
threshold = 0.45
start_time = time.time()

try:
    conn = getDBConnection()

    # Get Cursor
    cur = conn.cursor()

    cur.execute("""
                    select p.UID as PhotoUID, c.rootdir as RootDir, dt.date as Date, p.filepath as FilePath 
                        from Photo as p
                        join CameraDate as cd on p.cameraDateID=cd.UID
                        join Date as dt on dt.UID = cd.dateID
                        join Camera as c on cd.cameraID=c.UID
                        where p.processed = false;
		            """)

    photos = []
    for PhotoUID, RootDir, Date, FilePath in cur:
        photos.append((PhotoUID, RootDir, str(Date), FilePath))

    rows = []
    for PhotoUID, RootDir, Date, FilePath in photos:
        fullpath = os.path.join(RootDir, Date, FilePath)
        if not os.path.exists(fullpath):
            continue
        #print("Running person detect on {}".format(fullpath))
        try:
            img = cv2.imread(fullpath)
            img = cv2.resize(img, (640, 360))
        except Exception as e:
            print("Error reading file:"+fullpath)
            continue
        boxes, scores, classes, num = odapi.processFrame(img)
        label = ""
        for i in range(len(boxes)):
            box = boxes[i]
            # Class 1 represents human
            if classes[i] == 1 and scores[i] > threshold:
                label = label + "%d %d %d %d %s "%(box[0],box[1],box[2],box[3],str(round(scores[i]*100, 2)))
                #print(label)
                rows.append((PhotoUID, 1, box[0], box[1], box[2], box[3], scores[i]*100))
                #print("Found person for image: {}".format(fullpath))
        cur.execute("UPDATE Photo SET processed=TRUE WHERE UID=?",(PhotoUID,))

    total_time = time.time() - start_time
    st = ("Person detect ran at %s on %d images and took %d minutes and %d seconds\n")%(datetime.datetime.now().strftime("%Y-%m-%d %H:%M"),len(photos),total_time/60, total_time%60)
    print(st)

    for one, two, three, four, five, six, seven in rows:
        cur.execute("INSERT IGNORE INTO Detection(photoID, objectID, x1, y1, x2, y2, probability) values (?,?,?,?,?,?,?)",(one, two, three, four, five, six, seven))

    conn.commit()
    conn.close()

except mariadb.Error as e:
    print("Error connecting to MariaDB Platform: {}".format(e))

addMarkerLine()
