#!/bin/sh
rm demo.sqlite
sqlite3 demo.sqlite < schema.sqlite.sql
chmod 777 demo.sqlite
