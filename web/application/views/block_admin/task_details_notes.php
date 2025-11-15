<div class="left" style="width: 100%; min-width: 950px;">
    <input name="request_id" type="hidden" value="<?= $order['id'] ?>">

    <div class="right" style="background: #FCF783; padding: 10px">
        <strong>Only for Data entry person!</strong><br>
        <?php
        $user_data_entry = array();
        if (!empty($user['admin_comment'])) {
            $user_data_entry = unserialize($user['admin_comment']);
        }
        ?>
        <input type="checkbox" data-type="processed" name="set_user_processed" <?php if (!empty($user_data_entry['processed'])) echo 'checked="checked"' ?>> Processed 
        <input style="margin-left: 20px" type="checkbox" data-type="revision" name="set_user_revision" <?php if (!empty($user_data_entry['revision'])) echo 'checked="checked"' ?>> Revision
        <br><small style="font-size: 12px;">Date<input type="text" name="note_date" class="medium date_time" placeholder="yyyy-mm-dd hh:mm"></small>

    </div>

    <div class="left" style="width: 250px">
        <h4 style="font-size: 15px">
            <span class="ui-icon ui-icon-pencil edit_user_address pointer">edit</span>
            <div class="compleate_user_address" style="text-align: left; margin-left: 40px">
                <?= $user['company'] ?><br>
                <?= $user['first_name'] . ' ' . $user['last_name'] ?><br>
                <?= $user['street'] ?><br>
                <?= $user['city'] ?>, <?= $user['state'] ?> <?= $user['zipcode'] ?><br>
                <?= $user['phone'] ?> <?= (!empty($user['phone_ext'])) ? ' x' . $user['phone_ext'] : ''; ?>
            </div>
        </h4>
        <div class="hide edit_user_address_form">
            <input type="text" name="addr_company" value="<?= $user['company'] ?>" style="width: 190px"><br>
            <input type="text" name="addr_first_name" value="<?= $user['first_name'] ?>" style="width: 90px">
            <input type="text" name="addr_last_name" value="<?= $user['last_name'] ?>" style="width: 90px"><br>
            <input type="text" name="addr_street" value="<?= $user['street'] ?>" style="width: 190px"><br>
            <input type="text" name="addr_city" value="<?= $user['city'] ?>" style="width: 100px">
            <input type="text" name="addr_state" value="<?= $user['state'] ?>" style="width: 30px">
            <input type="text" name="addr_zipcode" value="<?= $user['zipcode'] ?>" class="zip" style="width: 38px"><br>
            <input type="text" name="addr_phone" value="<?= $user['phone'] ?>" class="phone" style="width: 150px">
            <input type="text" name="addr_phone_ext" value="<?= $user['phone_ext'] ?>" placeholder="ext" style="width: 30px">

            <br>
            <input type="button" name="save_user_address_form" class="hide" data-id="<?= $user['id'] ?>" value="Save">
            <input type="button" name="cancel_user_address_form" value="Cancel">
        </div> 

    </div>

    <div class="left">
        <h6 style="margin-bottom: 5px">
            <small style="font-size: 12px; margin-left: 424px">Source</small>
            <small style="font-size: 12px; margin-left: 110px; display: none" class="assign_to">Assign to</small>
        </h6>

        <textarea style="width: 400px; min-width: 400px" name="new_note" class="left"></textarea>

        <div class="left">
            <select name="type" style="width: 145px">
                <option value="">-</option>
                <option value="call_in">Call In</option>
                <option value="call_out">Call Out</option>
                <option value="email_in">Email In</option>
                <option value="email_out">Email Out</option>
                <option value="shipping_in">Shipping In</option>
                <option value="shipping_out">Shipping Out</option>
                <optgroup label="Only for Data entry" style="background: #FCF783">
                    <?php /*
                      <option style="background: #FCF783" value="balance">Balance</option>
                      <option style="background: #FCF783" value="installment">Installment</option>
                      <option style="background: #FCF783" value="deposit">Deposit</option>
                     */ ?>
                    <option style="background: #FCF783" value="payment">Payment</option>
                    <option style="background: #FCF783" value="credit">Credit</option>
                </optgroup>
            </select>

            <select name="assign_to" class="hide">
                <option value="">-</option>
                <?php
                if (!empty($admin_users)) {
                    foreach ($admin_users as $val) {
                        ?><option value="<?= $val['id'] ?>"><?= $val['first_name'] . ' ' . $val['last_name'] ?></option><?php
                    }
                }
                ?>
            </select>
            <br>

            <div class="left" style="width: 70px; margin-right: 6px">
                <label>
                    <input type="checkbox" value="1" name="note_required" class="left"> 
                    <small style="font-size: 12px; margin-left: 6px; display: block; width: 70px">Action required</small>
                </label>
            </div>

            <a class="button_big" id="add_new_note">Submit</a>
        </div>

        <div class="left hide add_payment_options" style="width: 180px; margin-left: 10px">
            <label class="fix_label">Summ,$:</label><input type="text" class="medium" name="pay_note_summ"><br class="clear">
            <label class="fix_label">Date:</label><input type="text" name="pay_note_date" class="medium date_time"><br class="clear">
            <label class="fix_label">&nbsp;</label>yyyy-mm-dd hh:mm<br class="clear">

            <label class="fix_label">Job:</label>
            <select class="left" name="select_note_job">
                <option value="">none</option>
                <?php
                if (!empty($job_id)) {

                    if (!empty($sorted)) {
                        foreach ($sorted as $key => $val) {
                            ksort($val);
                            foreach ($val as $k => $v) {
                                $current_id = (empty($v['job_id'])) ? $v['estimate_id'] : $v['job_id'];
                                ?>
                                <option value="<?= $current_id ?>" <?php if (!empty($current_job_id) && $current_job_id == $current_id) echo'selected="selected"' ?>><?= $current_id ?></option>
                                <?php
                            }
                        }
                    }
                }
                ?>
            </select>
            <br class="clear">

            <label class="fix_label">Card:</label><select name="pay_note_card">
                <option value="">none</option>
                <?php
                if (!empty($credit_cards)) {
                    foreach ($credit_cards as $val) {
                        ?><option value="<?= $val['id'] ?>"><?= $val['card_number'] ?></option><?php
                    }
                }
                ?>
            </select>
            <br class="clear">
        </div>
    </div>

    <br class="clear"><br>
    <div class="order_notes">
        <?php
        if (!empty($notes)) {
            foreach ($notes as $val) {
                require 'one_note.php';
            }
        }
        ?>
    </div>
</div>