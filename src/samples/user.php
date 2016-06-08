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
 * @author Sushant Gawali <sushant@introp.net>
 * @license MIT
 * @copyright (C) 2016 onwards Microsoft Corporation (http://microsoft.com/)
 */
session_start();
require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__.'/config.php');

$db = \microsoft\aadphp\samples\sqlite::get_db(__DIR__ . '/storagedb.sqlite');
$storage = new \microsoft\aadphp\OIDC\StorageProviders\SQLite(__DIR__.'/storagedb.sqlite');
const attribute_prefix = 'extension_fe2174665583431c953114ff7268b7b3_Education_';


if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} else {
    header('Location: ./index.php');
}

$user = $db->get_user($userId);
$aduser = $db->get_ad_user($userId);

if (!$aduser) {
    $link = '<a href="login.php">Link</a>';
} else {
    $link = '<a href="unlink.php">Unlink</a>';
}
?>
<html>
    <style type="text/css">
        body {
            padding-top: 100px;
            color: rgb(88, 88, 88) !important;
            background-color: rgb(240, 240, 240) !important;
        }
        .navbar-fixed-top {
            border-bottom: 1px solid rgba(255,255,255,.3);
            background: #000 !important;
        }
    </style>
    <?php include(__DIR__ .'./header.php'); ?>

    <div class="container">
        <?php
        if (isset($_GET['no_account'])) {
            ?>
            <div class="alert alert-danger alert-dismissable" role="alert" style="margin-top: 30px;">
                <h4>Unable to link the local account and the Azure AD account because their email id's do not match.</h4>
            </div>
        <?php } ?>
        <br />
        <br />
        <h1>Welcome to the PHP AAD Sample App</h1>
        <br/>
        <h2>Hello, <?php echo $user['firstname'] . ' ' . $user['lastname'] ?>.</h2>
        <br/>
        <h4>Email address: <?php echo $user['email'] ?>.</h4>
        <br/>
        <h4>Connected accounts:</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Account</th>
                    <th>Link / Unlink</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Azure AD</td>
                    <td><?php echo $link; ?></td>
                </tr>
            </tbody>
        </table>

        <br>
        <?php
        // Display sds data of user
        if ($aduser) {
            $school_info = array();
            echo "<h3> SDS Data </h3>";

            if (isset($aduser['token'])) {
                $tokenparams = json_decode($aduser['token'], true);
                if (isset($tokenparams['access_token'])) {
                    $httpclient = new \microsoft\aadphp\HttpClient();
                    $client = new \microsoft\aadphp\AAD\Client($httpclient, $storage);

                    // Set credentials.
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

                    // $token, $expiry, $refreshtoken, $scope, $resource
                    $token = new \microsoft\aadphp\AAD\token(
                        $tokenparams['access_token'],
                        $tokenparams['expires_on'],
                        $tokenparams['refresh_token'],
                        $tokenparams['scope'],
                        $tokenparams['resource'],
                        $client,
                        $httpclient);
                    $sds = new \microsoft\aadphp\samples\sdsapi($token, $httpclient, $db, $userId);
                    $school_info = $sds->get_schools();
                } else {
                    // Setting default values, if user has only 'id_token' set in db.
                    $school_info['success'] = false;
                    $school_info['value'] = "The access token to fetch sds data may be expired or not present.
                        Please login through Microsoft account using method Auth Code or Credentials.";
                }
            }

            if ($school_info['success']) {
                echo '<h4> School Information </h4>';
                foreach ($school_info['value']['value'] as $item) {
                    ?>
                    <div class="row">
                        &nbsp;
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            School Name :
                        </div>
                        <div class="col-md-4">
                            <?php echo $item['displayName'] ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            School Id :
                        </div>
                        <div class="col-md-4">
                            <?php echo $item[attribute_prefix . 'SyncSource_SchoolId'] ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            Principal Name :
                        </div>
                        <div class="col-md-4">
                            <?php echo $item[attribute_prefix . 'SchoolPrincipalName'] ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            Principal Mail :
                        </div>
                        <div class="col-md-4">
                            <?php echo $item[attribute_prefix . 'SchoolPrincipalEmail'] ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            Highest Grade :
                        </div>
                        <div class="col-md-4">
                            <?php echo $item[attribute_prefix . 'HighestGrade'] ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            Lowest Grade :
                        </div>
                        <div class="col-md-4">
                            <?php echo $item[attribute_prefix . 'LowestGrade'] ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            Phone :
                        </div>
                        <div class="col-md-4">
                            <?php echo $item[attribute_prefix . 'Phone'] ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            Address :
                        </div>
                        <div class="col-md-4">
                            <?php echo $item[attribute_prefix . 'Address']
                                . ', ' . $item[attribute_prefix . 'City']
                                . ', ' . $item[attribute_prefix . 'State']
                            ?>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <p class="alert alert-danger alert-dismissable">
                    <?php
                    echo $school_info['value'] ?>
                </p>
                <?php
            }
        }
        ?>
    </div>
    <?php
        include(__DIR__ . './footer.php');
    ?>
</html>