# IMAP Email Library
IMAP Library -  To Manage Email Opration in PHP

PHP already has a nice [IMAP extension] (https://www.php.net/manual/en/book.imap.php) for working with email. 
The extension needs to be installed and enabled before moving forward. 
The core functionality is all there, but the specifics on how to use it aren’t necessarily all that clear.

Here’s a PHP class I put together to do some basic operations on an IMAP Inbox. It’s a bit tailored to this project but could be easily revised to fit other needs or extended to be more full-featured.

## [See It In a Live Application](http://aarvitech.com/Research/MailAutoGenrateSupportTicket/index.php)

**NOTE: This will work only if you meet the following requirements**

- PHP version should be PHP5 or latest
- PHP IMAP Extension should be enabled in your PHP installation
- IMAP should be enabled in your Gmail settings.

# How to enable IMAP in PHP
# How to Enable IMAP in XAMPP

IMAP is not enabled by default in Xampp distribution, so to enable it go to the file "\xampp\php\php.ini" and search for ";extension=php_imap.dll" and by removing the beginning semicolon at the line ,it's get enabled ,it should be: extension=php_imap.dll.
