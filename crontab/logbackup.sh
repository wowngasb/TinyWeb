#!/bin/bash
logs_path="/usr/local/tengine/html/logs/"

zip ${logs_path}log_backup_$(date -d "yesterday" +"%Y%m%d").zip ${logs_path}*/*.log

find $logs_path -name "*.log" | xargs rm -f
find $logs_path -name "*" -type d -empty | xargs rm -f
find $logs_path -name "*.zip" -mtime +90 | xargs rm -f