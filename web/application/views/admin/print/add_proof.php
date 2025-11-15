<strong><?= (empty($proof['name'])) ? 'Add New Proof' : 'Edit Proof - ' . $proof['name']; ?></strong>
<br><br>

<?php if (@$_SERVER['HTTP_REFERER'] == 'http://' . $_SERVER['SERVER_NAME'] . @$_SERVER['REQUEST_URI'] && !empty($_POST['name'])) { ?>
    <div class="msgbar msg_Success hide_onC">
        <span class="iconsweet">=</span><p>Information Saved</p>
    </div>
<?php } ?>

<form method="POST" enctype="multipart/form-data">
    <ul class="form_fields_container" style="width: 800px">
        <li>
            <label>Name:</label>
            <div class="form_input">
                <input type="text" name="name" <?= (!empty($proof['name'])) ? 'value="' . $proof['name'] . '"' : '' ?>>
            </div>
            <br class="clear">
            <label>Preview:<br>
                <small>244x198 px</small>
            </label>
            <div class="form_input">
                <?php
                if (is_file(APPPATH . 'files/print/proof/' . @$proof['id'] . '.jpg')) {
                    ?><img src="/files/print/proof/<?= @$proof['id'] ?>.jpg" width="150"><br><?php
                }
                ?>
                <input type="file" name="preview">
            </div>
            <br class="clear">
            <br>
            <label>Price:</label>
            <div class="form_input">
                $<input type="text" name="price" <?= (!empty($proof['price'])) ? 'value="' . $proof['price'] . '"' : '' ?> class="small">
            </div>
            <br class="clear">
            <label>Description:</label>
            <div class="form_input">
                <textarea name="description"><?= @$proof['description'] ?></textarea>
            </div>
        </li>
        <li>
            <input class="button_small whitishBtn" type="submit" value="Save" name="save_proofs" style="margin-left: 20px">
            <a href="/admin/print/proof" class="button_small whitishBtn">Cancel</a>
        </li>
    </ul>
</form>