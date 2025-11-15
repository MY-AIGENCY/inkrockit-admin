<div>
    <h6 style="margin-bottom: 0">Edit user</h6>
    <hr>
    <label>First name</label>
    <input type="text" name="first_name" value="<?=$user['first_name']?>"><br class="clear">
    <label>Last name</label>
    <input type="text" name="last_name" value="<?=$user['last_name']?>"><br class="clear">
    <label>Email</label>
    <input type="text" name="email" value="<?=$user['email']?>"><br class="clear">
    <label>Address</label>
    <input type="text" name="street" value="<?=$user['street']?>"><br class="clear">
    <label>Address 2</label>
    <input type="text" name="street2" value="<?=$user['street2']?>"><br class="clear">
    <label>City</label>
    <input type="text" name="city" value="<?=$user['city']?>"><br class="clear">
    <label>State</label>
    <input type="text" name="state" value="<?=$user['state']?>"><br class="clear">
    <label>Zipcode</label>
    <input type="text" name="zipcode" class="zip" value="<?=$user['zipcode']?>"><br class="clear">
    <label>Phone</label>
    <input type="text" name="phone" value="<?=$user['phone']?>" class="phone" style="width: 130px">
    <input type="text" name="phone_ext" value="<?=$user['phone_ext']?>" placeholder="ext" style="width: 24px">
    <br class="clear">
    <label>Fax</label>
    <input type="text" name="fax" value="<?=$user['fax']?>" class="phone"><br class="clear">
    <label>Position</label>
    <input type="text" name="position" value="<?=$user['position']?>"><br class="clear">
    <label>&nbsp;</label>
    <button name="user_fast_update" data-id="<?=$user['id']?>">Save</button>
    <button name="user_fast_close">Cancel</button><br>
    <span class="user_err"></span>
    <br>   
</div>