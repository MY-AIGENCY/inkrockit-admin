<strong><?= (empty($paper['name'])) ? 'Add New Paper' : 'Edit Paper - ' . $paper['name']; ?></strong>
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
                <input type="text" name="name" <?= (!empty($paper['name'])) ? 'value="' . $paper['name'] . '"' : '' ?>>
            </div>
            <br class="clear">
            <label>Description</label>
            <div class="form_input">
                <textarea name="description"><?= @$paper['description'] ?></textarea>
            </div>
            <br class="clear">
            <br>
            <label>Prices</label>
            <div class="form_input">
                <a style="margin:5px;" class="button_small whitishBtn right add_coat_price"><span class="iconsweet">+</span>Add Price</a>
                <table class="activity_datatable prices_db" width="60%" border="0" cellspacing="0" cellpadding="3">
                    <tbody>
                        <tr>
                            <th width="60">Count</th>
                            <th>Price for ONE, $</th>
                            <th width="60">Action</th>
                        </tr>
                        <?php
                        if (!empty($paper_price)) {
                            foreach ($paper_price as $val) {
                                ?>
                                <tr>
                                    <td><input type="text" value="<?= $val['count'] ?>" name="count[]" class="short"></td>
                                    <td><input type="text" name="price[]" value="<?= $val['price'] ?>" class="short"></td>
                                    <td class="iconsweet"><a class="tip_north remove_coat">X</a></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>

            </div>
        </li>
        <li>
            <input class="button_small whitishBtn" type="submit" value="Save" name="save_paper" style="margin-left: 20px">
            <a href="/admin/print/papers" class="button_small whitishBtn">Cancel</a>
        </li>
    </ul>
</form>