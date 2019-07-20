#!/bin/bash
echo "Running nighlty script on $(date)" >> /mnt/hdd/logs/nightly/log.txt
python3 /home/pi/cctv_php/get_footage.py &>> /mnt/hdd/logs/nightly/log.txt
