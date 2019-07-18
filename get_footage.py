from shared import *
import urllib.request

REMOTE_URL = "http://earwiggy-sealion-5900.dataplicity.io"
LOCAL_PATH = "C:/Users/sgudla/Downloads/cctv"

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

today = datetime.datetime.now().strftime("%Y-%m-%d")
getFootage(REMOTE_URL,"GatePhotos", today)
getFootage(REMOTE_URL,"StairsPhotos", today)