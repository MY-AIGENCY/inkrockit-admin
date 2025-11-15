<strong><?= (empty($folding['title'])) ? 'Add New Folding' : 'Edit Folding - ' . $folding['title']; ?></strong>
<br><br>

<?php if (@$_SERVER['HTTP_REFERER'] == 'http://' . $_SERVER['SERVER_NAME'] . @$_SERVER['REQUEST_URI'] && !empty($_POST['title'])) { ?>
    <div class="msgbar msg_Success hide_onC">
        <span class="iconsweet">=</span><p>Information Saved</p>
    </div>
<?php } ?>

<form method="POST">
    <ul class="form_fields_container" style="width: 800px">
        <li>
            <label>Title</label>
            <div class="form_input">
                <input type="text" name="title" <?= (!empty($folding['title'])) ? 'value="' . $folding['title'] . '"' : '' ?>>
            </div>
            <br class="clear">

        </li>
        <li>
            <label>Available for:</label>
            <div class="form_input">
                <?php if (!empty($cat)) { ?>
                    <table class="activity_datatable prices_db" width="600">
                        <tr>
                            <th><input type="checkbox" name="check_all"></th>
                            <th>Product Type</th>
                        </tr>
                        <?php
                        foreach ($cat as $v) {
                            if ($v['active'] == 1) {
                                ?>
                                <tr>
                                    <td width="30" style="text-align: center">
                                        <input type="checkbox" <?php if(in_array($v['id'], $folding_types)) echo 'checked="checked"'?> name="aval_type[]" value="<?= $v['id'] ?>">
                                    </td>
                                    <td><?= $v['title'] ?></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </table>
                <?php } ?>
            </div>
        </li>
        <li>

            <label>Active?</label>
            <div class="form_input">
                <select name="active">
                    <option value="1">Yes</option>
                    <option value="0" <?= (!empty($folding) && $folding['active'] == 0) ? 'selected' : '' ?>>No</option>
                </select>
            </div>
        </li>
        <li>
            <input class="button_small whitishBtn" type="submit" value="Save" name="add_folding" style="margin-left: 20px">
            <a href="/admin/print/folding" class="button_small whitishBtn">Cancel</a>
        </li>
    </ul>
</form>