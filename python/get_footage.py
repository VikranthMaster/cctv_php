from shared import *
import urllib.request
import time
from pytz import timezone

REMOTE_URL = "http://earwiggy-sealion-5900.dataplicity.io"
LOCAL_PATH = "/mnt/hdd"

def getFootageHour(url,camera,date,hour):
    ensure_dir_exists(os.path.join(LOCAL_PATH,camera,date))
    date_url = os.path.join(url,camera,date)
    tar = os.path.join(date_url, hour, hour+".tar.gz")
    if os.path.exists(os.path.join(LOCAL_PATH,camera,date,hour)):
        return

    print("Getting images on from: "+tar)
    try:
        tar_local = os.path.join(LOCAL_PATH,camera,date,hour+".tar.gz")
        urllib.request.urlretrieve(tar, tar_local)
        t = tarfile.open(tar_local)
        t.extractall(os.path.join(LOCAL_PATH,camera,date))
        t.close()
        os.remove(tar_local)
    except Exception as e:
        print("Error getting tar file:"+tar)
        print(e)

def getFootage(url, camera, date):
    date_url = url+"/"+camera+"/"+date
    ensure_dir_exists(os.path.join(LOCAL_PATH,camera,date))
    print("Getting footage from : "+date_url)
    for hour in range(0,24):
        hour = "%02dhour"%(hour);
        print("Getting images on "+hour)
        tar = date_url+"/"+hour+"/"+hour+".tar.gz"
        try:
            tar_local = os.path.join(LOCAL_PATH,camera,date,hour+".tar.gz")
            urllib.request.urlretrieve(tar, tar_local)
            t = tarfile.open(tar_local)
            t.extractall(os.path.join(LOCAL_PATH,camera,date))
            t.close()
            os.remove(tar_local)
        except Exception as e:
            print("Error getting tar file:"+tar)
            print(e)

start_time = time.time()
india = timezone('Asia/Calcutta')

for i in range(1,20):
    lasthour = datetime.datetime.now(india)-datetime.timedelta(hours=i)
    date = lasthour.strftime("%Y-%m-%d")
    hour = '%02dhour'%(lasthour.hour)

    getFootageHour(REMOTE_URL,"GatePhotos", date,hour)
    getFootageHour(REMOTE_URL,"StairsPhotos", date,hour)

total_time = time.time() - start_time
str = ("Get Footage took %d minutes and %d seconds\n")%(total_time/60, total_time%60)
print(str)
addMarkerLine()