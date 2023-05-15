Magento 2 Multiple Custom Forms by Mageside
===========================================

####Support
    v1.4.4 - Magento 2.1.* - 2.3.*

####Change list
    v1.4.4 - Fixed sending attachments in Magento 2.3
    v1.4.3 - Magento 2.3 compatibility checking (updated composer.json)
    v1.4.2 - Fixed duplicate fields bug
    v1.4.1 - Fixed submitting form bug
    v1.4.0 - Added fieldsets
    v1.3.0 - Added store switcher for form settings
    v1.2.5 - Fixed admin listings filters errors
    v1.2.4 - Fixed rendering file field
    v1.2.3 - Included Google\Recaptcha lib to package
    v1.2.0 - Added functionality to adding custom settings for emails
    v1.1.10 - Added custom css class to form wrapper
    v1.1.9 - Fix recapcha validation bug
    v1.1.8 - Fixed data-bind errors, added reCAPTCHA settings errors
    v1.1.7 - Fixed multiselect field
    v1.1.6 - Fixed escapeJs method for magento 2.1
    v1.1.2 - Added code escaping, some fixes
    v1.1.1 - Code refactoring
    v1.1.0 - Added backend data validation
    v1.0.3 - Fix DateTime for Magento 2.1
    v1.0.2 - Added DateTime type field
    v1.0.0 - Start project

####Installation
    1. Download the archive.
    2. Make sure to create the directory structure in your Magento - 'Magento_Root/app/code/Mageside/MultipleCustomForms'.
    3. Unzip the content of archive (use command 'unzip ArchiveName.zip') 
       to directory 'Magento_Root/app/code/Mageside/MultipleCustomForms'.
    4. Create reCAPTCHA keys for your website on https://www.google.com/recaptcha/admin
    5. Run the command 'php bin/magento module:enable Mageside_MultipleCustomForms' in Magento root.
       If you need to clear static content use 'php bin/magento module:enable --clear-static-content Mageside_MultipleCustomForms'.
    6. Run the command 'php bin/magento setup:upgrade' in Magento root.
    7. Run the command 'php bin/magento setup:di:compile' if you have a single website and store, 
       or 'php bin/magento setup:di:compile-multi-tenant' if you have multiple ones.
    8. Clear cache: 'php bin/magento cache:clean', 'php bin/magento cache:flush'
    9. If you use nginx, you have to configure access to folder 'Magento_Root/pub/media/customform/submission'. 
       You should add the following line to your nginx.conf file: 
            location /media/customform/submission {
                deny all;
            }
       Also, find a line:
            location /pub/ {
       and add the following lines under this location scope:
            location ~ ^/pub/media/customform/submission {
               deny all;
            }
