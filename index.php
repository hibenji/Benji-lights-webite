<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('max_execution_time', 300); //300 seconds = 5 minutes. In case if your CURL is slow and is loading too much (Can be IPv6 problem)

error_reporting(E_ALL);

define('OAUTH2_CLIENT_ID', '-------');
define('OAUTH2_CLIENT_SECRET', '------');

$authorizeURL = 'https://discord.com/api/oauth2/authorize';
$tokenURL = 'https://discord.com/api/oauth2/token';
$apiURLBase = 'https://discord.com/api/users/@me';
$revokeURL = 'https://discord.com/api/oauth2/token/revoke';

if(get('action') == 'logout') {
    logout($revokeURL, array(
        'token' => session('access_token'),
        'token_type_hint' => 'access_token',
        'client_id' => OAUTH2_CLIENT_ID,
        'client_secret' => OAUTH2_CLIENT_SECRET,
      ));
    unset($_SESSION['access_token']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    die();
}

// Start the login process by sending the user to Discord's authorization page
if(get('action') == 'login') {

  $params = array(
    'client_id' => OAUTH2_CLIENT_ID,
    'redirect_uri' => 'https://home.benji.host/',
    'response_type' => 'code',
    'scope' => 'identify email'
  );
  // Redirect the user to Discord's authorization page
  header('Location: https://discord.com/api/oauth2/authorize' . '?' . http_build_query($params));
  die();
}



// When Discord redirects the user back here, there will be a "code" and "state" parameter in the query string
if(get('code')) {

  // Exchange the auth code for a token
  $token = apiRequest($tokenURL, array(
    "grant_type" => "authorization_code",
    'client_id' => OAUTH2_CLIENT_ID,
    'client_secret' => OAUTH2_CLIENT_SECRET,
    'redirect_uri' => 'https://home.benji.host/',
    'code' => get('code')
  ));
  $logout_token = $token->access_token;
  $_SESSION['access_token'] = $token->access_token;


  header('Location: ' . $_SERVER['PHP_SELF']);
}

?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Home Control</title>
<meta content="Home Control" property="og:title">
<meta content="A Website to control Benji's lights" property="og:description">
<meta content="https://home.benji.host" property="og:url">
<meta content="/light.png" property="og:image">
<meta content="#43B581" data-react-helmet="true" name="theme-color">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/yanchokraev/grayshift@1.0.0/css/grayshift.min.css" integrity="sha384-GJupCSwpa/5Jlkr/zMNCiBxPP7//lA3VTn0C5aOao0nhGEskGTL19lBzk0eVmfUC" crossorigin="anonymous">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/light.png">
<link rel="icon" type="image/png" sizes="16x16" href="/light.png">
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-t6I8D5dJmMXjCsRLhSzCltuhNZg6P10kE0m0nAncLUjH6GeYLhRU1zfLoW3QNQDF" crossorigin="anonymous"></script>
<style>
    .center{
        display: grid;
        place-items: center;
    }
    </style>

<script async src="https://arc.io/widget.min.js#nU6A4gDo"></script>
</head>
<body>
    
    
<?php

if(session('access_token')) {
  $user = apiRequest($apiURLBase);
  $_SESSION['id'] = $user->id;
    $_SESSION['username'] = $user->username;
    $_SESSION['email'] = $user->email;
    $_SESSION['tag'] = $user->discriminator;

//     echo '<pre>';
//     print_r($user);
//   echo '</pre>';
?>


<div class="center">

<div class="row">

<div class="card mx-5 mt-1" style="max-width: 400px;">
  <img class="w-100 rounded" src="https://proxy.effi.xyz/index1.jpg" alt="...">
  <div class="p-3">
    <h3 class="mb-1" style="font-size: 1.125rem;">Light Strip</h3>
    <p class="mb-3">Turn off or on the Light Strip.</p>
    <a class="btn btn-lg btn-primary w-100" href="/light_off.php" target="_blank">Off</a><br><br>
    <a class="btn btn-lg btn-primary w-100" href="/light_on.php" target="_blank">On</a>
  </div>
</div>




</div>

</div>




<?php


} else {


echo'<div class="center">';
    echo '<br>';
  echo '<h3>Not logged in</h3>';
  echo '<br>';
  echo 'By Clicking \'Log In\' you accept our <a href="privacy.html">Privacy Policy</a>';
  echo '<br>';
  echo '<p><a class="btn btn-primary" role="button" href="?action=login">Log In</a></p>';
echo '</div>';

}


if(get('action') == 'logout') {
  // This must to logout you, but it didn't worked(

  $params = array(
    'access_token' => $logout_token
  );

  // Redirect the user to Discord's revoke page
  header('Location: https://discord.com/api/oauth2/token/revoke' . '?' . http_build_query($params));
  die();
}

function apiRequest($url, $post=FALSE, $headers=array()) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

  $response = curl_exec($ch);


  if($post)
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

  $headers[] = 'Accept: application/json';

  if(session('access_token'))
    $headers[] = 'Authorization: Bearer ' . session('access_token');

  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $response = curl_exec($ch);
  return json_decode($response);
}

function logout($url, $data=array()) {
    $ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
        CURLOPT_POSTFIELDS => http_build_query($data),
    ));
    $response = curl_exec($ch);
    return json_decode($response);
}

function get($key, $default=NULL) {
  return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
}

function session($key, $default=NULL) {
  return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
}

?>


</body>
</html>
