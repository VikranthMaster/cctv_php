#!/bin/bash
python3 /home/pi/cctv_php/python/process_hourly.py &>> /mnt/hdd/logs/process/log_$(date +%F).txt
python3 /home/pi/cctv_php/python/process_person.py &>> /mnt/hdd/logs/person_detect/log_$(date +%F).txt
