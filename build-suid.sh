#!/bin/sh

echo "dummy hoto compile code:";
echo "for compile C code on native linux need INSTALL developers tools:";
echo "sudo apt update";
echo "sudo apt install build-essential";
echo "cc -o suid suid.c";
echo "chmod u+s suid";
echo "ls -al -G suid;echo \"bye bye security;)\""

echo "Prepare build code.. ok Start:"
cc -o suid suid.c
echo "Setting sticky bit on file: set suid"
chmod u+s suid
ls -al suid
