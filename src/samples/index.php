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

// Create required tables for first run.
$db->create_tables();
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
            if (isset($_SESSION['data'])) {
                $data = json_decode($_SESSION['data'], TRUE);
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

    <!-- Intro Header -->
    <header class="intro">
        <div class="intro-body">
            <div class="container-fluid">
                <?php if ($error != '') { ?>
                    <div style="position: absolute; margin-left: auto; margin-right: auto; margin-top: 100px; width: 98%;">
                        <div class="alert alert-danger" role="alert" style="position: relative; ">
                            <h4><?php echo $error ?></h4>
                        </div>
                    </div>
                    <?php
                }
                if (isset($_GET['new_acc'])) {
                    ?>
                    <div style="position: absolute; margin-left: auto; margin-right: auto; margin-top: 100px;width: 98%;">
                        <div class="alert alert-info" role="alert" style="position: relative;  ">
                            <h4>This account does not exist. Please sign up to create the account.</h4>
                        </div>
                    </div>
                <?php }
                ?>
                
                <div class="row">
                    <div class="col-md-4 brand-title" style="margin-top: 200px;">

                        <h1 class="brand-heading"><i class="fa fa-check-square-o"></i> AADS</h1>
                        <h2>(PHP Sample App)</h2>
                        <p class="intro-text">This sample app demonstrates authentication and REST API usage with Microsoft Azure AD using PHP.</p>
                    </div>

                    <div class="col-md-4 brand-title">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <form role="form" action="index.php" method="post">
                                    <div class="form-group">
                                        <input type="email" value="<?php echo isset($_GET['email']) ? $_GET['email'] : $email; ?>" class="form-control" id="email" name="email" placeholder="EMail Address">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" value="<?php echo $password; ?>" class="form-control" id="password" name="password" placeholder="Password">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" value="<?php echo isset($_GET['firstname']) ? $_GET['firstname'] : $firstname; ?>" class="form-control" id="firstname" name="firstname" placeholder="First Name">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" value="<?php echo isset($_GET['lastname']) ? $_GET['lastname'] : $lastname; ?>" class="form-control" id="lastname" name="lastname" placeholder="Last Name">
                                    </div>
                                    <div class="super-tiny-text">By signing up, you agree to our <a href="javascript:void(0)" style="color: #fff;">Terms and Conditions</a></div>
                                    <input type="submit" value="Sign up" class="btn btn-block btn-em" >
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row scroll-arrow" style="width: 150px;">
                    <div class="col-md-12">
                        <a href="#about" class="btn btn-circle page-scroll" style="padding-top: 15px;">
                            <i class="fa fa-angle-double-down animated"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- About Section -->
    <section id="about" class="container content-section text-center">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
                <h2>About the PHP AAD Sample App</h2>
                <p>The PHP AAD Sample App can help you understand how to integrate your app with Azure Active Directory using PHP.</p>
                <p>In the sign-up form, type in the email address of a user with an Azure AD account and a password. This will create a local account for that user. After signing in, you can link this local account with their Azure AD account.  Once linked in this manner, you can sign in either using the local account or
                using the Azure AD account. For the Azure AD account, you can sign in using one of 3 standard OAuth2 flows: Authorization, Hybrid, and Resource Owner Credentials.</p>
            </div>
        </div>
    </section>

    <!-- Download Section -->
    <section id="download" class="content-section text-center">
        <div class="download-section">
            <div class="container">
                <div class="col-lg-8 col-lg-offset-2">
                    <h2>Download PHP AAD Sample App</h2>
                    <p>You can view the source code for PHP AAD Sample App on GitHub.</p>
                    <a href="https://github.com/jamesmcq/oidc-aad-php-library/tree/master/src/samples" class="btn btn-default btn-lg btn-transparent"><i class="fa fa-github fa-fw"></i> <span class="network-name">Azure-Samples on Github</span></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="container content-section text-center">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
                <h2>Contact Azure AD</h2>
                <p>We're here to help with all your questions about integrating with Azure AD.  We've already posted a lot of good stuff though, so check out our documenation first!</p>
                <ul class="list-inline banner-social-buttons">
                    <li>
                        <a href="https://twitter.com/AzureAD" class="btn btn-default btn-lg btn-transparent"><i class="fa fa-twitter fa-fw"></i> <span class="network-name">Twitter</span></a>
                    </li>
                    <li>
                        <a href="https://stackoverflow.com/tags/azure-active-directory" class="btn btn-default btn-lg btn-transparent"><i class="fa fa-stack-overflow"></i> <span class="network-name">Stack Overflow</span></a>
                    </li>
                    <li>
                        <a href="https://github.com/AzureAD" class="btn btn-default btn-lg btn-transparent"><i class="fa fa-github fa-fw"></i> <span class="network-name">Github</span></a>
                    </li>
                    <li>
                        <a href="https://aka.ms/aaddev" class="btn btn-default btn-lg btn-transparent"><span class="network-name">Azure.com</span></a>
                    </li>
                </ul>
            </div>
        </div>
    </section>
    <?php include(__DIR__ . './footer.php'); ?>
</html>