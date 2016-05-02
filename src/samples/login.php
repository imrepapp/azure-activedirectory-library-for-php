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
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license MIT
 * @copyright (C) 2016 onwards Microsoft Corporation (http://microsoft.com/)
 */

require(__DIR__.'/../../vendor/autoload.php');

// Construct.
$httpclient = new \microsoft\adalphp\HttpClient;
$storage = new \microsoft\adalphp\OIDC\StorageProviders\SQLite(__DIR__.'/storagedb.sqlite');
$client = new \microsoft\adalphp\AAD\Client($httpclient, $storage);

if (!empty($_GET['type']) && $_GET['type'] === 'Hybrid') {
    $client->set_authflow('hybrid');
}

// Set credentials.
require(__DIR__.'/config.php');
if (!defined('AADSPHP_CLIENTID') || empty(AADSPHP_CLIENTID)) {
	throw new \Exception('No client ID set - please set in config.php');
}
$client->set_clientid(AADSPHP_CLIENTID);

if (!defined('AADSPHP_CLIENTSECRET') || empty(AADSPHP_CLIENTSECRET)) {
	throw new \Exception('No client secret set - please set in config.php');
}
$client->set_clientsecret(AADSPHP_CLIENTSECRET);

if (!defined('AADSPHP_CLIENTREDIRECTURI') || empty(AADSPHP_CLIENTREDIRECTURI)) {
	throw new \Exception('No redirect URI set - please set in config.php');
}
$client->set_redirecturi(AADSPHP_CLIENTREDIRECTURI);

// Make request.
if (!empty($_GET['prompt']) && $_GET['prompt'] === '1') {
	$client->authrequest(true);
} else {
	$client->authrequest();
}
