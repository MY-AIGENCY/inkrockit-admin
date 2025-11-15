<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>InkRockIt</title>
		<?php
		if (!empty($styles)) {
			foreach ((array)$styles as $style) {
        		echo '<link rel="stylesheet" href="/css/' . htmlspecialchars($style, ENT_QUOTES, 'UTF-8') . '.css">';
		    }
		}
		?>        <meta name="viewport" content="width=device-width">
        <script src="/js/jquery.js"></script>
        <!--[if lt IE 9]>
        <script>
        document.createElement('header');
        document.createElement('nav');
        document.createElement('article');
        document.createElement('aside');
        document.createElement('footer');
        document.createElement('hgroup');
        document.createElement('menu');
        </script>
        <![endif]-->
        <!--[if IE]>
        <link rel="stylesheet" type="text/css" href="/css/styleMsie.css" />
        <![endif]-->
    </head>
    <body>

        <header class="TopHeader">
            <a class="logo" href="/"><img src="/images/logo.png"></a>
            
            <a class="vew_sample_pack pointer">
            <div class="HeadItem">
                <div class="headtext">
                    <p class="headitemname2"><img src="/images/SempleImg.png">
                    </p>
                    <p class="headitemtext2">See the quality and custom
                        finishes that set us apart.</p>
                    <div class="headbutton vew_sample_pack">Send Me Samples</div>
                </div>
                <img class="item2Bg" src="/images/headitem2.png">
            </div>
            </a>
            <a href="/inspiration_station#posit">
            <div class="HeadItem">
                <div class="headtext">
                    <p class="headitemname"><img src="/images/DesignLabIcon.png">Inspiration Station</p>
                    <p class="headitemtext">Explore creative ideas, templates, and production-ready
                        files for your projects.</p>
                    <div class="headbutton vew_sample_pack">See Styles</div>
                </div>
                <img class="item2Bg" src="/images/headitem3.png">
            </div>
            </a>
            <a href="/designlab">
            <div class="HeadItem">
                <div class="headtext">
                    <p class="headitemname"><img src="/images/DesignLabIcon.png">Design Lab</p>
                    <p class="headitemtext">Work with our team to turn your
                        ideas into finished products.</p>
                    <div class="headbutton vew_sample_pack">Start a Project</div>
                </div>
                <img class="item2Bg" src="/images/headitem1.png">
            </div>
            </a>

            <div class="callUs">
                <img src="/images/HotLine.png" class="left">
                <div class="welcome_user_signd">Welcome back <strong>John</strong>. You are signed in.</div>
            </div>

            <div class="basketNav">
                <?php
                $user = Session::instance()->get('user');
                if (!empty($user)) {
                    ?>
                    <div class="loginMainPageTest">
                        <a id="login" class="firstLog" href="">Sign In</a>

                        <div class="logmenu">
                            <form action="/user/login" method="post">
                                <label for="LoginForm_username">
                                    <label>Email address:</label>
                                    <input type="text" class="logInp" name="login" id="LoginForm_username" autocomplete="off">
                                </label><br class="clear">
                                <label class="loginLab">
                                    <label>Password:</label>
                                    <input  class="logInp" type="password" name="password" id="LoginForm_password" autocomplete="off">
                                </label>
                                <div class="errorMessage" id="login_form_error"></div>
                                <input class="logButton" type="submit" name="yt0" id="yt0" value="Sign In"><br class="clear">
                            </form>

                            <div style="padding: 0 16px 10px 16px;">
                                <input type="radio" name="remember" value="1" class="left">
                                <p>Remember my email and password.</p>
                                <br class="clear">
                                <a href="/user/forgot_password" class="restore">Forgot password?</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
                <a href="/basket">
                    <div class="basket">
                        <img src="/images/BasketIcon.png" class="left">
                        <div class="basketIn">
                            <p id="basketAmount">0 items</p>
                            <form action="/basket" method="post">
                                <input type="submit" name="go_to_checkout" id="basketButton" value="Go to Check Out">
                            </form>
                        </div></a>
                </div>
                <?php if (!empty($user)) { ?>
                    <p class="assigned">Welcome back <?php echo htmlspecialchars($user, ENT_QUOTES, 'UTF-8'); ?>. You are signed in.</p>
                <?php } ?>
            </div>
            <br class="clear">
        </header>

        <div class="main">
            <a id="posit" name="posit"></a>

            <?php if (!empty($content)) { echo $content; } ?>

        </div>

        <footer class="footer">
            <div class="inner">
                <div class="left">
                    <a href="/about">About</a> |
                    <a href="/contact">Contact</a> |
                    <a href="/privacy">Privacy</a> |
                    <a href="/terms">Terms</a>
                </div>
                <div class="right">
                    <p>&copy; <?php echo date('Y'); ?> InkRockIt. All rights reserved.</p>
                </div>
                <br class="clear">
            </div>
        </footer>

        <?php
        if (!empty($scripts)) {
            foreach ((array)$scripts as $script) {
                echo '<script src="/js/' . htmlspecialchars($script, ENT_QUOTES, 'UTF-8') . '.js"></script>';
            }
        }
        ?>

        <script>
        // your inline JS here if needed
        </script>
    </body>
</html>