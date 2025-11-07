#!/bin/bash
sudo rm -rf static
sleep 1
sudo rm index.html
sleep 1
sudo cp /home/ubuntu/build.zip ./
sleep 1
sudo unzip build.zip
sleep 1
sudo rm build.zip
sleep 1
sudo systemctl restart confideas.service
tail -f ../src/logger/confideas.log

