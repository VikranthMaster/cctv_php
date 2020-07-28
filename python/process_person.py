#!/usr/bin/python
from shared2 import *
from detector import DetectorAPI

def writeListToFile(list, file):
    if os.path.exists(file):
        os.remove(file)
    f = open(file, "w")
    for elem in list:
        f.write(elem+"\n")
    f.close()

def get_files(parent_dir, extension):
    return [x for x in os.listdir(parent_dir) if x.endswith(extension)]

def person_detect(odapi,camera, date, hr, hrDir):
    imgs = getImages(camera, hrDir)

    start_time = time.time()
    threshold = 0.45
    p_images = []
    p_only_images = []

    for x in imgs:
        try:
            img = cv2.imread(x)
            img = cv2.resize(img, (640, 360))
        except Exception as e:
            print("Error reading file:"+x)
            continue
        boxes, scores, classes, num = odapi.processFrame(img)
        label = ""
        for i in range(len(boxes)):
            box = boxes[i]
            # Class 1 represents human
            if classes[i] == 1 and scores[i] > threshold:
                label = label + "%d %d %d %d %s "%(box[0],box[1],box[2],box[3],str(round(scores[i]*100, 2)))

        if label != "":
            p_images.append(str(x)+" "+label)
            p_only_images.append(x)

    cur_dir = getCacheDirHr(camera, date, hr)
    person_txt = os.path.join(cur_dir,"person.txt")
    writeListToFile(p_images,person_txt)

    others_txt = os.path.join(cur_dir,"others.txt")
    other_list = []
    for img in imgs:
        if img not in p_only_images:
            other_list.append(img)
    writeListToFile(other_list,others_txt)
    total_time = time.time() - start_time
    st = ("Person detect ran at %s on %d images and took %d minutes and %d seconds\n")%(datetime.datetime.now().strftime("%Y-%m-%d %H:%M"),len(imgs),total_time/60, total_time%60)
    print(st)

def isProcessPersonDoneDt(camera, date):
    cacheDir = getCacheDirDt(camera,date)
    ensure_dir_exists(cacheDir)
    dtDoneFile = os.path.join(cacheDir, "person.done")
    return os.path.exists(dtDoneFile)

def markProcessPersonDoneDt(camera, date):
    cacheDir = getCacheDirDt(camera,date)
    ensure_dir_exists(cacheDir)
    dtDoneFile = os.path.join(cacheDir, "person.done")
    if(date!=getCurrentDate()):
        # Creates a new file
        print("Marking the date as processed\n")
        with open(dtDoneFile, 'w') as fp: 
            pass

def isProcessPersonDoneHr(camera, date, hr):
    cacheDir = getCacheDirHr(camera,date, hr)
    ensure_dir_exists(cacheDir)
    hrDoneFile = os.path.join(cacheDir, "person.done")
    return os.path.exists(hrDoneFile)
         
def markProcessPersonDoneHr(camera, date, hr):
    cacheDir = getCacheDirHr(camera,date, hr)
    ensure_dir_exists(cacheDir)
    hrDoneFile = os.path.join(cacheDir, "person.done")
    if(hr[:2]!=getCurrentHour()):
        # Creates a new file
        print("Marking the hour as processed\n")
        with open(hrDoneFile, 'w') as fp: 
            pass

def processPersonDate(odapi, camera, date):
    if isProcessPersonDoneDt(camera,date):
       return
    print("\nProcessPerson Date:" + str(dt))

    cacheDir = getCacheDirDt(camera,date)

    for hrDir in getHourDirs(camera,date):
        hr = os.path.basename(hrDir)

        if isProcessPersonDoneHr(camera,date, hr):
            continue

        if date==getCurrentDate() and hr[:2]==getCurrentHour():
            continue
        print("ProcessPerson Hour:"+ hr)
        person_detect(odapi, camera, date, hr, hrDir)
        
        markProcessPersonDoneHr(camera, date, hr)
    markProcessPersonDoneDt(camera,date)
        
# Execution starts here...
addMarkerLine()
log_message("Running process_person at: " + datetime.datetime.now().strftime("%Y-%m-%d %H:%M"))

model_path = '/home/pi/person_detect_models/latest/frozen_inference_graph.pb'
odapi = DetectorAPI(path_to_ckpt=model_path)
today = datetime.datetime.now().date()
for cam in (Cameras):
    print("\nProcessing Camera:" + str(cam))
    for dt in getDateDirs(cam):
        date = datetime.datetime.strptime(dt, "%Y-%m-%d").date()
        elapsed_days = (today-date).days
        if elapsed_days>2:
            continue

        processPersonDate(odapi,cam,dt)    
