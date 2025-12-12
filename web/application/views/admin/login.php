<div id="login_container">
    <div id="dreamworks_container">

        <div id="login">
            <img src="/images/logo.png" class="logo_ink">
            <?php if (!empty($error)): ?>
                <div style="margin: 10px 0; padding: 10px; border: 1px solid #b94a48; background: #f2dede; color: #b94a48; border-radius: 4px;">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="input_box"> <input placeholder="User Name" name="login" type="text" id="username"></div>
                <div class="input_box"> <input placeholder="Password" name="pass" type="password" id="password"></div>
                <div> <input type="submit" value="Login"></div>
            </form>
        </div>

    </div>
</div>