#!/bin/bash
python3 /home/pi/cctv_php/cctv_human_detection.py &>> /mnt/hdd/logs/person_detect/log_$(date +%F).txt
