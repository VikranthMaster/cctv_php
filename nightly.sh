#!/bin/bash
echo "Running nighlty script on $(date)" >> /mnt/hdd/logs/nightly/log.txt
python3 /home/pi/cctv_php/daily.py &>> /mnt/hdd/logs/nightly/log.txt
