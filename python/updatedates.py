from shared import *
from database import *

for CameraName, RootDir, CacheDir in getCameraRecords():
    print(RootDir)
    for date in get_sub_dirs(RootDir):
        addCameraDateToDB(CameraName, date)