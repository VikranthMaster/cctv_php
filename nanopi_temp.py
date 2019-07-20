import os
import time
import datetime

def measure_temp():
	f = open("/sys/devices/virtual/thermal/thermal_zone0/temp","r")
	temp = int(f.read().rstrip())/1000
	f.close()
	time = datetime.datetime.now().strftime("%Y-%m-%d %H:%M")
	return "Temperature measured at %s is %.02f C"%(time, temp)

cur_time = datetime.datetime.now()
temp_log = "/mnt/hdd/logs/temperature/log_"+cur_time.strftime("%Y-%m-%d")+".txt"
str = measure_temp()
print(str)

f = open(temp_log, "a")
f.write(str+"\n")
f.close()
