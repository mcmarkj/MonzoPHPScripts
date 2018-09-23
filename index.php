<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config.php';
$provider = new Edcs\OAuth2\Client\Provider\Mondo([
  'clientId'     => $clientid,
  'clientSecret' => $clientsecret,
  'redirectUri'  => $redicurl,
]);
if (!isset($_GET['code'])) {
    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: '.$authUrl);
    exit;
// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid state');
} else {
    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code'],
    ]);
    // Store the token in the session so we can refresh the page while we're testing

    $arr1 = array (
        'access_token'      => $token->getToken(),
        'expires'           => $token->getExpires(),
        'refresh_token'     => $token->getRefreshToken(),
        'resource_owner_id' => $token->getResourceOwnerId(),
    );
    file_put_contents("array.json",json_encode($arr1));

    // Optional: Now you have a token you can look up a users profile data
    echo 'Script Live - Now visit <a href="monzobalance.php">this page</a>.';
}

?>
