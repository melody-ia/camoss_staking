# cdasset TEST
53 01 * * * /opt/php/bin/php -q /var/www/html/cdasset/m3cron/bonus.daily.php 
55 01 * * * /opt/php/bin/php -q /var/www/html/cdasset/m3cron/bonus.direct.php > /var/log/autocron.log 2>&1
57 01 * * * /opt/php/bin/php -q /var/www/html/cdasset/m3cron/bonus.rollup.php >  /var/log/autocron.log 2>&1
05 23 * * * /opt/php/bin/php -q /var/www/html/cdasset/m3cron/bonus.rank.php > /var/log/autocron.log 2>&1
