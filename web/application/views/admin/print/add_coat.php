<strong><?= (empty($cat['title'])) ? 'Add New ' . $page : 'Edit ' . $page . ' - ' . $cat['title']; ?></strong>
<br><br>

<div class="msgbar msg_Error hide"><p></p></div>

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
                <input type="text" name="title" <?= (!empty($cat['title'])) ? 'value="' . htmlspecialchars($cat['title']) . '"' : '' ?>>
            </div>
            <br class="clear">
            <label>ABBR</label>
            <div class="form_input">
                <input type="text" name="abbr" <?= (!empty($cat['abbr'])) ? 'value="' . htmlspecialchars($cat['abbr']) . '"' : '' ?>>
            </div>
            <br class="clear">
            <label>Days</label>
            <div class="form_input">
                <input type="text" name="days" <?= (!empty($cat['days'])) ? 'value="' . $cat['days'] . '"' : '' ?>><br>
                <small>(examples: -1; 1; 2)</small>
            </div>
            <br class="clear"><br>
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
                        if (!empty($coat_price)) {
                            foreach ($coat_price as $val) {
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
            <br class="clear">
            <br>
            <label>Active?</label>
            <div class="form_input">
                <select name="active">
                    <option value="1">Yes</option>
                    <option value="0" <?= (!empty($cat) && $cat['active'] == 0) ? 'selected' : '' ?>>No</option>
                </select>
            </div>
        </li>
        <li>
            <input type="submit" class="button_small whitishBtn" name="save_prev" value="< Save and Prev" style="margin-left: 20px">
            <input type="submit" class="button_small whitishBtn" name="add_coat" value="Save">
            <input type="submit" class="button_small whitishBtn" name="save_next" value="Save and Next >">
            <a href="/admin/print/<?= $page ?>" class="button_small whitishBtn">Cancel</a>
        </li>
    </ul>
</form>
<script>
    $(function() {
        $('input[name=add_coat]').click(function() {
            var title = $('input[name=title]').val()
            var days = $('input[name=days]').val()
            var err = ''
            if (!title) {
                err = 'Field Title is empty'
            } else if (!days) {
                err = 'Field Days is empty'
            }
            if (err) {
                $('.msg_Error').show().children().text(err);
                return false;
            }
        })
    })
</script>