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
?>
<head>
    <link rel="stylesheet" type="text/css" href="./css/bootstrap.css"/>
    <link rel="stylesheet" type="text/css" href="./css/bootstrap-theme.css"/>
    <link rel="stylesheet" type="text/css" href="./css/site.css"/>
    <link rel="stylesheet" type="text/css" href="./css/index.css"/>
    <link rel="stylesheet" type="text/css" href="./css/grayscale.css"/>
    <link rel="stylesheet" type="text/css" href="./css/jquery-ui.min.css"/>
    <link rel="stylesheet" type="text/css" href="./css/google_font_montserrat.css">
    <link rel="stylesheet" type="text/css" href="./css/google_font_lora.css"/>
    <link rel="stylesheet" type="text/css" href="./css/home.css">

    <link rel="stylesheet" type="text/css" href="./css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="./css/font-awesome.min.css">
    
    <script type="text/javascript" src="./js/jquery-1.10.2.js"></script>
    <script type="text/javascript" src="./js/jquery.validate.js"></script>
    <script type="text/javascript" src="./js/bootstrap.js"></script>
    <script type="text/javascript" src="./js/grayscale.js"></script>
    <script type="text/javascript" src="./js/jquery-ui.js"></script>

    <style type="text/css">
        .nav-link{
            color: #fff;
        }
        .navbar a{
            text-shadow: none !important;
        }
        .color-pink{
            color: lightcoral;
        }
    </style>
</head>
<body data-spy="scroll" >
    <nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
        <div class="container">

            <div class="navbar-header">
                <a class="navbar-brand" href="./index.php">php adal sample app</a>
            </div>
            <div id="navbar">
                <ul class="nav navbar-nav pull-right">
                    <li><a href="/#about" class="nav-link page-scroll navbar-normal">About</a></li>
                    <li><a href="/#contact" class="nav-link page-scroll navbar-normal">Contact</a></li>
                    <?php if (isset($_SESSION['user_id'])) { ?>
                        <li><a href="./logout.php" class="nav-link page-scroll navbar-normal">Logout</a></li>
                    <?php } else { ?>
                            <li><a href="signin.php" class="nav-link color-pink page-scroll navbar-normal">Sign In</a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>
