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
 * @author Sushant Gawali sushant@introp.net>
 * @license MIT
 * @copyright (C) 2016 onwards Microsoft Corporation (http://microsoft.com/)
 */
session_start();
require(__DIR__ . '/../../vendor/autoload.php');

$db = \microsoft\adalphp\samples\sqlite::get_db(__DIR__ . '/storagedb.sqlite');

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
        body{
            padding-top: 100px;
            color: rgb(88, 88, 88) !important;
            background-color: rgb(240, 240, 240) !important;
        }
        .navbar-fixed-top{
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
    </div>
    <?php include(__DIR__ . './footer.php'); ?>
</html>