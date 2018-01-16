<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/funciones.php';

session_start();

 // @see: https://bitbucket.org/account/user/<username>/api
 $oauth_params = array(
     'identifier'    =>  getenv('bitbucket_consumer_key'),
     'secret'        =>  getenv('bitbucket_consumer_secret'),
     'callback_uri'  => 'http://localhost/Bitbucket_bot/'
 );

 $server = new League\OAuth1\Client\Server\Bitbucket($oauth_params);

 if (array_key_exists('profile', $_GET)) {
     if (false === array_key_exists('bb_credentials', $_SESSION)) {
         header('Location: ' . $oauth_params['callback_uri']);
         exit;
     }

     $oauth_params = array_merge(unserialize($_SESSION['bb_credentials']), array(
         'oauth_consumer_key'        => $oauth_params['identifier'],
         'oauth_consumer_secret'     => $oauth_params['secret'],
         'oauth_callback'            => $oauth_params['callback_uri'],
     ));


     $bitbucket = new \Bitbucket\API\Api();
     $bitbucket->getClient()->addListener(
         new \Bitbucket\API\Http\Listener\OAuthListener($oauth_params)
     );

     /** @var \Bitbucket\API\User $user */
     $user = $bitbucket->api('User');
     $profile = json_decode($user->get()->getContent(), true);

    //ISSUES
    $issues = $bitbucket->api('Repositories\Issues');
    $issues_json = json_decode($issues->all('fecron', 'mapfre-soporte',array('limit' => 50, 'start' => 0))->getContent(), true);
    $HTMLIssuesGlobal =  getHTMLIssuesGlobal($issues_json);

    //VISTA
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
      <link rel="stylesheet" href="./public/css/app.css" />
      <title>Espia :D</title>
    </head>
    <body>
      <?php
      echo sprintf('<a href="?logout">Logout %s</a>', $profile['user']['username']);
      //BIENVENIDA
      echo '<img src="'.$profile['user']['avatar'].'" width=50 height=50 />';
      echo '<h3>Bienvenido a casa '.$profile['user']['display_name'].'</h3><ul>';
      echo $HTMLIssuesGlobal; ?>
    </body>
    </html>

    <?php
     exit;
 } elseif (array_key_exists('login', $_GET)) {
     // Retrieve temporary credentials
     $temporaryCredentials = $server->getTemporaryCredentials();

     // Store credentials in the session, we'll need them later
     $_SESSION['temporary_credentials'] = serialize($temporaryCredentials);
     session_write_close();

     // Second part of OAuth 1.0 authentication is to redirect the
     // resource owner to the login screen on the server.
     $server->authorize($temporaryCredentials);
     exit;
 } elseif (array_key_exists('oauth_token', $_GET) && array_key_exists('oauth_verifier', $_GET)) {
     // Retrieve the temporary credentials we saved before
     $temporaryCredentials = unserialize($_SESSION['temporary_credentials']);

     // We will now retrieve token credentials from the server
     $tokenCredentials = $server->getTokenCredentials(
         $temporaryCredentials, $_GET['oauth_token'], $_GET['oauth_verifier']
     );

     $oauth_params = array(
         'oauth_token'               => $tokenCredentials->getIdentifier(),
         'oauth_token_secret'        => $tokenCredentials->getSecret()
     );

     unset($_SESSION['temporary_credentials'], $_SESSION['token_credentials']);
     $_SESSION['bb_credentials'] = serialize($oauth_params);
     session_write_close();

     // redirect the user to the profile page, in order to fetch his/her information.
     header('Location: '.$oauth_params['callback_uri'].'?profile');
     exit;
 } elseif (array_key_exists('logout', $_GET)) {
     unset($_SESSION['bb_credentials']);
     session_write_close();
 }

 echo '<a href="?login">Login with BitBucket!</a>';
