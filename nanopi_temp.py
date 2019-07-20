import datetime

f = open("/sys/devices/virtual/thermal/thermal_zone0/temp","r")
temp = int(f.read().rstrip())/1000
time = datetime.datetime.now().strftime("%Y-%m-%d %H:%M")
print("Temperature at %s is %.02f C"%(time, temp))