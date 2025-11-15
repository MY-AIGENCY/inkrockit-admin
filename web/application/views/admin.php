<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <title>CMS <?php echo htmlspecialchars($_SERVER['SERVER_NAME'] ?? '', ENT_QUOTES, 'UTF-8'); ?></title>

    <!-- Core CSS -->
    <link rel="stylesheet" href="/css/admin/reset.css">
    <link rel="stylesheet" href="/css/admin/main.css">
    <link rel="stylesheet" href="/css/admin/typography.css">
    <link rel="stylesheet" href="/css/admin/jquery-ui-1.9.2.css">
    <link rel="stylesheet" href="/css/admin/bootstrap.css">

    <?php
    if (!empty($styles)) {
        foreach ((array)$styles as $styleHref) {
            echo '<link href="' . htmlspecialchars($styleHref, ENT_QUOTES, 'UTF-8') . '" rel="stylesheet">';
        }
    }
    ?>

    <script type="text/javascript" src="/js/admin/jquery-1.8.3.js"></script>
</head>
<body>
    <!-- http://themeforest.net/item/dream-works-responsive-admin-template/full_screen_preview/1988987 -->

    <?php if (Cookie::get('admin_user')) { ?>
        <br>
        <div id="dreamworks_container">
            <!-- Primary Navigation -->
            <nav id="primary_nav">
                <ul>
                    <?php
                    if (!empty($menu) && is_array($menu)) {
                        foreach ($menu as $key => $val) {
                            $itemClass = htmlspecialchars($val['class'] ?? '', ENT_QUOTES, 'UTF-8');
                            $isActive  = (isset($controller) && $controller === $key) ? 'active' : '';
                            $firstSub  = $sub_menu[$key][0]['url'] ?? '';
                            $firstSub  = ltrim((string)$firstSub, '/');
                            $title     = htmlspecialchars($val['title'] ?? '', ENT_QUOTES, 'UTF-8');
                            echo '<li class="' . $itemClass . ' ' . $isActive . '"><a href="/admin/' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . '/' . htmlspecialchars($firstSub, ENT_QUOTES, 'UTF-8') . '">' . $title . '</a></li>';
                        }
                    }
                    ?>
                </ul>
            </nav>

            <div class="scroll_top">
                <div id="triangle-up"></div>
            </div>

            <!-- Main Content -->
            <section id="main_content">
                <nav id="secondary_nav">
                    <dl class="user_info">
                        <dt>
                            <?php
                            $adminId   = (string)($admin['id'] ?? '');
                            $userPhoto = '/images/admin/avatar.png';
                            if ($adminId !== '' && is_file(APPPATH . '/files/users/' . $adminId . '.jpg')) {
                                $userPhoto = '/files/users/' . $adminId . '.jpg';
                            }
                            ?>
                            <a href="/admin/users/edit/<?php echo htmlspecialchars($adminId, ENT_QUOTES, 'UTF-8'); ?>">
                                <img src="<?php echo htmlspecialchars($userPhoto, ENT_QUOTES, 'UTF-8'); ?>" width="79" class="personal_photo" />
                            </a>
                        </dt>
                        <dd>
                            <a class="welcome_user">
                                Welcome, <strong>
                                <?php
                                $first = htmlspecialchars($admin['first_name'] ?? '', ENT_QUOTES, 'UTF-8');
                                $last  = htmlspecialchars($admin['last_name']  ?? '', ENT_QUOTES, 'UTF-8');
                                echo trim($first . ' ' . $last);
                                ?>
                                </strong>
                            </a>
                            <span class="log_data">Today is: <?php echo date('m-d-Y'); ?></span>
                            <a href="/admin/version" class="left ver">version</a>
                            <a class="logout right" href="/admin/logout">Logout</a>
                        </dd>
                    </dl>

                    <ul>
                        <?php
                        if (!empty($controller) && !empty($sub_menu[$controller]) && is_array($sub_menu[$controller])) {
                            foreach ($sub_menu[$controller] as $row) {
                                $url   = ltrim((string)($row['url'] ?? ''), '/');
                                $title = $row['title'] ?? '';
                                $checked = (isset($action) && $action === ($row['url'] ?? null)) ? ' class="checked"' : '';
                                echo '<li' . $checked . '><a href="/admin/' . htmlspecialchars($controller, ENT_QUOTES, 'UTF-8') . '/' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">' . $title . '</a></li>';
                            }
                        }
                        ?>
                    </ul>
                </nav>

                <!-- Content Wrap -->
                <div id="content_wrap">
                    <?php
                    // $content should contain rendered HTML from the controller/view layer.
                    // Do not escape here.
                    echo $content ?? '';
                    ?>
                </div>
            </section>
        </div>

    <?php } else { ?>

        <?php echo $content ?? ''; ?>

    <?php } ?>

    <div class="modal_bg">
        <div>
            <img src="/images/rem.png" class="close_modal" alt="">
            <img class="loading" src="/images/admin/loading.gif" alt="">
            <em class="err"></em><br>
            <div class="contents"></div>
        </div>
    </div>

    <!--[if lt IE 9]>
    <script src="/js/admin/html5.js"></script>
    <![endif]-->
    <!-- <script type="text/javascript" src="/js/admin/jquery.autogrowtextarea.js"></script> -->
    <script type="text/javascript" src="/js/admin/jquery-ui-1.9.2.js"></script>
    <script type="text/javascript" src="/js/admin/form_elements.js"></script>
    <script type="text/javascript" src="/js/admin/jquery.tablesorter.js"></script>
    <script type="text/javascript" src="/js/admin/input.format.js"></script>
    <script type="text/javascript" src="/js/jquery.numeric.js"></script>
    <script type="text/javascript" src="/js/admin/main.js"></script>
    <script type="text/javascript" src="/js/tinymce/tinymce.min.js"></script>
    <script type="text/javascript" src="/js/admin/admin.init.js"></script>

    <?php
    if (!empty($scripts)) {
        foreach ((array)$scripts as $js) {
            echo '<script src="' . htmlspecialchars($js, ENT_QUOTES, 'UTF-8') . '"></script>';
        }
    }
    ?>
</body>
</html>