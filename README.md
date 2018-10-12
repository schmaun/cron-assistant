# cron-assistant
A small programm to help you identifiy if a cron is configured to be run on a given date/time.

## Installation
```
git clone ...
composer install
```

### Or
Download a not yet created phar file ;)

## Usage
```
./app.php --path <path where crontab files are.> <Date/Time>
```
### Examples
To find cronjobs that are configured for January 1st 2018:
```
./app.php --path /etc/cron.d/ 2018-01-01 
```
To find cronjobs that are configured for January 1st 2018 at 4o'clock (from 4 to 5):
```
./app.php --path /etc/cron.d/ 2018-01-01 04
```

To find cronjobs that are configured for January 1st 2018 at 4:10:
```
./app.php --path /etc/cron.d/ 2018-01-01 04:10
```
