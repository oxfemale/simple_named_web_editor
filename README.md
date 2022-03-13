# simple_named_web_editor
apache + php + ssl simple named/bind editor
#Protect Directory
AuthName "Restricted access"
AuthType Basic
AuthUserFile /var/www/topline.online/.htpasswd
Require valid-user
p.s.
for compile C code on native linux need developers tools:
$sudo apt update
$sudo apt install build-essential
cc -o suid suid.c
chmod u+s suid
ls -al -G suid;echo "bye bye security;)"
