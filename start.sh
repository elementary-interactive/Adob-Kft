#!/bin/bash

# Start PHP-FPM
php-fpm &

# Start nginx
systemctl enable nginx
service nginx start

# Start Supervisord
supervisord &

# Wait for any process to exit
wait -n

# Exit with status of process that exited first
exit $?
