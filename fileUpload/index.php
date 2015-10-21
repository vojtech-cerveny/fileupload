<html>
    <head>
        <!--Import materialize.css-->
        <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection"/>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="css/css.css"" rel="stylesheet">
        <link href="js/toastr.min.css"  rel="stylesheet" >
        <meta charset="UTF-8">
        <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" src="js/materialize.min.js"></script>
        <script type="text/javascript" src="js/particleground.min.js"></script>
        <script type="text/javascript"> 
        var fileInput = null,
            urlInput = null;

        $(document).ready(function() {
          $('#upload').particleground({
            dotColor: '#1a7269',
            lineColor: '#1a7269'
          });
          document.getElementById("uploadBtn").disabled = true;
          fileInput = document.getElementById("inputFile");
          urlInput = document.getElementById("url");
        }); 

        function validateField(){
            var x = urlInput.className;
            if(fileInput.value || (urlInput.value && (x.indexOf('invalid') == -1 ))){
                document.getElementById("uploadBtn").disabled = false;
            }else{
                document.getElementById("uploadBtn").disabled = true;
            }
        }
        </script>
        <script src="js/toastr.min.js"> </script>
    
        <title>Upload a file</title>

        <!--Let browser know website is optimized for mobile-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    </head>


    <?php

    function HumanSize($Bytes)
    {
      $Type=array("", "ki", "Mi", "Gi", "Ti", "Pi", "Ei", "Zi", "Yi");
      $Index=0;
      while($Bytes>=1024)
      {
        $Bytes/=1024;
        $Index++;
      }
      return("". round($Bytes, 2)." ".$Type[$Index]."B");
    }

    $servername = "localhost";
    $username = "";
    $password = "";
    $dbname = "myDB";

// Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
    if ($conn->connect_error) {
        //die("Connection failed: " . $conn->connect_error);
    }

    $result = $conn->query("select * from db_file;");
    $countFiles = $result->num_rows;
    

    $result = $conn->query("select id from db_file order by id desc limit 1;"); 
    $totalFiles = $result->fetch_assoc();


    if($_GET["del"] != null)
    {
        $sql = "SELECT url FROM db_file WHERE id = " . $_GET["del"];
        
    if ($result = $conn->query($sql)) {

        while ($row = mysqli_fetch_row($result)) {
            unlink("uploads/" . $row[0]);
        }
        mysqli_free_result($result);
    }
        
        $sql = "DELETE FROM db_file WHERE id = " . $_GET["del"];
        $result = $conn->query($sql);
        $errors = "<body onload=\"toastr.success('Deleted!')\">";
        
    }

    $errors = array();
    $date = Date("j/m/Y H:i:s", Time());

    if ($_POST["url"] != null) {

        $url = htmlspecialchars($_POST["url"]);

        $sql = "INSERT INTO db_file (url, text, ext) VALUES ('" . $url . "', '" . $url . "', 'url')";

        if ($conn->query($sql) === TRUE) {
            $errors = "<body onload=\"toastr.success('Your URL is mine now!')\">";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    if (isset($_FILES['image'])) {

        $file_name = preg_replace('/\s+/', '', $_FILES['image']['name']);
        $file_size = $_FILES['image']['size'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_type = $_FILES['image']['type'];
        $file_ext = strtolower(end(explode('.', $_FILES['image']['name'])));
        if ($file_name == null AND $_POST["url"] == null) {
            $errors = "<body onload=\"toastr.info('Give me some file, man!')\">";
        }
        if (empty($errors) == true OR $errors != "<body onload=\"toastr.success('Your URL is mine now!')\">") {
            move_uploaded_file($file_tmp, "uploads/" . $file_name);
            $aType = [
                "zip" => "archive",
                "jpeg" => "image",
                "gif" => "image",
                "jpg" => "image",
                "png" => "image",
                "mp4" => "videocam",
                "swf" => "videocam",
                "avi" => "videocam",
                "mkv" => "videocam",
                "mov" => "videocam",
                "doc" => "insert_drive_file",
                "odt" => "insert_drive_file",
                "xls" => "insert_drive_file",
                "xml" => "insert_drive_file",
                "txt" => "insert_drive_file",
                "exe" => "settings_applications",
                "msi" => "settings_applications",
                "sh" => "settings_applications",
                "url" => "search"
            ];

            $sql = "INSERT INTO db_file (url, text, ext, size) VALUES ('" . $file_name . "', '" . $file_name . "', '" . $aType[$file_ext] . "', '" . $file_size . "')";

            if ($conn->query($sql) === TRUE) {
                echo "<body onload=\"toastr.success('Yeah man! Your file is mine now!')\">";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo $errors;
        }
    }

    $result = $conn->query("select * from db_file;");
    $countFiles = $result->num_rows;

    ?>
<div id="upload" style="width: 100%; height: 500px;position: relative">
<div style="position: absolute; top: 0;width: 100%;">
    <div class="container" style="">    
        <div class="section">
        <center><h1> FileUpload 176 </h1></center>  
            <form action="#" method="POST" enctype="multipart/form-data">
                <div class="file-field input-field" >
                    <input class="file-path validate" id="inputFile" type="text" style="width: 50%;" onchange="validateField()"/>

                    <div class="btn">
                        <span>File</span>
                        <input type="file" name="image"/>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                        <input name="url" id="url" type="url" class="validate" oninput="validateField()">
                        <label for="url">URL</label>
                    </div>
                </div>
                <br/>
                <button id="uploadBtn" type="submit" name="action" class="waves-effect waves-light btn" style="width:100%;">Upload</button>
            </form>
        </div>
        <br/>
    </div>
</div>
</div>
<div class="container">
        <div class="divider"></div>
        <div class="section">
            <h5>Files and URLs</h5><span>Free space: <span style="color: #8ec549"><?php echo HumanSize(disk_free_space("/"));?> </span></span> <span> || Actual number of files: <span style="color: #8ec549"><?php echo $countFiles;?> </span></span> <span> 
            <table class="striped">
                <thead>
                    <tr>
                        <th data-field="type">Type</th>
                        <th data-field="name">Link</th>
                        <th data-field="name">Size</th>
                        <th data-field="date">Date</th>
                    </tr>
                </thead>

                <tbody>

                <?php
                    $sql = "SELECT id, url, text, upload_date, ext, size FROM db_file ORDER BY id DESC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            if ($row["ext"] == "url") {
                                $short =  explode( '/' , $row["url"]);  
                                echo "<tr><td> <i class=\"material-icons grey-text\">change_history</i>  </td><td><a class=\"truncate\" href=\"" . $row["url"] . "\" target=\"_blank\">" . $short[2] . "/.../" . substr($row["url"], -35) .  "</a></td><td></td><td>" . $row["upload_date"] . "</td><td><a class=\"waves-effect waves-light red lighten-3 btn\" href=\"?del=" . $row["id"]."\">Delete</a></td></tr>";
                            } else {
                                echo "<tr><td> <i class=\"material-icons grey-text\">" . $row["ext"] . "</i>  </td><td><a href=\"uploads/" . $row["url"] . "\" target=\"_blank\">" . $row["text"] . "</a></td><td>" . HumanSize($row["size"]) . "</td><td>" . $row["upload_date"] . "</td><td><a class=\"waves-effect waves-light red lighten-3 btn\" href=\"?del=" . $row["id"]."\">Delete</a></td></tr>";
                            }
                        }
                    }
                ?>
                </tbody>
            </table>
        </div>
    </div>
        <footer class="page-footer teal lighten-2">
          <div class="container">
            <div class="row">
              <div class="col l6 s12">
                <h5 class="white-text">About</h5>
                <p class="grey-text text-lighten-4">Developed for <a href="http://profiq.com"  class="grey-text text-lighten-3" target="_blank">Profiq</a> by Vojtěch Červený.</p>
              </div>
              <div class="col l4 offset-l2 s12">
                <h5 class="white-text">Links</h5>
                <ul>
                  <li><a class="grey-text text-lighten-3" href="http://profiq.com" target="_blank">Profiq</a></li>
                  <li><a class="grey-text text-lighten-3" href="https://github.com/cervenyprofiq/fileupload" target="_blank">GIT repository</a></li>
                  <li><a class="grey-text text-lighten-3" href="http://cervik.net" target="_blank">cervik.NET</a></li>
                </ul>
              </div>
            </div>
          </div>
          <div class="footer-copyright">
            <div class="container">
            © 2015 Copyright
            </div>
          </div>
        </footer>
</body>
</html>
