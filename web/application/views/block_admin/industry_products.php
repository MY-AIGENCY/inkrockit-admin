<?php
if (!empty($items)) {
    foreach ($items as $val) {
        if(in_array($val['item_id'],$checked_items)){
        ?>
        <div style="margin-bottom: 2px;">
            <input type="checkbox" name="ind_item_need" value="<?= $val['item_id'] ?>" data-type="<?=$val['type_index']?>" checked="checked" class="left marg_r10 marg_t10">
            <img src="/files/items/thumbnails/<?= $val['item_id'] ?>.png" width="60" class="left marg_r10">
            <span class="left" style="width: 140px"><?= $val['title'] ?></span>
            <br class="clear">
        </div>
        <?php
        }
    }
    foreach ($items as $val) {
        if(!in_array($val['item_id'],$checked_items)){
        ?>
        <div style="margin-bottom: 2px;">
            <input type="checkbox" name="ind_item_need" value="<?= $val['item_id'] ?>" data-type="<?=$val['type_index']?>" class="left marg_r10 marg_t10">
            <img src="/files/items/thumbnails/<?= $val['item_id'] ?>.png" width="60" class="left marg_r10">
            <span class="left" style="width: 140px"><?= $val['title'] ?></span>
            <br class="clear">
        </div>
        <?php
        }
    }
}
?>