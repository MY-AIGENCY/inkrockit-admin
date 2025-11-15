<strong><?= (empty($ink['name'])) ? 'Add New INK' : 'Edit INK - ' . $ink['name']; ?></strong>
<br><br>

<?php if (@$_SERVER['HTTP_REFERER'] == 'http://' . $_SERVER['SERVER_NAME'] . @$_SERVER['REQUEST_URI'] && !empty($_POST['name'])) { ?>
    <div class="msgbar msg_Success hide_onC">
        <span class="iconsweet">=</span><p>Information Saved</p>
    </div>
<?php } ?>

<form method="POST">
    <ul class="form_fields_container" style="width: 800px">
        <li>
            <label>Name</label>
            <div class="form_input">
                <input type="text" name="name" <?= (!empty($ink['name'])) ? 'value="' . $ink['name'] . '"' : '' ?>>
            </div>
            <br class="clear">
            <label>Description</label>
            <div class="form_input">
                <textarea name="description"><?= @$ink['description'] ?></textarea>
            </div>
            <br class="clear">
            <label>Price</label>
            <div class="form_input">
                <input type="text" name="price" <?= (!empty($ink['price'])) ? 'value="' . $ink['price'] . '"' : '' ?> style="width: 100px"> $
            </div>
        </li>
        <li>
            <input class="button_small whitishBtn" type="submit" value="Save" name="save_ink" style="margin-left: 20px">
            <a href="/admin/print/inks" class="button_small whitishBtn">Cancel</a>
        </li>
    </ul>
</form>