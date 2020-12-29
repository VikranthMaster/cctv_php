#!/bin/bash
bash /home/pi/cctv_php/bash/autologin.sh
python3 /home/pi/cctv_php/python/updatedates.py
python3 /home/pi/cctv_php/python/updatefootage.py
python3 /home/pi/cctv_php/python/process_person_db.py &>> /mnt/hdd/logs/person_detect/log_$(date +%F).txt
