<div id="activity_stats">
    <h3><?=(!empty($cat['title']))? 'Edit Category - '.$cat['title'] : 'Add New Category' ;?></h3>
</div>

<form method="POST">
    <ul class="form_fields_container" style="width: 800px">
        <li>
            <label>Title</label>
            <div class="form_input">
                <input type="text" name="title" <?= (!empty($cat['title'])) ? 'value="' . $cat['title'] . '"' : '' ?>>
            </div>
            <br class="clear">
            
            <label>ABBR</label>
            <div class="form_input">
                <input type="text" name="abbr" <?= (!empty($cat['abbr'])) ? 'value="' . $cat['abbr'] . '"' : '' ?>>
            </div>
            <br class="clear">
            
            <label>Active?</label>
            <div class="form_input">
                <select name="active">
                    <option value="1">Yes</option>
                    <option value="0" <?= (!empty($cat) && $cat['active'] == 0) ? 'selected' : '' ?>>No</option>
                </select>
            </div>
        </li>
        <li>
            <?php if ((!empty($cat['title']))) { ?>
                <input class="button_small whitishBtn" type="submit" value="< Save and Prev" name="save_and_prev" style="margin-left: 20px">
            <?php } ?>
            <input class="button_small whitishBtn" type="submit" value="Save" name="add_cat">
            <?php if ((!empty($cat['title']))) { ?>
                <input class="button_small whitishBtn" type="submit" value="Save and Next >" name="save_next">
            <?php } ?>
            <a href="/admin/inspiration/categories" class="button_small whitishBtn">Cancel</a>
        </li>
    </ul>
</form>