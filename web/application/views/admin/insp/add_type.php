<div id="activity_stats">
    <h3><?= (!empty($type['title'])) ? 'Edit Industries - ' . $type['title'] : 'Add New Industries'; ?></h3>
</div>

<form method="POST">
    <ul class="form_fields_container" style="width: 800px">
        <li>
            <label>Index</label>
            <div class="form_input">
                <input type="text" name="index" <?= (!empty($type['index'])) ? 'value="' . $type['index'] . '"' : '' ?>>
            </div>
            <br class="clear">
            <label>Title</label>
            <div class="form_input">
                <input type="text" name="title" <?= (!empty($type['title'])) ? 'value="' . $type['title'] . '"' : '' ?>>
            </div>
            <br class="clear">
            <label>Active?</label>
            <div class="form_input">
                <select name="active">
                    <option value="1">Yes</option>
                    <option value="0" <?= (@$type['active'] == 0 && !empty($type)) ? 'selected' : '' ?>>No</option>
                </select>
            </div>
        </li>
        <li>
            <?php if (!empty($type['index'])) {
                ?><input class="button_small whitishBtn" type="submit" value="< Save and Prev" name="save_and_prev" style="margin-left: 20px"><?php }
            ?>            
            <input class="button_small whitishBtn" type="submit" value="Save" name="add_type">
            <?php if (!empty($type['index'])) {
                ?><input class="button_small whitishBtn" type="submit" value="Save and Next >" name="save_next"><?php }
            ?>
            <a href="/admin/inspiration/types" class="button_small whitishBtn">Cancel</a>
        </li>
    </ul>
</form>