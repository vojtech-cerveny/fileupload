<html>
    <head>
        <!--Import materialize.css-->
        <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection"/>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
        <link href="css/css.css" rel="stylesheet" />
        <link href="js/toastr.min.css"  rel="stylesheet" />
        <meta charset="UTF-8">
        <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" src="js/materialize.min.js"></script>
        <script src="js/tus.js"></script>
        <script src="js/toastr.min.js"> </script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

        <title>Upload a file</title>

        <!--Let browser know website is optimized for mobile-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <script>
            google.charts.load("current", {packages:["corechart"]});
            google.charts.setOnLoadCallback(drawChart);
            function drawChart() {
              var data = google.visualization.arrayToDataTable([
                ['Task', 'Hours per Day'],
                ['Work',     11],
                ['Eat',      2],
                ['Commute',  2],
                ['Watch TV', 2],
                ['Sleep',    7]
              ]);

              var options = {
                title: 'My Daily Activities',
                pieHole: 0.4
                ,width:900
                ,height:500
              };

              var dataPac = google.visualization.arrayToDataTable([
                  ['Pac Man', 'Percentage'],
                  ['Epic', 75],
                  ['', 25]
                ]);

                var optionsPac = {
                  title: 'This is awesome',
                  legend: 'none',
                  pieSliceText: 'none',
                  pieStartAngle: 135,
                  tooltip: { trigger: 'none' },
                  slices: {
                    0: { color: '#26A69A' },
                    1: { color: 'transparent' }
                    }
                    ,width:900
                    ,height:500

                };
                var chartPac = new google.visualization.PieChart(document.getElementById('pacman'));
                chartPac.draw(dataPac, optionsPac);

              var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
              chart.draw(data, options);
            }




            var fileInput = null,
            urlInput = null;
            $(document).ready(function(){
                //document.getElementById("uploadBtn").disabled = true;
                fileInput = document.getElementById("inputFile");
                urlInput = document.getElementById("url");
                submit = document.getElementById('uploadBtn');

                submit.addEventListener("click", function(){
                        $('#modal1').openModal();
                    });
            })



            function validateField(){
                console.log('volam se');
                var x = urlInput.className;
                if(fileInput.value || (urlInput.value && (x.indexOf('invalid') == -1 ))){
                    document.getElementById("uploadBtn").disabled = false;
                }
                else{
                    document.getElementById("uploadBtn").disabled = true;
                }
            }
        </script>
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
    $username = "root";
    $password = "profiq";
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
                "rar" => "archive",
                "jpeg" => "image",
                "gif" => "image",
                "jpg" => "image",
                "png" => "image",
                "mp4" => "videocam",
                "swf" => "videocam",
                "avi" => "videocam",
                "mkv" => "videocam",
                "mov" => "videocam",
                "webm" => "videocam",
                "js" => "insert_drive_file",
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

<nav>
<div class="nav-wrapper">
  <a href="#" class="brand-logo center">FileUpload 176</a>
  <ul id="nav-mobile" class="right hide-on-med-and-down">
    <li><a href="../">176</a></li>
    <li><a href="../static">Static</a></li>
  </ul>
</div>
</nav>
<div class="container">
    <div class="row" style="padding-top: 20px">
        <div class="col s12 m12">
          <div class="card white">
            <div class="card-content blac-text">
                <div id="upload">
                    <div class="container">
                        <div class="section">
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
                                <button id="uploadBtn" class="waves-effect waves-light btn" style="width:100%;" disabled>Upload</button>
                            </form>
                        </div>
                        <br/>
                    </div>
                </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row" style="padding-top: 20px">
        <div class="col s12 m12">
            <ul class="collapsible" data-collapsible="accordion">
                <li>
                  <div class="collapsible-header"><i class="material-icons">donut_large</i>Graphs / in progress</div>
                  <div class="collapsible-body">
                      <div id="donutchart" style="width: 900px;"></div>
                      <div id="pacman" style="width: 900px; height: 500px;"></div>
                  </div>
                </li>
            </ul>
        </div>
      </div>
  </div>

<div class="container">
    <div class="divider"></div>
    <div class="row" style="padding-top: 20px">
        <div class="col s12 m12">
            <div class="section">
                <h5>Files and URLs</h5><span>Free space: <span style="color: #26A69A"><?php echo HumanSize(disk_free_space("/"));?> </span></span> <span> || Actual number of files: <span style="color: #26A69A"><?php echo $countFiles;?> </span></span>
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
                        $row["ext"] = "attach_file";
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $date = date_create($row["upload_date"]);
                                if ($row["ext"] == "url") {
                                    $short =  explode( '/' , $row["url"]);
                                    echo "<tr><td> <i class=\"material-icons grey-text\">change_history</i>  </td><td><a class=\"truncate\" href=\"" . $row["url"] . "\" target=\"_blank\">" . $short[2] . "/.../" . substr($row["url"], -35) .  "</a></td><td></td><td>" . date_format($date, 'Y-m-d H:i') . "</td><td><a class=\"waves-effect waves-light red circle lighten-3 chip\" href=\"?del=" . $row["id"]."\"><i class=\"material-icons circleBase\">delete</i></a></td></tr>";
                                } else {
                                    if ($row["ext"] == "") {
                                        $row["ext"] = "attach_file";
                                    }
                                    echo "<tr><td> <i class=\"material-icons grey-text\">" . $row["ext"] . "</i>  </td><td><a href=\"uploads/" . $row["url"] . "\" target=\"_blank\">" . $row["text"] . "</a></td><td>" . HumanSize($row["size"]) . "</td><td>" . date_format($date, 'Y-m-d H:i'). "</td><td><a class=\"waves-effect waves-light red circle lighten-3 chip\" href=\"?del=" . $row["id"]."\"><i class=\"material-icons circleBase\">delete</i></a></td></tr>";
                                }
                            }
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
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
            © 2016 Copyright
            </div>
          </div>
        </footer>
 <!-- Modal Structure -->
  <div id="modal1" class="modal">
    <div class="modal-content center black cyan-text text-accent-3">
            <img src="resources/cat.gif" />
          <br />
          <h3>Uploading...</h3>
    </div>
  </div>


</body>
</html>
