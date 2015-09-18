# fileupload
![alt tag](https://raw.githubusercontent.com/cervenyprofiq/fileupload/master/screenshot.png)
A simple fileupload server for private networks. Can share files and URLs. Recommended install only on TRUSTED network! 

#Highlight
* Easy for sharing files (from touch devices to your PC) 
* No limitations for upload
* Easy deleting unnecessary files

#The requirements
1. Web server
2. MySQL

#Install
1. Import myDB.sql to your MySQL server (maybe want create database myDB)
2. Set up index.php (start on line 41)
```
    $servername = "localhost";
    $username = "";
    $password = "";
    $dbname = "myDB";

```
3. Set up your apache2/nginx - upload_max_filesize, post_max_size, memory_limit

#Used js frameworks
* MaterializeCSS - http://materializecss.com/
* Patricleground - https://github.com/jnicol/particleground
* Toastr - https://github.com/CodeSeven/toastr

# Known bugs
* If you upload file with name, which is on server, file overwrite this file. 
* Toastr looks bad on small screen
* Table is not responsive
* MaterialiseCSS don't support IE8, IE9
