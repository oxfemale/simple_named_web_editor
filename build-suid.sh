#!/bin/sh

echo 'build'
cc -o suid suid.c
echo 'set suid'
chmod u+s suid
ls -al suid
