from shared import *
from detector import DetectorAPI

photo_root_dirs = ['C:\\Users\\sgudla\\Downloads\\cctv\\gate','C:\\Users\\sgudla\\Downloads\\cctv\\stairs']

gate_model_path = 'C:\\Users\\sgudla\\Downloads\\models\\person_gate_model\\frozen_inference_graph.pb'
stairs_model_path = 'C:\\Users\\sgudla\\Downloads\\models\\person_stairs_model\\frozen_inference_graph.pb'
threshold = 0.9

for photo_root in photo_root_dirs:
    if "stairs" in photo_root:
        odapi = DetectorAPI(path_to_ckpt=stairs_model_path)
    else:
        odapi = DetectorAPI(path_to_ckpt=gate_model_path)


    # Runs upto the specified date (not including the date)
    dt = datetime.datetime.strptime("2019-09-05", "%Y-%m-%d").date()

    for date_dir in get_sub_dirs(photo_root):
        cur_dt = datetime.datetime.strptime(date_dir, "%Y-%m-%d").date()
        elapsed_days = (dt-cur_dt).days
        if elapsed_days<1 :
            continue
        print(elapsed_days)
        for hr_dir in get_sub_dirs(os.path.join(photo_root,date_dir)):
            cur_dir = os.path.join(photo_root,date_dir,hr_dir)
            print("Run on :"+cur_dir)
            #runPersonDetect(photo_root,date_dir,hr_dir,odapi,threshold)