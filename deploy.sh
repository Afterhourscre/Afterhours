rm -rf var/view_processesd/*
rm -rf pub/static/frontend/Pearl/weltpixel/*
rm -rf pub/static/frontend/Pearl/weltpixel_custom/*
php bin/magento s:d:c
php bin/magento setup:static-content:deploy -f -t Pearl/weltpixel_custom