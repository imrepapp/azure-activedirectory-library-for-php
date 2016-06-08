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

$db = \microsoft\aadphp\samples\sqlite::get_db(__DIR__ . '/storagedb.sqlite');
if (isset($_GET['link_check']) && isset($_SESSION['data'])) {
    $data = json_decode($_SESSION['data'], TRUE);
    $db->insert_ad_user($data['addata'], $data['userid'], $data['emailid'], $data['tokentype']);
    $_SESSION['user_id'] = $data['userid'];
    unset($_SESSION['data']);
    header('Location: ./user.php');
    die();
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
    <?php include(__DIR__ . './header.php'); ?>

    <div class="container">
        <div class="starter-template">
            <div class="row">
                <div class="col-lg-12">
                    <h4 class="lead">We have found a local account with the same email id as the Azure AD account you are using to sign in.
                        Do you want to link them?
                    </h4>
                    <p>
                        <a class="btn btn-primary" href="link.php?link_check=1" role="button">Yes</a>
                        <a class="btn btn-primary" href="signin.php" role="button">No</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    </body>
</html>