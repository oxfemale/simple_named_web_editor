# simple_named_web_editor
apache + php + ssl simple named/bind editor
#Protect Directory
AuthName "Restricted access"
AuthType Basic
AuthUserFile /var/www/topline.online/.htpasswd
Require valid-user
