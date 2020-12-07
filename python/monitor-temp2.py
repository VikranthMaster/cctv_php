import os
import datetime
from database import *

def measure_temp():
        temp = os.popen("vcgencmd measure_temp").readline()
        temp = temp.replace("temp=","")
        return temp[:-3]

cur_time = datetime.datetime.now()
temp = measure_temp()
date_time = str(cur_time).split()
addTemperatureToDB(date_time[0],date_time[1], temp)

