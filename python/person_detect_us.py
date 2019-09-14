from shared import *
from detector import DetectorAPI
from pytz import timezone

gate_model_path = '/home/pi/Downloads/person_gate_model/frozen_inference_graph.pb'
stairs_model_path = '/home/pi/Downloads/person_stairs_model/frozen_inference_graph.pb'
threshold = 0.9

for photo_root in photo_root_dirs:
    if "StairsPhotos" in photo_root:
        odapi = DetectorAPI(path_to_ckpt=stairs_model_path)
    else:
        odapi = DetectorAPI(path_to_ckpt=gate_model_path)

    # Runs upto the specified date (not including the date)
    india = timezone('Asia/Calcutta')
    today = datetime.datetime.now(india)
    yday = today-datetime.timedelta(days=1)
    today_date = today.strftime("%Y-%m-%d") 
    yday_date = yday.strftime("%Y-%m-%d") 

    days = [today_date,yday_date]
    dt = datetime.datetime.strptime("2019-05-14", "%Y-%m-%d").date()

    for date_dir in days:
        if not os.path.exists(os.path.join(photo_root,date_dir)):
            continue       
        
        print("Running on :"+os.path.join(photo_root,date_dir))

        for hr_dir in get_sub_dirs(os.path.join(photo_root,date_dir)):
            cur_dir = os.path.join(photo_root,date_dir,hr_dir)
            if os.path.exists(os.path.join(cur_dir,'person_detect_us.done')):
                continue
            runPersonDetect(photo_root,date_dir,hr_dir,odapi,threshold)
            open(os.path.join(cur_dir,'person_detect_us.done'), 'a').close()

addMarkerLine()
