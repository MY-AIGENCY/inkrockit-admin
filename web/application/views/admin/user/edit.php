<div id="activity_stats">
    <h3>Edit User Information</h3>
</div>

<div class="one_wrap fl_left">
    <div class="widget">
        <form action="" method="post" enctype="multipart/form-data">
            <ul class="form_fields_container" style="width: 800px">
                <?php
                if (!empty($user['id'])) {
                    ?>
                    <li>
                        <label>ID</label>
                        <div class="form_input">
                            <p><?=$user['id']?></p>
                        </div>
                    </li>
                    <?php
                }
                ?>
                <li>
                    <label>Login</label>
                    <div class="form_input">
                        <input type="text" name="login" <?= (!empty($user['login'])) ? 'value="' . $user['login'] . '"' : '' ?>>
                    </div>
                </li>
                <li>
                    <label><?=(!empty($user['id']))? 'Change Password To' : 'Password' ;?></label>
                    <div class="form_input">
                        <input type="text" name="password">
                    </div>
                </li>
                <li>
                    <label>Email</label>
                    <div class="form_input">
                        <input type="text" name="email" <?= (!empty($user['email'])) ? 'value="' . $user['email'] . '"' : '' ?>>
                    </div>
                </li>
                <li>
                    <label>First Name</label>
                    <div class="form_input">
                        <input type="text" name="first_name" <?= (!empty($user['first_name'])) ? 'value="' . $user['first_name'] . '"' : '' ?>>
                    </div>
                </li>
                <li>
                    <label>Last Name</label>
                    <div class="form_input">
                        <input type="text" name="last_name" <?= (!empty($user['last_name'])) ? 'value="' . $user['last_name'] . '"' : '' ?>>
                    </div>
                </li>
                <li>
                    <label>Photo</label>
                    <div class="form_input">
                        <input type="file" name="photo"><br>
                        <img src="<?=(!empty($user['id']) && is_file(APPPATH.'/files/users/'.$user['id'].'.jpg'))? '/files/users/'.$user['id'].'.jpg?v='.time() : '/images/admin/avatar.png' ;?>" style="border-radius: 50%" width="79" />
                    </div>
                </li>
                <li>
                    <label>Access</label>
                    <div class="form_input">
                        <select name="group">
                            <?php
                            if(!empty($groups)){
                                foreach($groups as $key=>$val){
                                    ?><option <?php if(!empty($user['group_id']) && $user['group_id']==$key)echo'selected="selected"'?> value="<?=$key?>"><?=$val?></option><?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </li>
                <li>
                    <input type="submit" name="save" value="Save" class="button_small whitishBtn" style="margin-left: 20px">
                    <a href="/admin/users" class="button_small whitishBtn">Cancel</a>
                </li>
            </ul>
        </form>
    </div>
    <br class="clear">
</div>
<br class="clear" /> 