<h3>
    Add Transaction
    <select name="trans_type" class="marg_l20">
        <option value="">select method...</option>
        <option value="creditcard">Credit Card</option>
        <option value="check">Check</option>
        <option value="cash">Cash</option>
        <option value="misc">Misc</option>
        <option value="confirm">Order Total - For Data Entry Only</option>
        <option value="failed">Failed - For Data Entry Only</option>
        <option value="credit">Credit - For Data Entry Only</option>
    </select>

    <select name="select_job_trans">
        <?php
        if (!empty($jobs)) {
            foreach ($jobs as $val) {
                ?><option value="<?= $val['id'] ?>" <?php
                if ($active == $val['id']) {
                    echo 'selected="selected"';
                }
                ?>><?= $val['job_id'] ?></option><?php
                    }
                }
                ?>
    </select>
</h3><br>
<div class="block_trans">
    
    
    <div class="hide block_creditcard">
        <div class="set_order_total hide">
            <label>Order Total:</label>
            <?php
            if (!empty($job_info['order_total'])) {
                echo '<p class="lh30">$' . number_format($job_info['order_total'], 2).'</p>';
            } else {
                ?><input type="text" name="set_trans_total" class="numeric"><?php
            }
            ?>
            <br class="clear">
        </div>
        
        <label>Date:</label>
        <input type="text" name="trans_date" class="datepicker_mod">
        <br class="clear">
        
        <label>Credit Card:</label>
        <select name="trans_credit_card" style="width: 467px">
            <option value=""></option>
            <?php
            if (!empty($credit_cards)) {
                foreach ($credit_cards as $card) {
                    ?><option value="<?= $card['id'] ?>" <?php
                    if (!empty($card['default'])) {
                        echo 'selected="selected"';
                    }
                    ?>><?php
                                $last_dig = substr($card['card_number'], -4);
                                echo $card['view_as'] . ' - X' . $last_dig . ' | ';
                                if (!empty($card_jobs) && !empty($card_jobs[$card['id']])) {
                                    $all_str = implode(', ', $card_jobs[$card['id']]);
                                    echo substr($all_str, 0, 45);
                                }
                                ?></option><?php
                }
            }
            ?>
            <option value="add">Add New...</option>
        </select>
        <div class="hide add_new_card" style="margin-left: 100px">
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
        <br class="clear">

        <label>Amount, $:</label>
        <input type="text" name="trans_amount">
        <br class="clear">

        <label>Notes:</label>
        <textarea name="trans_note">System was not working. Card processed manually.</textarea>
        <br class="clear">
        <label>&nbsp;</label>
        <input type="button" name="add_card_transaction" value="Submit" class="button_small greenishBtn">
        <span class="error trans_card_error"></span>
        <br class="clear">
    </div>
    
    
    <div class="hide block_cash">
        <div class="set_order_total hide">
            <label>Order Total:</label>
            <?php
            if (!empty($job_info['order_total'])) {
                echo '<p class="lh30">$' . number_format($job_info['order_total'], 2).'</p>';
            } else {
                ?><input type="text" name="set_trans_total" class="numeric"><?php
            }
            ?>
            <br class="clear">
        </div>
        
        <label>Date:</label>
        <input type="text" name="cash_trans_date" class="datepicker_mod">
        <br class="clear">

        <label>Amount, $:</label>
        <input type="text" name="cash_trans_amount">
        <br class="clear">

        <label>Notes:</label>
        <textarea name="cash_trans_note">System was not working. Card processed manually.</textarea>
        <br class="clear">
        <label>&nbsp;</label>
        <input type="button" name="add_cash_transaction" value="Submit" class="button_small greenishBtn">
        <span class="error trans_cash_error"></span>
        <br class="clear">
    </div>
    
    
    <div class="hide block_check">
        <div class="set_order_total hide">
            <label>Order Total:</label>
            <?php
            if (!empty($job_info['order_total'])) {
                echo '<p class="lh30">$' . number_format($job_info['order_total'], 2).'</p>';
            } else {
                ?><input type="text" name="set_trans_total" class="numeric"><?php
            }
            ?>
            <br class="clear">
        </div>
        <label>Date:</label>
        <input type="text" name="check_trans_date" class="datepicker_mod">
        <br class="clear">

        <label>Amount, $:</label>
        <input type="text" name="check_trans_amount">
        <br class="clear">

        <label>Check#:</label>
        <input type="text" name="check_number">
        <br class="clear">

        <label>Notes:</label>
        <textarea name="check_note">System was not working. Card processed manually.</textarea>
        <br class="clear">

        <label>&nbsp;</label>
        <input type="button" name="add_check_transaction" value="Submit" class="button_small greenishBtn">
        <span class="error trans_check_error"></span>
        <br class="clear">
    </div>
    
    
    <div class="hide block_misc">

        <div class="set_order_total hide">
            <label>Order Total:</label>
            <?php
            if (!empty($job_info['order_total'])) {
                echo '<p class="lh30">$' . number_format($job_info['order_total'], 2).'</p>';
            } else {
                ?><input type="text" name="set_trans_total" class="numeric"><?php
            }
            ?>
            <br class="clear">
        </div>
        <label>Date:</label>
        <input type="text" name="misc_trans_date" class="datepicker_mod">
        <br class="clear">

        <label>Amount, $:</label>
        <input type="text" name="misc_trans_amount">
        <br class="clear">

        <label>Notes:</label>
        <textarea name="misc_trans_note">System was not working. Card processed manually.</textarea>
        <br class="clear">
        <label>&nbsp;</label>
        <input type="button" name="add_misc_transaction" value="Submit" class="button_small greenishBtn">
        <span class="error trans_misc_error"></span>
        <br class="clear">
    </div>
    
    
    <div class="hide block_confirm">
        <label>Date:</label>
        <input type="text" name="confirm_trans_date" class="datepicker_mod">
        <br class="clear">

        <label>Amount, $:</label>
        <input type="text" name="confirm_trans_amount">
        <br class="clear">
        <label>&nbsp;</label>
        <input type="button" name="add_confirm_transaction" value="Submit" class="button_small greenishBtn">
        <span class="error trans_confirm_error"></span>
        <br class="clear">
    </div>

    
    <div class="hide block_failed">
        <label>Date:</label>
        <input type="text" name="failed_trans_date" class="datepicker_mod">
        <br class="clear">

        <label>Amount, $:</label>
        <input type="text" name="failed_trans_amount">
        <br class="clear">

        <label>Notes:</label>
        <textarea name="failed_note">System was not working. Card processed manually.</textarea>
        <br class="clear">

        <label>&nbsp;</label>
        <input type="button" name="add_failed_transaction" value="Submit" class="button_small greenishBtn">
        <span class="error trans_failed_error"></span>
        <br class="clear">
    </div>

    
    <div class="hide block_credit">
        <label>Date:</label>
        <input type="text" name="credit_trans_date" class="datepicker_mod">
        <br class="clear">

        <label>Amount, $:</label>
        <input type="text" name="credit_trans_amount">
        <br class="clear">

        <label>Notes:</label>
        <textarea name="credit_note">System was not working. Card processed manually.</textarea>
        <br class="clear">

        <label>&nbsp;</label>
        <input type="button" name="add_credit_transaction" value="Submit" class="button_small greenishBtn">
        <span class="error trans_credit_error"></span>
        <br class="clear">
    </div>
</div>