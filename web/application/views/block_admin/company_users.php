
<button name="sel_user_form" class="right hide">Select Exist User</button>
<div class="sel_user">

    <?php
    if (empty($job)) {
        ?>
        Assign a contact person to the company profile<br>
        <br>
        <?php
    } else {
        ?>
        Assign a contact person to a Job# <?= $job['job_id'] ?><br>
        <br>
        <?php
    }
    ?>

    <div class="users_list">
        Current contact person: 
        <select name="select_comp_user" class="right">
            <?php
            if ($users) {
                foreach ($users as $val) {
                    ?>
                    <option value="<?= $val['id'] ?>" <?php if ($checked == $val['id']) echo 'selected="selected"' ?>><?= $val['first_name'] . ' ' . $val['last_name'] ?></option>
                    <?php
                }
            }
            ?>
        </select>
        <br class="clear">
        <ul class="right marg_r10 this_users_list">
            <?php
            if ($users) {
                foreach ($users as $val) {
                    ?>
                    <li data-id="<?= $val['id'] ?>">
                        <a class="ui-icon ui-icon-pencil left edit_this_user">edit</a>
                        <?php
                        if (count($users) > 1) {
                            ?>
                            <a class="ui-icon ui-icon-closethick left remove_this_user">del</a>
                        <?php } ?>
                        <?= $val['first_name'] . ' ' . $val['last_name'] ?>
                    </li>
                    <?php
                }
            }
            ?>
        </ul>
        <br class="clear">
        Do you want to add new contact person to the company profile: <button name="add_user_form" class="right">Add new User</button>
    </div>
    <br>
</div>
<div class="hide add_user"> 
    <br class="clear">
    <label>First name</label>
    <input type="text" name="first_name"><br class="clear">
    <label>Last name</label>
    <input type="text" name="last_name"><br class="clear">
    <label>Email</label>
    <input type="text" name="email"><br class="clear">
    <label>Address</label>
    <input type="text" name="street"><br class="clear">
    <label>Address2</label>
    <input type="text" name="street2"><br class="clear">
    <label>City</label>
    <input type="text" name="city"><br class="clear">
    <label>State</label>
    <input type="text" name="state"><br class="clear">
    <label>Zipcode</label>
    <input type="text" name="zipcode" class="zip" ><br class="clear">
    <label>Phone</label>
    <input type="text" name="phone" class="phone" style="width: 130px">
    <input type="text" name="phone_ext" placeholder="ext" style="width: 24px">
    <br class="clear">
    <label>Fax</label>
    <input type="text" name="fax" class="phone"><br class="clear">
    <label>Position</label>
    <input type="text" name="position"><br class="clear">
    <label>&nbsp;</label>
    <button name="user_fast_add">Add</button>
    <button name="user_fast_close">Cancel</button>
    <br>
    <span class="user_err"></span>
    <br>
    <br>
</div>