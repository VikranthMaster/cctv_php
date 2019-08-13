#!/bin/bash
python3 /home/pi/cctv_php/python/get_footage.py &>> /mnt/hdd/logs/process/log_$(date +%F).txt
