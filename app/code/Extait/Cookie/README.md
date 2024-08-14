# Cookie Mode Pro
The extension allows adjusting use of cookies to the General Data Protection Regulation
 statements. Cookie Mode Pro is user-friendly and easy in its customization. With the help
 of it, you can show a popup about cookies on your website just as a visitor has his first
 interaction. Besides, the extension allows customers to enable or disable different categories
 of cookies. So it gives customers much more freedom.

## Extension features
- Show popup for use of cookies.
- Write your message for popup. 
- Make type groups of cookies.
- Enable Cookie Settings page for customers.
- Improve Cookie Settings page with descriptions.
- Display or hide list of cookies under each category.
- Let customers memorize their settings.

## Third-party cookies
For third-party cookies to work correctly as well as to have the possibility to enable/disable them,
 it is needed to insert js-code (which set them) after the Extait_Cookie:js/cookie.js component.
 To implement it you can include Extait_Cookie:js/cookie.js component into your component.

```
define([
    'Extait_Cookie/js/cookie'
], function (cookie) {
    'use strict';
    
    //your code
});
```
## License

This extension is licensed under the Commercial License - see the [LICENSE.txt](LICENSE.txt) file for details
