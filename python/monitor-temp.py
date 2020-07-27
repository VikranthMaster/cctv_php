import os
import datetime
import time

def measure_temp():
        temp = os.popen("vcgencmd measure_temp").readline()
        temp = temp.replace("temp=","")
	time = datetime.datetime.now().strftime("%Y-%m-%d %H:%M")
	return "Temperature measured at %s is %s"%(time, temp)

cur_time = datetime.datetime.now()
temp_log = "/mnt/hdd/logs/temperature/log_"+cur_time.strftime("%Y-%m-%d")+".txt"
str = measure_temp()
print(str)

f = open(temp_log, "a")
f.write(str+"\n")
f.close()
