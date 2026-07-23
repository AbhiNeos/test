@echo off
echo Syncing Fashion Shop theme to WordPress container...
docker cp "%~dp0fashion-shop" fashion_shop_wp:/var/www/html/wp-content/themes/fashion-shop
echo Done! Theme updated.
pause
