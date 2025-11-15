<div>
    <?php
    if (!empty($user)) {
        foreach ($user as $val) {
            switch ($type) {
                case 'email':
                    ?><div class="all_additional" data-type="email">
                        <input type="text" name="addtional_email" value="<?= $val['email'] ?>">
                        <br class="clear"></div><?php
                    break;
                case 'phone':
                    ?>
                    <div class="all_additional" data-type="phone">
                        <select name="addtional_phone_type">
                            <option value="cell">cell</option>
                            <option value="home" <?php if (@$val['type'] == 'home') echo'selected="selected"'; ?>>home</option>
                            <option value="office" <?php if (@$val['type'] == 'office') echo'selected="selected"'; ?>>office</option>
                        </select>
                        <input type="text" name="addtional_phone" value="<?= $val['phone'] ?>" class="phone">
                        <input type="text" name="addtional_phone_ext" value="<?= $val['ext'] ?>" placeholder="ext" style="width: 30px">
                        <br class="clear">
                    </div><?php
                    break;
            }
        }
    }else {
        switch ($type) {
            case 'email':
                ?><div class="all_additional" data-type="email">
                    <input type="text" name="addtional_email">
                    <br class="clear"></div><?php
                break;
            case 'phone':
                ?><div class="all_additional" data-type="phone">
                    <select name="addtional_phone_type">
                        <option value="cell">cell</option>
                        <option value="home">home</option>
                        <option value="office">office</option>
                    </select>
                    <input type="text" name="addtional_phone" class="phone">
                    <input type="text" name="addtional_phone_ext" placeholder="ext" style="width: 30px">
                    <br class="clear">
                </div><?php
                break;
        }
    }
    ?>
</div>
<hr><input type="button" name="save_additional_<?= $type ?>" value="Save">
<input type="button" name="close_edit" value="Cancel">
<span class="right add_fast_additional" data-type="<?= $type ?>">add</span>