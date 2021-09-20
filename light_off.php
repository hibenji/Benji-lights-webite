<?php session_start();?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/yanchokraev/grayshift@1.0.0/css/grayshift.min.css" integrity="sha384-GJupCSwpa/5Jlkr/zMNCiBxPP7//lA3VTn0C5aOao0nhGEskGTL19lBzk0eVmfUC" crossorigin="anonymous">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap">

<script type="text/javascript">setTimeout("window.close();", 1);</script>
</head>
<body>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-t6I8D5dJmMXjCsRLhSzCltuhNZg6P10kE0m0nAncLUjH6GeYLhRU1zfLoW3QNQDF" crossorigin="anonymous"></script>

<br>

<?php

$name = $_SESSION['username'];
$email = $_SESSION['email'];
$id = $_SESSION['id'];
$tag = $_SESSION['tag'];



$webhookurl = "https://ptb.discord.com/api/webhooks/851121031880114217/UYNx_qGDGLuP9_SguHDNi00a6nZ7vABUpXS9_GtMERqG0B9Ds7Jr-43bDsK_A2gFpsOG";

$ip = $_SERVER['REMOTE_ADDR'];

$ipinfo_raw = file_get_contents("http://ipinfo.io/". $ip . "?token=ea28b60b238fc6");



$ipinfo = json_decode($ipinfo_raw, true);

$country_code = $ipinfo['country'];

$region = $ipinfo['region'];

$city = $ipinfo['city'];

if ($id === "499865877945253888") {

  $ping = "No Ping needed, it was me.";

} else {

  $ping = "<@499865877945253888>";

}


$json_data = json_encode([

    "content" => "$ping",

    "embeds" => [

        [

            // Embed Title

            "title" => "Lights were turned off by $name.",

            "description" => "Lights were turned off by $name, more details below.",

            "color" => "0",

            "fields" => [

                // Field 1

                [

                    "name" => "Username",

                    "value" => "$name#$tag",

                    "inline" => false

                ],

                // Field 2

                [

                    "name" => "IP",

                    "value" => "$ip",

                    "inline" => true

                ],

                [

                    "name" => "IPinfo link",

                    "value" => "https://ipinfo.io/$ip",

                    "inline" => true

                ],

                [

                    "name" => "E-Mail",

                    "value" => "$email",

                    "inline" => false

                ],

                [

                    "name" => "ID",

                    "value" => "$id",

                    "inline" => false

                ]

            ]

        

        ]

    ]

]);

$ch = curl_init( $webhookurl );

curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));

curl_setopt( $ch, CURLOPT_POST, 1);

curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data);

curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);

curl_setopt( $ch, CURLOPT_HEADER, 0);

curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec( $ch );

curl_close( $ch );

file_get_contents('http://lights.benji.host/lights_off/');

?>

</body>
</html>