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

if (isset($_SESSION['user_id'])) {
    unset($_SESSION['user_id']);
}

require(__DIR__ . '/../../vendor/autoload.php');

$db = \microsoft\adalphp\samples\sqlite::get_db(__DIR__ . '/storagedb.sqlite');


$error = '';
if (isset($_GET['local'])) {
    $user = $db->verify_user($_POST['localemail'], $_POST['localpassword']);
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: ./user.php');
        die();
    } else {
        $error = 'Invalid username or password';
    }
}

if (isset($_SESSION['error'])) {
    $error = 'Error in login with username and password.';
    unset($_SESSION['error']);
}

?>

<html>
    <style type="text/css">
        body{
            color: #fff;
            background-color: rgb(240, 240, 240) !important;
        }
        .navbar-fixed-top{
            border-bottom: 1px solid rgba(255,255,255,.3);
            background: #000 !important;
        }
    </style>
    <?php include(__DIR__ . './header.php'); ?>

    <div class="container">
        <?php if ($error != '') { ?>
            <div style="position: absolute; margin-left: auto; margin-right: auto; margin-top: 100px; width: 85%;">
                <div class="alert alert-danger" role="alert" style="position: relative; text-align: center;">
                    <h4><?php echo $error ?></h4>
                </div>
            </div>
            <?php
        }?>
    </div>
    <center>
        <div class="panel panel-default">
            <div class="panel-body">
                <p class="brand">adal</p>
                <div id="tabs">
                    <ul>
                        <li><a href="#tabs-1" class="btn">Auth Code</a></li>
                        <li><a href="#tabs-2" class="btn">Hybrid</a></li>
                        <li><a href="#tabs-3" class="btn">Credentials</a></li>
                        <li><a href="#tabs-4" class="btn">Local </a></li>
                    </ul>
                    <div id="tabs-1">
                        <div class="form-group">
                            <a class="btn btn-block btn-msft" href="login.php" role="button"><img src="./img/microsoft-icon.png" class="msft-icon">Sign in with your Work or School account</a>
                        </div>
                    </div>
                    <div id="tabs-2">
                        <div class="form-group">
                            <a class="btn btn-block btn-msft" href="login.php?type=Hybrid" role="button"><img src="./img/microsoft-icon.png" class="msft-icon">Sign in with your Work or School account</a>
                        </div>
                    </div>
                    <div id="tabs-3">
                        <form action="pwgrant.php" method="post">
                            <fieldset>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="username" name="username" placeholder="Office 365 Email">
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Office 365 Password">
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-block btn-em">Login</button>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                    <div id="tabs-4">
                        <form action="signin.php?local=1" method="post">
                            <fieldset>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="localemail" name="localemail" placeholder="Email">
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" id="localpassword" name="localpassword" placeholder="Password">
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-block btn-em">Login</button>
                                </div>
                            </fieldset>
                        </form>
                        <div class="row">
                            <center> <a href="index.php" class="help">Sign Up</a> </center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </center>
</body>
<?php include(__DIR__ . './footer.php'); ?>
</html>

<script>
    $(function () {
        $("#tabs").tabs();
    });
</script>