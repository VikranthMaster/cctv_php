from shared import *
import urllib.request

REMOTE_URL = "http://earwiggy-sealion-5900.dataplicity.io"
LOCAL_PATH = "C:/Users/sgudla/Downloads/cctv"

def get_remote_file(remote_url, local_url):
    flag = os.path.basename(remote_url) == "person.txt"
    try:
        urllib.request.urlretrieve(remote_url, local_url)
        f = open(local_url)
        while 1:
            line = f.readline().rstrip()
            if not line:
                break
            if flag == True:
                img_r_url = os.path.dirname(remote_url)+"/"+line
            else:
                img_r_url = os.path.dirname(remote_url)+"/thumbnails/"+line

            img_local_path = os.path.join(os.path.dirname(local_url),line)
            try:
                urllib.request.urlretrieve(img_r_url, img_local_path)
            except Exception as e:
                print("Error getting image file:"+img_r_url)
                print(e)
        f.close()

    except Exception as e:
        print("Error getting file:"+remote_url)
        print(e)

def getFootage(url, camera, date):
    date_url = url+"/"+camera+"/"+date
    ensure_dir_exists(os.path.join(LOCAL_PATH,camera,date))
    print("Getting footage from : "+date_url)
    for hour in range(0,24):
        hour = "%02dhour"%(hour);
        print("Getting images on "+hour)
        person_url = date_url+"/"+hour+"/person.txt"
        other_url = date_url+"/"+hour+"/others.txt"
        ensure_dir_exists(os.path.join(LOCAL_PATH,camera,date,hour))
        get_remote_file(person_url, os.path.join(LOCAL_PATH,camera,date,hour,"person.txt"))
        get_remote_file(other_url, os.path.join(LOCAL_PATH,camera,date,hour,"others.txt"))

today = datetime.datetime.now().strftime("%Y-%m-%d")
getFootage(REMOTE_URL,"GatePhotos", today)
getFootage(REMOTE_URL,"StairsPhotos", today)