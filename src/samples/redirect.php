<?php

/**
 * Copyright (c) 2016 Micorosft Corporation
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author Aashay Zajriya <aashay@introp.net>
 * @license MIT
 * @copyright (C) 2016 onwards Microsoft Corporation (http://microsoft.com/)
 */
session_start();
require(__DIR__ . '/../../vendor/autoload.php');

// Construct.
$httpclient = new \microsoft\adalphp\HttpClient;
$storage = new \microsoft\adalphp\OIDC\StorageProviders\SQLite(__DIR__ . '/storagedb.sqlite');
$db = \microsoft\adalphp\samples\sqlite::get_db(__DIR__ . '/storagedb.sqlite');
$client = new \microsoft\adalphp\AAD\Client($httpclient, $storage);

// Set credentials.
require(__DIR__.'/config.php');
if (!empty($_REQUEST['id_token'])) {
    $client->set_authflow('hybrid');
} 

if (!defined('ADALPHP_CLIENTID') || empty(ADALPHP_CLIENTID)) {
    throw new \Exception('No client ID set - please set in config.php');
}
$client->set_clientid(ADALPHP_CLIENTID);

if (!defined('ADALPHP_CLIENTSECRET') || empty(ADALPHP_CLIENTSECRET)) {
    throw new \Exception('No client secret set - please set in config.php');
}
$client->set_clientsecret(ADALPHP_CLIENTSECRET);

if (!defined('ADALPHP_CLIENTREDIRECTURI') || empty(ADALPHP_CLIENTREDIRECTURI)) {
    throw new \Exception('No redirect URI set - please set in config.php');
}
$client->set_redirecturi(ADALPHP_CLIENTREDIRECTURI);
$token_type = '';
// Process response.
if (!empty($_REQUEST['id_token'])) {
    list($idtoken, $stateparams) = $client->handle_id_token($_REQUEST);
    $insertAdData = $_REQUEST['id_token'];
    $token_type = 'id_token';
} else {
    list($idtoken, $tokenparams, $stateparams) = $client->handle_auth_response($_REQUEST);
    $insertAdData = $tokenparams;
    $token_type = 'access_token';
}

if (isset($_SESSION['user_id'])) {
    $user = $db->get_user($_SESSION['user_id']);
    
    if ($user['email'] != strtolower($idtoken->claim('upn'))) {
        header('Location: ./user.php?no_account=1');
        die();
    }
}

$user = $db->is_user_exist($idtoken->claim('upn'));

if ($user) {
    $adUser = $db->get_ad_user($user['id']);
    if (isset($_SESSION['user_id']) && !$adUser) {
        
        $db->insert_ad_user($insertAdData, $user['id'], $idtoken->claim('upn'), $token_type);
        
    } else if (!$adUser) {
        $data = array('userid' => $user['id'],
                      'emailid' => $idtoken->claim('upn'),
                      'addata' => $insertAdData,
                      'tokentype' => $token_type);
        $_SESSION['data'] = json_encode($data);
        header('Location: ./link.php');
        die();
     } 
} else {
    $data = array('userid' => $user['id'],
                  'emailid' => $idtoken->claim('upn'),
                  'addata' => $insertAdData,
                  'tokentype' => $token_type);
    $_SESSION['data'] = json_encode($data);
    header('Location: ./index.php?firstname=' . $idtoken->claim('given_name') . '&lastname=' . $idtoken->claim('family_name') . '&email=' .$idtoken->claim('upn') . '&new_acc=1');
    die();
}

$_SESSION['user_id'] = $user['id'];
header('Location: ./user.php');
?>