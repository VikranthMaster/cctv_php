#!/bin/bash
echo "Running nighlty script on $(date)" >> /home/pi/www/logs/nightly/log.txt
python3 /home/pi/cctv_php/get_footage.py &>> /home/pi/www/logs/nightly/log.txt
