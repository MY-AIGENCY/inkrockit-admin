<div class="sel_category">
    <div class="new_ind_form">
        <input type="button" name="create_content" value="+ Create Content">
    </div>
    <hr>
    <div class="content_industry_list">
        <?php
        if (!empty($avaliable)) {
            foreach ($avaliable as $val) {
                if ($val['active'] == 1) {
                    ?><label><input type="checkbox" name="industry_item" value="<?= $val['index'] ?>" class="left" <?php if (in_array($val['index'], $checked_items)) echo 'checked="checked"'; ?>><small><?= $val['title'] ?> (<spam class="used"><?php
                    if (!empty($all_checked[$val['index']])) {
                        echo count($all_checked[$val['index']]);
                    } else {
                        echo '0';
                    }
                    ?></spam>/<?= $val['items'] ?>)</small></label><?php
                }
            }
        } 
        ?>
    </div>    
    <div class="collections hide">
        <?php
        if (!empty($collections)) {
            foreach ($collections as $val) {
                ?><label><input type="radio" name="industry_collection" value="<?= $val['id'] ?>" class="left"><small><?= $val['title'] ?></small>
                    <a class="iconsweet tip_north remove_collection" data-id="<?= $val['id'] ?>" style="float: right; margin-right: 4px" original-title="Delete">X</a>
                </label><?php
            }
        }
        ?>
    </div>

    <div class="nav_buttons">
        <hr>
        <input type="button" value="Save" name="save_industry_items">
        <input type="button" name="close_edit" value="Cancel">
        <div class="right saved_button <?php if (empty($collections)) echo'hide' ?>">Saved Contents &raquo;</div>
    </div>
</div>
<div class="sel_products"></div>
<br class="clear">