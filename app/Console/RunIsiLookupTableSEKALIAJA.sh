#!/bin/bash
# SCRIPT FOR FETCHING Rekon Data PSR Onle Sama SMART-PSR

echo "SCRIPT TO Rekon data Pekebun PSR Online V1 to V2"
echo "RUNNING FETCH SCRIPT"
#/var/www/html/psr2/app/Http/Middleware/MT940/DontRunFetchMT940Transactions.php
#php -F /var/www/html/psr2/app/Http/Middleware/MT940/DontRunFetchMT940Transactions.php
#php -r 'include("/var/www/html/psr2/app/Http/Middleware/MT940/DontRunFetchMT940Transactions.php"); asd();'
php -f DontRunIsiLookupTable.php
