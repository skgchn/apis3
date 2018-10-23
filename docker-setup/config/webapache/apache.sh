#!/bin/bash
if [ ! -d "/var/run/apache2" ]; then
	mkdir "/var/run/apache2"
	chown usoft:usoft /var/run/apache2
fi
if [ -f "/var/run/apache2/apache2.pid" ]; then
	rm "/var/run/apache2/apache2.pid"
fi
/usr/sbin/apache2ctl -D FOREGROUND
