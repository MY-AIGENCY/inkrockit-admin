<h3>
    Edit Transaction
    <select name="editing_trans_type" class="marg_l20">
        <option value="">select method...</option>
        <option value="manual_cc" <?php if ($trans['type'] == 'manual_cc') echo 'selected="selected"' ?>>Credit Card</option>
        <option value="manual_check" <?php if ($trans['type'] == 'manual_check') echo 'selected="selected"' ?>>Check</option>
        <option value="manual_cash" <?php if ($trans['type'] == 'manual_cash') echo 'selected="selected"' ?>>Cash</option>
        <option value="manual_misc" <?php if ($trans['type'] == 'manual_misc') echo 'selected="selected"' ?>>Misc</option>
        <option value="failed" <?php if ($trans['type'] == 'failed') echo 'selected="selected"' ?>>Failed</option>
        <option value="order_confirmed" <?php if ($trans['type'] == 'order_confirmed') echo 'selected="selected"' ?>>Order Total - For Data Entry Only</option>
        <option value="credit" <?php if ($trans['type'] == 'credit') echo 'selected="selected"' ?>>Credit - For Data Entry Only</option>
    </select>

    <select name="select_job_trans">
        <?php
        if (!empty($jobs)) {
            foreach ($jobs as $val) {
                ?><option value="<?= $val['id'] ?>" <?php
                if ($trans['job_id'] == $val['id']) {
                    echo 'selected="selected"';
                }
                ?>><?= $val['job_id'] ?></option><?php
                    }
                }
                ?>
    </select>
</h3><br>


<?php
$date = explode(' ', $trans['date']);
$day = explode('-', $date[0]);
$day_str = $day[1] . '/' . $day[2] . '/' . $day[0];
?>
<div class="block_creditcard">
    <label>Date:</label>
    <input type="text" name="trans_date" class="datepicker_mod" value="<?= $day_str ?>">
    <br class="clear">

    <label>Amount, $:</label>
    <input type="text" name="trans_amount" value="<?= $trans['summ'] ?>">
    <br class="clear">

    <label>Notes:</label>
    <textarea name="trans_note"><?= $trans['description'] ?></textarea>
    <br class="clear">
    
    <div class="for_card_edits <?php if($trans['type']!='manual_cc')echo 'hide'?>">
        <label>Credit Card:</label>
        <select name="trans_credit_card" style="width: 78%">
        <?php
        if(!empty($credit_cards)){
            ?><option value=""></option><?php
            foreach($credit_cards as $val){
                ?><option value="<?=$val['id']?>" <?php if($trans['card_id']==$val['id'])echo 'selected="selected"'?>><?=$val['view_as']?> - <?=  substr(trim($val['card_number']), -4)?></option><?php
            }
            ?><option value="add">Add New...</option><?php
        }
        ?>
        </select>
        <br class="clear">
        <div class="add_new_card hide" style="margin-left: 100px">
            <input type="text" name="add_card_name" placeholder="Name on Card">
            <select name="add_card_type">
                <option value="American Express">American Express</option>
                <option value="Visa">Visa</option>
                <option value="Master Card">Master Card</option>
                <option value="Diners Club">Diners Club</option>
                <option value="Discover">Discover</option>
                <option value="JCB">JCB</option>
            </select>
            <input type="text" name="last_digits" maxlength="4" placeholder="Last 4 digits">
        </div>
    </div>
    
    <div class="for_check_edits <?php if($trans['type']!='check')echo 'hide'?>">
        <label>Check#:</label>
        <input type="text" name="check_number">
        <br class="clear">
    </div>
    
    <label>&nbsp;</label>
    <input type="button" name="edit_transaction" data-id="<?= $trans['id'] ?>" value="Save" class="button_small greenishBtn">
    <span class="error trans_card_error"></span>
    <br class="clear">
</div> 
