#!/bin/bash

# for suid
echo 'stop named service'
#systemctl stop named.service
/usr/sbin/rndc stop || /bin/kill -TERM /var/run/named/named.pid
sleep 5
echo 'start named service'
systemctl start named.service
sleep 5
echo 'status named service'
systemctl status named.service

