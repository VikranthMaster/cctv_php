from shared import *
from database import *

for CameraName, RootDir, CacheDir in getCameraRecords():
    cameraDates = []
    for date in get_sub_dirs(RootDir):
        cameraDates.append((CameraName, date))
        addCameraDateToDB(CameraName, date)
