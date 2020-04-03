+ -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- +
Desmond tuimg (CMS) image management system installation instructions
+ -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- +
1. The server environment: Apache, Nginx, IIS, etc
2. Recommend a PHP: PHP 5.5 - PHP7.3
3. Recommend a MySQL: MySQL 5.0-MySQL 5.7 and above

+ -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- +
Process operation https://www.bcors.com/
+ -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- +
1. Upload all the files in the program files directory to the server

2. Set the directory attribute (Windows server can skip this step)
These directories need to read/write permissions (0777).
. / the attach
/ data contain subdirectories

3. Perform the install script (domain name)/install. PHP - without setting direct access to the site
Your domain name in the browser, please visit http:// / install directory/install PHP

4. To be safe after the installation is complete, please delete the PHP file

5. Address of the default background
Your domain name http:// / install directory/admin. PHP
The default user name password is: admin

6. The pseudo static landing rules = > web static background Settings = > view the current Rewrite rules
Your domain name http:// / admin. PHP? C = Config&a = index&type = Rewrite
