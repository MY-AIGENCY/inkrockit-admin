<div id="activity_stats">
    <h3>Users</h3>
</div>
<ul class="form_fields_container search_block">
    <li>
        <a class="whitishBtn button_small marg_l10 left" href="/admin/users/edit"><span class="iconsweet">+</span>Add new User</a>

        <div class="form_input" style="width: auto">
            <label>Search:</label>
            <input type="text" name="search_user_print" value="<?=@$search_val?>" class="search_field left">
            <label>Group:</label>
            <select name="user_group_search">
                <option value="">-</option>
                <?php
                if (!empty($user_groups)) {
                    foreach ($user_groups as $key => $val) {
                        ?><option <?php if(!empty($search_group) && $search_group==$key)echo'selected="selected"'?> value="<?= $key ?>"><?= $val ?></option><?php
                    }
                }
                ?>
            </select>
        </div>

    </li>    
</ul>

<div class="one_wrap">

    <div class="widget">
        <div class="widget_body">

            <?php require_once APPPATH.'/views/block_admin/users_table.php';?>

        </div>
    </div>
</div>