============================================================
BookLibrary Free, version 3.0, for Joomla 1.6.x and PHP5
============================================================


CONTENTS
========

01. Introduction
02. System Requirements
03. License
04. Installation
05. Upgrade Instructions from free and Pro version
06. Upgrade Instructions from versions before
07. Getting started
08. Support
09. Addons
10. Languages
11. Other components/plugins
12. Customisation
13. Keeping up-to-date



01. INTRODUCTION
----------------

The BookLibrary Pro component for Joomla allows you to manage and maintain 
a book library or book collection with ease on a Joomla-based website. 

Book details (title, authors, covers, release information) can be pulled in 
easily from Amazon using the ISBN (International Standard Book Number). 
Because BookLibrary incorporates Lend-Return management and user contributed 
Book Review options, it is excellent for book library management. 
It can be used to set up and manage community libraries, libraries for 
organisations, school and university libraries, private book collection 
management, etc. 

Since BookLibrary also supports book sales directly through Amazon (with sales 
commission) as well as ebooks and ebook downloads, websites can also use the 
component to promote and sell their own or other people's publications. 

The Pro version of BookLibrary has a lot of added functionality compared to 
the Free version. For full details on the different options visit 
http://www.ordasoft.com.

Version    : 3.0 Free 
Maintainers: Andrey Kvasnevskiy
Homepage   : http://www.ordasoft.com



02. SYSTEM REQUIREMENTS
-----------------------

BookLibrary Pro is a Joomla component. It needs a functioning Joomla 1.6.x 
installation. BookLibrary Pro needs PHP 5 with GD, CURL, XSL, XML and YAZ 
extensions to function properly.

PLEASE CHECK BEFORE INSTALLING BOOKLIBRARY:
In order for BookLibrary Amazon Web Services to work, you need to compile PHP5 
with support for the XML extension!
In order for csv export to work, you need to compile PHP5 with support for the 
XSL extension!
In order for Book Cover downloads to work, you need to compile PHP5 with support 
for the CURL extension!
In order for the z39.50 protocol to work, you need to compile PHP with the YAZ 
extension!

CHECK YOUR PHP INSTALLATION FOR PROPER EXTENSIONS:
First please make sure PHP5 has the above extensions enabled!
- If you run your own web server, please recompile PHP with support for XSL, 
  XML, CURL, GD, YAZ.
- If your website is with a hosting provider, check with them for the inclusion 
  of these PHP extensions.
BookLibrary needs these PHP extensions to install and function correctly!

INSTALLER CHECK FOR PHP EXTENSIONS:
The BookLibrary installer will check for the availability of these PHP extensions 
and issue a warning if they are missing. If you get such a warning, just uninstall 
BookLibrary, fix the PHP extensions first, and then reinstall BookLibrary.

PLEASE NOTE:
Even though we can do checks for the proper PHP extensions inside the BookLibrary 
installer package, we have no way to roll back the installation once started, not 
even when a check fails. So installation will continue, even if a PHP extension is 
not present. This is unfortunately a limitation of the Joomla installer!



03. LICENSE
-----------

BookLibrary Pro is released as a commercial component.
Check the included LICENSE.txt file for license details.
There is also a Free version available (with less functionality), released 
under the GNU/GPL.



04. INSTALLATION
----------------

BookLibrary Pro is installed easily with the standard Joomla component installer. 
For additional information on how to set up and configure BookLibrary Pro to 
suit your needs, please consult the [BookLibrary Manuals] section on the website.


WARNING / IMPORTANT:

Local cover images:
In the book edit interface there is now a button to automatically set the path
for the book cover to the locally saved cover image. The cover images are set
by ISBN, so when you fill in book details manually, please remember this!

This current Pro version of BookLibrary also supports full data exports, 
so future upgrades will be relatively painless.



05. GETTING STARTED
-------------------

The [BookLibrary Free 1.5.x Manuals] section on the website has many articles 
with instructions on BookLibrary setup and use.



06. SUPPORT
-----------

The OrdaSoft site has a [Support Forums] section for support to the 
BookLibrary component, modules and plugins. There is a special forum section 
dedicated to registered BookLibrary Pro users.



07. UPGRADE INSTRUCTIONS FROM PREVIOUS 1.6.x VERSIONS TO Pro WITHOUT LOST DATA 
----------------------------------------------------------

Full save so files and folders:

{yours site}/administrator/components/com_booklibrary
{yours site}/components/com_booklibrary

Please do full books export.

After that please remove
{yours site}/administrator/components/com_booklibrary
{yours site}/components/com_booklibrary

Please do install BookLibrary 2.1 version.

Please recover folders:
{yours site}/administrator/com_booklibrary/ebooks
{yours site}/components/com_booklibrary/covers

Also you will need upgrade all plugins and modules
And all will work.

At first please check Upgrade process at test site


07. ADDONS (MODULES, PLUGINS)
-----------------------------

Modules and plugins (mambots) are constantly being developed for use with 
BookLibrary. You can download them from the download sections on the website.



08. LANGUAGES
-------------

BookLibrary Free comes with English language file included.
The BookLibrary interface will automatically pick up the frontend or backend 
language set in your Joomla configuration. Frontend language switches with 
JoomFish will also result in the automatic language change in BookLibrary.

You can add non-included languages to BookLibrary by creating your own 
translations. 
Copy english.php from the directory /components/com_booklibrary/language/, 
rename it to your language and then create the translation. Next add the 
language selectors to the code and upload the translation to your booklibrary.
You can find full instructions on creating translations and adding languages 
on the website.
Remember that Joomla 1.5.x needs files saved as UTF-8, so do NOT use Notepad or
Wordpad (they are not UTF-8 capable)!



09. OTHER COMPONENTS/PLUGINS
----------------------------

Add-ons for interaction with other components are also available developed 
(SEF, Sitemap). Community Builder and full JoomFish integration will be 
developed later.



10. CUSTOMISATION
-----------------

If you need a new specific feature added to BookLibrary Free for your own 
installation, you can order a custom development. 
Just contact sales@ordasoft.com describing the details of your requirements. 
We will then investigate your request and send you a price quote for this 
development. When you pay for a customisation for BookLibrary, you will receive 
the next version of BookLibrary Pro with your feature included.



11. KEEPING UP-TO-DATE
----------------------

Please check http://www.ordasoft.com for news, details and contact 
information regarding BookLibrary. In time there will also be a BookLibrary 
Newsletter to which you can subscribe (news, developments, etc.).
