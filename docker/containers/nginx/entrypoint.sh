#!/bin/bash
umask 0002

exec nginx -g 'daemon off;'
