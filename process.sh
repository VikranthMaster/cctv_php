#!/bin/bash
python3 /home/pi/cctv_php/process_footage.py &>> /home/pi/www/logs/process/log_$(date +%F).txt
