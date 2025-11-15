<input type="search" name="content_search" placeholder="Live Search">
<div>
    <?php
    $tmp_title = '';
    if (!empty($items)) {
        foreach ($items as $val) {
            if ($tmp_title !== $val[0]['type_title']) {
                $tmp_title = $val[0]['type_title'];
                ?></div>
            <div>
                <hr><br>
                <h6><?= $val[0]['type_title'] ?></h6>
                <?php
            }
            ?>
            <div class="one_item">
                <input type="checkbox" class="ind_new left marg_r10 marg_t10" name="<?=$val[0]['item_id']?>" data-type="<?=floor($val[0]['item_id']/100)*100;?>" value="<?= $val[0]['id'] ?>" <?php if(!empty($checked) && !empty($checked[$val[0]['item_id']])&& !empty($checked[$val[0]['item_id']][$val[0]['id']]))echo 'checked="checked"';?>>
                <img src="/files/items/thumbnails/<?= $val[0]['item_id'] ?>.png" width="60" class="left marg_r10">
                <span class="left title"><?= $val[0]['title'] ?></span>
                <br class="clear">
            </div>
            <?php
        }
    }
    ?>  
</div>