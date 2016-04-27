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

$db = \microsoft\adalphp\samples\sqlite::get_db(__DIR__ . '/storagedb.sqlite');

$error = '';
$email = $firstname = $lastname = $password = '';
if (isset($_POST['email'])) {

    $email = $_POST['email'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $password = $_POST['password'];

    if ($email == '' || $firstname == '' || $lastname == '' || $password == '') {
        $error = 'Please enter all the fields.';
    } else {

        $exist = $db->is_user_exist($_POST['email']);

        if (!$exist) {
            $result = $db->insert_user($firstname, $lastname, $email, $password);
            if (isset($_SESSION['data'])) 
            {
                $data = json_decode($_SESSION['data'],TRUE);
                $db->insert_ad_user($data['addata'], $result, $data['emailid'], $data['tokentype']);
                unset($_SESSION['data']);
            }
            if ($result) {
                $_SESSION['user_id'] = $result;
                header('Location: ./user.php');
                die();
            } else {
                $error = 'Error in registering user. Please try again';
            }
        } else {
            $error = 'User already exists. Please use differant email id.';
        }
    }
}
?>

<html>
    <?php include(__DIR__ .'./header.php'); ?>

    <div class="container">
        <?php if ($error != '') { ?>
            <div class="alert alert-danger" role="alert" style="margin-top: 30px">
                <h4><?php echo $error ?></h4>
            </div>
            <?php
        }

        if (isset($_GET['new_acc'])) {
            ?>
            <div class="alert alert-info" role="alert" style="margin-top: 30px">
                <h4>This account does not exist. Please sign up to create the account.</h4>
            </div>
        <?php }
        ?>
        <form class="form-horizontal" action="signup.php" method="post" style="margin-top: 50px">
            <fieldset>
                <legend> <b> Sign Up </b></legend>
                <div class="form-group">
                    <label for="firstname" class="col-lg-3 control-label">First Name</label>
                    <div class="col-lg-6">
                        <input type="text" value="<?php echo isset($_GET['firstname']) ? $_GET['firstname'] : $firstname; ?>" class="form-control" id="firstname" name="firstname" placeholder="First Name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="lastname" class="col-lg-3 control-label">Last Name</label>
                    <div class="col-lg-6">
                        <input type="text" value="<?php echo isset($_GET['lastname']) ? $_GET['lastname'] : $lastname; ?>" class="form-control" id="lastname" name="lastname" placeholder="Last Name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="username" class="col-lg-3 control-label">E-Mail</label>
                    <div class="col-lg-6">
                        <input type="email" value="<?php echo isset($_GET['email']) ? $_GET['email'] : $email; ?>" class="form-control" id="email" name="email" placeholder="E-Mail">
                    </div>
                </div>
                <div class="form-group">
                    <label for="password" class="col-lg-3 control-label">Password</label>
                    <div class="col-lg-6">
                        <input type="password" value="<?php echo $password; ?>" class="form-control" id="password" name="password" placeholder="Password">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-3 col-lg-offset-3">
                        <button type="submit" class="btn btn-block btn-em">Sign Up</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</html>