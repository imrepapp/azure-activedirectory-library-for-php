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
        <!-- <form class="form-horizontal" action="index.php" method="post" style="margin-top: 50px">
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
    </div>-->




<!-- Intro Header -->
<header class="intro">
    <div class="intro-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4 brand-title" style="margin-top: 200px;">
                    
                    <h1 class="brand-heading"><i class="fa fa-check-square-o"></i> TO-DO LIST</h1>
                    <h2>(reimagined)</h2>
                    <p class="intro-text">The best* way to keep track of your team's work.</p>
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
<!--                                    <div class="form-group">
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control input-sm" placeholder="Confirm Password">
                                    </div>-->
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
                    <p class="tiny-text">*Not actually the best.</p>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- About Section -->
<section id="about" class="container content-section text-center">
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2">
            <h2>About TDLR;</h2>
            <p>Soooooo, yeah.  To-Do List Reimagined isn't a <em>real</em> app, per se.  But it can help you understand how to integrate your app with Azure Active Directory in a realistic way.  In the sign-up form, try typing in an email address of a user with an Azure AD account.  Then sign in as that user, and get started adding some tasks to that user's to-do list.</p>
            <p>Once you've explored sign-up and sign-in, try sharing a task with other users in your company, and watch TDLR take the company by storm.</p>
            <p>Finally, click the Admin link to login as an admin of your company and view a list of users that have been assigned to the app.  As you add/remove users, TDLR's user store is automatically updated.</p>
        </div>
    </div>
</section>

<!-- Download Section -->
<section id="download" class="content-section text-center">
    <div class="download-section">
        <div class="container">
            <div class="col-lg-8 col-lg-offset-2">
                <h2>Download TDLR;</h2>
                <p>You can view the source code for TDLR; on GitHub.</p>
                <a href="https://github.com/azure-samples/name-of-repo" class="btn btn-default btn-lg btn-transparent"><i class="fa fa-github fa-fw"></i> <span class="network-name">Azure-Samples on Github</span></a>
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