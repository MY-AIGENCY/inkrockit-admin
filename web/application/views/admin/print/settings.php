<div id="activity_stats">
    <h3>Main Settings</h3>
</div>

<?php
if (@$_SERVER['HTTP_REFERER'] == 'http://' . $_SERVER['SERVER_NAME'] . @$_SERVER['REQUEST_URI']) {
    ?>
    <div class="msgbar msg_Success hide_onC">
        <span class="iconsweet">=</span><p>Information Saved</p>
    </div>
<?php } ?>

<form method="POST">
    <?php if (!empty($settings)) { ?>
        <ul class="form_fields_container" style="width: 800px">
            <li>
                <?php foreach ($settings as $val) { ?>
                    <label><?= $val['title'] ?></label>
                    <div class="form_input"><input type="text" name="<?= $val['key'] ?>" value="<?= $val['val'] ?>"></div>
                    <br class="clear">
                <?php } ?>
            </li>
            <li>
                <input type="submit" class="button_small whitishBtn" name="save_setting" value="Save" style="margin-left: 20px">
            </li>
        </ul>
    <?php } ?>
</form>