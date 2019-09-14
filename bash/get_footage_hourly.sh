#!/bin/bash
python3 /home/pi/cctv_php/python/get_footage.py &>> /mnt/hdd/logs/process/log_$(date +%F).txt
python3 /home/pi/cctv_php/python/person_detect_us.py &>> /mnt/hdd/logs/person_detect/log_$(date +%F).txt
