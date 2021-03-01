#!/bin/sh

# for apache2 run under named user
echo 'Reload named'
/usr/sbin/rndc reload || /bin/kill -HUP /var/run/named/named.pid
echo 'stop named'
/usr/sbin/rndc stop || /bin/kill -TERM /var/run/named/named.pid
echo 'check named configs'
/usr/sbin/named-checkconf -z /etc/named.conf
echo 'start named under named user'
/usr/sbin/named -u named -c /etc/named.conf
