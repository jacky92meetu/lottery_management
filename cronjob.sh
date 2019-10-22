#!/bin/sh

result=$(/usr/bin/php /var/www/html/myfd/linux_cronjob/check.php | grep -c "valid")
if [ $result -eq 0 ]
then
	#/usr/bin/killall -q -g -o 2m php > /dev/null
	pgrep php | xargs kill -9 > /dev/null
fi

result=$(/bin/ps -ef)
result=$(echo $result | /bin/grep -c "php cronjob.php")

if [ $result -eq 0 ]
then
	cd /var/www/html/myfd/linux_cronjob/
        /usr/bin/php cronjob.php
else
        echo "The process was running!"
fi

exit
