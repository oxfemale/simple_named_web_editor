#include <unistd.h>
int main() {
    setuid(0);
    execle("/bin/bash","bash","/usr/bin/systemctl restart named",(char*) NULL,(char*) NULL);
}