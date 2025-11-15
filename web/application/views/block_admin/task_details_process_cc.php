<ul class="form_fields_container" >
    <li style="margin-top: 0">
        <div class="form_input" style="width: 97%">

            <strong>Available Cards</strong>
            <?php
            $cards = array('Visa' => 'visa', 'MasterCard' => 'mastercard', 'American Express' => 'american_express', 'Diners Club' => 'diners_club', 'Discover' => 'discover', 'JCB' => 'jcb');

            if (!empty($current_job_id)) {
                ?>
                <a class="button_small whitishBtn add_credit_card" style="margin-left: 506px"><span class="iconsweet">+</span>Add payment method</a>
                <button data-id="1126" class="button_small whitishBtn add_check_trans">Add transaction</button>
                <br>    
                <table class="credit_card_list" width="930">
                    <tr>
                        <th width="40"></th>
                        <th>Available Card</th>
                        <th width="110">
                            Set as Default
                        </th>
                        <th width="60">
                            Action
                        </th>
                        <th width="40"></th>
                    </tr>
                    <?php
                    if (!empty($credit_cards)) {
                        $all_cards = array();
                        foreach ($credit_cards as $k => $v) {
                            if ($v['default'] != 0) {
                                $all_cards[0] = $v;
                            } else {
                                $all_cards[$k + 1] = $v;
                            }
                        }
                        ksort($all_cards);
                        foreach ($all_cards as $val) {
                            ?>
                            <tr>
                                <td style="padding: 4px">
                                    <?php
                                    if (!empty($val['title']) && !empty($cards[$val['title']])) {
                                        echo '<img src="/images/credit_cards/' . $cards[$val['title']] . '.png">';
                                    } else {
                                        echo '<img src="/images/credit_cards/card_default.png">';
                                    }
                                    ?>
                                </td>
                                <td data-id="<?= $val['id'] ?>" class="<?php
                                echo'show_card_details pointer';
                                ?>">
                                    <strong>
                                        <?php
                                        echo '<span class="view_as">' . $val['view_as'] . '</span><span> - ';
                                        if (!empty($val['title'])) {
                                            echo $val['title'] . ' - ';
                                        }
                                        echo substr($val['card_number'], -4);

                                        if (!empty($val['default'])) {
                                            echo ' (default)';
                                        }
                                        echo '</span>';
                                        ?>
                                    </strong><br>
                                    <?php
                                    if (!empty($used_jobs[$val['id']])) {
                                        foreach ($used_jobs[$val['id']] as $v) {
                                            echo $v . '; ';
                                        }
                                    }
                                    ?>
                                </td>
                                <td style="text-align: center; vertical-align: middle">
                                    <input type="radio" name="set_card_default" value="<?= $val['id'] ?>" <?php
                                    if (!empty($val['default'])) {
                                        echo 'checked="checked"';
                                    }
                                    ?>>
                                </td>
                                <td >
                                    <button data-id="<?= $val['id'] ?>" class="button_small greenishBtn card_process">Process</button>
                                </td>
                                <td>
                                    <span data-id="<?= $val['id'] ?>" class="ui-icon ui-icon-closethick card_delete marg_t5"></span>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?><tr class="no_cards">
                            <td colspan="5"><em>*No Credit Cards</em></td>
                        </tr><?php
                    }
                    ?>
                </table>




                <?php
            } else {
                //TAB: All

                if (!empty($credit_cards)) {
                    $all_cards = array();
                    foreach ($credit_cards as $k => $v) {
                        $all_cards[$v['first_name'] . ' ' . $v['last_name']][] = $v;
                    }
                    ?><table width="600">
                        <tr>
                            <th>Username</th>
                            <th>Cards</th>
                        </tr>   
                        <?php
                        foreach ($all_cards as $key => $val) {
                            ?>
                            <tr class="pointer show_user_cards">
                                <td><?= $key ?></td>
                                <td><?= count($val) ?></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="hide all_user_cards">
                                    <?php
                                    foreach ($val as $v) {
                                        echo '<div>';
                                        if (!empty($v['title']) && !empty($cards[$v['title']])) {
                                            echo '<img src="/images/credit_cards/' . $cards[$v['title']] . '.png" class="left">';
                                        } else {
                                            echo '<img src="/images/credit_cards/card_default.png"  class="left">';
                                        }
                                        $num = substr(str_replace(' ', '', $v['card_number']), -4);
                                        echo '<span class="lh40">' . $v['view_as'] . ' - X' . $num . '</span>';
                                        echo '</div><br class="clear">';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?></table><?php
                } else {
                    ?><br><br><em>*No Credit Cards</em><?php
                    }
                }
                ?>



            <br class="clear">


            <div class="card_edit_form hide">
                <form name="card_info_procc">
                    <h3 class="form_title"></h3>

                    <table width="96%">
                        <tr>
                            <td class="left_col"><strong>Billing Information</strong></td>
                            <td><strong>Credit or debit card</strong></td>
                        </tr>
                        <tr>
                            <td class="left_col">

                                <label>Auto fill with:</label>
                                <div class="select_elems opened left">
                                    <div class="sel_selected">
                                        <div class="sel_arrows">
                                        </div>
                                    </div>
                                    <ul class="sel_list billing_selector">
                                    </ul>
                                </div>
                                <br class="clear">

                                <a class="clear_billing right marg_r10 marg_t5">Clear</a>
                                <br class="clear"><br>

                                <label>Company</label> <input type="text" name="billing_company" value="" class="medium"><br class="clear">
                                <label>Name</label> <input type="text" name="billing_fname" value="" style="width: 115px" placeholder="First Name"> 
                                <input type="text" name="billing_lname" value="" style="width: 120px" placeholder="Last Name"><br class="clear">

                                <label>Street address</label> 
                                <input type="text" name="billing_address" value="" style="width: 120px">
                                <input type="text" name="billing_suite" value="" style="width: 115px" placeholder="Apt./Suite">
                                <br class="clear">                                                
                                <label>Address line 2</label> 
                                <input type="text" name="billing_address2" value="" class="medium">
                                <br class="clear">

                                <label>City</label> <input type="text" name="billing_city" value="" class="medium"><br class="clear">
                                <label>State</label> <input type="text" name="billing_state" value="" class="medium"><br class="clear">
                                <label>Zip Code</label> <input type="text" name="billing_zip" value="" class="medium"><br class="clear">
                                <label>Country</label> <input type="text" name="billing_country" value="" class="medium"><br class="clear">
                                <hr class="dotted">
                                <label>Email</label> <input type="text" name="billing_email" value="" class="medium"><br class="clear">
                                <label>Phone</label> <input type="text" class="phone" name="billing_phone" value="" style="width: 180px">
                                <input type="text" name="billing_phone_ext" value="" class="number" style="width: 54px" placeholder="ext.">
                                <br class="clear"><br>
                                <label>&nbsp;</label>
                                <br class="clear">
                                <span class="right billing_notif green"></span>
                            </td>
                            <td class="right_col">
                                <div class="credit_card_info">
                                    <label>Payment Method</label>
                                    <div class="left">
                                        <input type="radio" name="pay_type" value="card" checked="checked"> Credit Card<br>
                                    </div><br class="clear">

                                    <label style="line-height: 12px">Full name as it appears on the Card</label> 
                                    <input type="text" name="full_card_name" value="" class="medium" readonly="true" style="width: 230px; background: #DDD">
                                    <span class="tooltip"><img src="/images/quest.png"><span class="hide" style="display: none;">This field generated automatically from the data at the fields "First Name" and "Last Name" which you can find on the right hand side at Billing Address form.</span></span>

                                    <br class="clear">

                                    <label>Credit Card Number</label> 
                                    <input type="text" name="card_number" value="" class="medium">

                                    <br class="clear">
                                    <label>Credit Card Type</label> <input type="text" name="card_description" readonly="readonly" value="" class="medium"><br class="clear">
                                    <label>Expiration Date</label>
                                    <input type="text" name="cc_date" placeholder="mm/yy" class="credit_date short" style="width:100px"><br class="clear">
                                    <label>Security Code</label>
                                    <input type="text" name="cc_ccv" placeholder="CCV" maxlength="4" class="short"><br class="clear">


                                    <label style="line-height: 12px">Your full name (if you are not cardholder)</label>
                                    <input type="text" name="full_user_name" class="medium">
                                    <br class="clear">

                                    <label>&nbsp;</label>
                                    <input type="checkbox" name="set_default"> Set as default Payment Method. 
                                    <br class="clear"><br>

                                    <div class="hide save_billing_changes">
                                        <b class="lh22">For to proceed with adding the credit card please select method for storing the billing details from the form:</b><br>
                                        <div class="text-right">
                                            <a class="pointer bill_save_add">Save as new Billing Address template</a> 
                                            <span class="tooltip"><img src="/images/quest.png"><span class="hide">If you use this option to store the billing details data from the Billing Address form, system will store data as a template and you'll be able to use it in future. You can access to these details from "Auto fill with" dropdown menu.</span></span><br>
                                            <a class="pointer bill_save_update">Update current Billing Address template</a> 
                                            <span class="tooltip"><img src="/images/quest.png"><span class="hide">Using this option you'll update currently selected template.</span></span><br>
                                            <a class="pointer bill_save_continue">Continue without saving a template</a> 
                                            <span class="tooltip"><img src="/images/quest.png"><span class="hide">Use this option if you do not have full Billing Details of the card. System will store the Credit Card with the Billing details you put into the form but it would not make template for the "Auto fill with" dropdown menu.</span></span><br>
                                        </div>
                                    </div>
                                    <br>

                                    <div class="right" id="save_changed_info" style="margin-top: 5px">
                                        <span class="dblueBtn button_small save_changed_info hide">Save Changes</span>
                                    </div>

                                    <span class="button_small whitishBtn cancel_edit_card right">Close form</span>
                                    <br class="clear"><br>
                                    <span class="card_err left marg_l20"></span>
                                </div>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>

            <div class="card_add_form hide">
                <form name="card_info">
                    <h3 class="form_title"></h3>

                    <table width="96%">
                        <tr>
                            <td class="left_col"><strong>Billing Address</strong> </td>
                            <td class="right_col"><strong>Credit or debit card</strong></td>
                        </tr>
                        <tr>
                            <td class="left_col">

                                <label>Auto fill with:</label>
                                <div class="select_elems opened left">
                                    <div class="sel_selected">
                                        <div class="sel_arrows">
                                        </div>
                                    </div>
                                    <ul class="sel_list billing_selector">
                                    </ul>
                                </div>
                                <br class="clear">

                                <div class="same_as_shipping_block left">
                                    <label>&nbsp;</label>
                                    <!--<input type="checkbox" name="same_as_shipping"> Same as shipping-->
                                </div>
                                <a class="clear_billing right marg_r10 marg_t5">Clear</a>
                                <br class="clear">
                                <label>Company</label> <input type="text" name="billing_company" value="" class="medium"><br class="clear">
                                <label>Name</label> <input type="text" name="billing_fname" value="" placeholder="First Name" style="width: 115px">
                                <input type="text" name="billing_lname" value="" class="medium" placeholder="Last Name" style="width: 120px"><br class="clear">

                                <label>Street address</label> 
                                <input type="text" name="billing_address" value="" style="width: 120px">
                                <input type="text" name="billing_suite" value="" style="width: 115px" placeholder="Apt./Suite">
                                <br class="clear">                                                
                                <label>Address line 2</label> 
                                <input type="text" name="billing_address2" value="" class="medium">
                                <br class="clear">
                                <label>City</label> <input type="text" name="billing_city" value="" class="medium"><br class="clear">
                                <label>State</label> <input type="text" name="billing_state" value="" class="medium"><br class="clear">
                                <label>Zip Code</label> <input type="text" name="billing_zip" value="" class="medium"><br class="clear">
                                <label>Country</label> <input type="text" name="billing_country" value="" class="medium"><br class="clear">
                                <hr class="dotted">
                                <label>Email</label> <input type="text" name="billing_email" value="" class="medium"><br class="clear">
                                <label>Phone</label> <input type="text" name="billing_phone" class="phone" value="" style="width: 180px">
                                <input type="text" name="billing_phone_ext" value="" style="width: 54px" placeholder="ext.">
                                <br class="clear">
                                <span class="right billing_notif green"></span>
                            </td>
                            <td class="right_col">

                                <div class="credit_card_info">
                                    <label style="line-height: 12px">Full name as it appears on the Card</label> 
                                    <input type="text" name="full_card_name" value="" class="medium" readonly="true" style="width: 230px; background: #DDD">
                                    <span class="tooltip"><img src="/images/quest.png"><span class="hide" style="display: none;">This field generated automatically from the data at the fields "First Name" and "Last Name" which you can find on the right hand side at Billing Address form.</span></span>

                                    <br class="clear">

                                    <label>Credit Card Number</label> 
                                    <input type="text" name="card_number" value="" class="medium">

                                    <br class="clear">
                                    <label>Card Type</label> <input type="text" name="card_description" readonly="true" value="" class="medium"><br class="clear">
                                    <label>Expiration Date</label>
                                    <input type="text" name="cc_date" placeholder="mm/yy" class="short credit_date" style="width:100px"><br class="clear">
                                    <label>Security Code</label>
                                    <input type="text" name="cc_ccv" placeholder="CCV" maxlength="4" class="short"><br class="clear">

                                    <label style="line-height: 12px">Your full name (if you are not cardholder)</label>
                                    <input type="text" name="full_user_name" class="medium">
                                    <br class="clear">
                                    <label>&nbsp;</label>
                                    <input type="checkbox" name="set_default"> Set as default Payment Method. 

                                    <br class="clear">
                                    <label style="line-height: 12px">View as</label>
                                    <input type="text" name="view_as" class="medium" value="<?= $user['first_name'] . ' ' . $user['last_name'] ?>">
                                    <br class="clear">
                                    <br>
                                    <div class="hide save_billing_changes">
                                        <b class="lh22">For to proceed with adding the credit card please select method for storing the billing details from the form:</b><br>
                                        <div class="text-right">
                                            <a class="pointer bill_save_add">Save as new Billing Address template</a> 
                                            <span class="tooltip"><img src="/images/quest.png"><span class="hide">If you use this option to store the billing details data from the Billing Address form, system will store data as a template and you'll be able to use it in future. You can access to these details from "Auto fill with" dropdown menu.</span></span><br>
                                            <a class="pointer bill_save_update">Update current Billing Address template</a> 
                                            <span class="tooltip"><img src="/images/quest.png"><span class="hide">Using this option you'll update currently selected template.</span></span><br>
                                            <a class="pointer bill_not_save_continue">Continue without saving a template</a> 
                                            <span class="tooltip"><img src="/images/quest.png"><span class="hide">Use this option if you do not have full Billing Details of the card. System will store the Credit Card with the Billing details you put into the form but it would not make template for the "Auto fill with" dropdown menu.</span></span><br>
                                        </div>
                                    </div>
                                    <br>

                                    <span class="button_small dblueBtn save_card right marg_l20 marg_r10">Add Card</span>
                                    <span class="button_small whitishBtn cancel_save_card right">Close form</span><br class="clear">
                                    <div class="card_err"></div>
                                </div> 
                            </td>
                        </tr>
                    </table>
                </form>
            </div>

            <div class="card_process_form hide">
                <form name="card_info_procc">
                    <div class="right">
                        <label>Job#</label>
                        <select name="payment_job_id" class="hide">
                            <?php
                            $edg = 0;
                            if (!empty($job_id)) {
                                foreach ($job_id as $val) {
                                    $current_id = (empty($val['job_id'])) ? $val['estimate_id'] : $val['job_id'];
                                    ?><option value="<?= $val['id'] ?>" <?php
                                    if (!empty($current_job_id) && $current_job_id == $val['id']) {
                                        $job_curr = $current_id;
                                        $job_orders_count = $val['order_counts'];
                                        $current_amount = $val['order_total'];
                                        echo'selected="selected"';
                                        if (!empty($val['edg'])) {
                                            $edg = 1;
                                        }
                                    }
                                    ?>><?= $current_id ?></option><?php
                                        }
                                    }
                                    ?>
                        </select>
                        <span style="line-height: 30px">
                            <?php
                            if (!empty($job_curr)) {
                                echo $job_curr . ', transaction ' . ($job_orders_count + 1);
                            }
                            ?>
                        </span>
                        <br class="clear">
                    </div>

                    <h3 class="form_title"></h3>
                    <div class="payment_fields">
                        <table width="100%">
                            <tr>
                                <td class="left_col"><strong style="color: red; font-size: 15px">Shipping Address</strong> </td>
                                <td class="right_col"><strong>Credit or debit card</strong></td>
                            </tr>
                            <tr>
                                <td class="left_col">
                                    <label>Autofill with:</label>
                                    <div class="select_elems opened left">
                                        <div class="sel_selected">
                                            <div class="sel_arrows">
                                            </div>
                                        </div>
                                        <ul class="sel_list shipping_selector">
                                        </ul>
                                    </div>
                                    <br class="clear">
                                    <a class="clear_shipping right marg_r10 marg_t5">Clear</a><br>
                                    <br class="clear">
                                    <div class="shipp_info">
                                        <label>Company</label> <input type="text" name="shipping_company" value="" class="medium"><br class="clear">
                                        <label>Name</label> <input type="text" name="shipping_fname" value="" placeholder="First Name" style="width: 115px">
                                        <input type="text" name="shipping_lname" value="" style="width: 120px" placeholder="Last Name"><br class="clear">
                                        <label>Street address</label> 
                                        <input type="text" name="shipping_address" value="" class="medium" style="width: 115px">
                                        <input type="text" name="shipping_suite" value="" class="medium" placeholder="Apt./Suite" style="width: 120px">
                                        <br class="clear">
                                        <label>Address 2</label> <input type="text" name="shipping_address2" value="" class="medium"><br class="clear">
                                        <label>City</label> <input type="text" name="shipping_city" value="" class="medium"><br class="clear">
                                        <label>State</label> <input type="text" name="shipping_state" value="" class="medium"><br class="clear">
                                        <label>Zip Code</label> <input type="text" name="shipping_zip" value="" class="medium"><br class="clear">
                                        <label>Country</label> <input type="text" name="shipping_country" value="" class="medium"><br class="clear">

                                        <label>Phone</label> <input type="text" name="shipping_phone" value="" class="medium phone" style="width: 180px">
                                        <input type="text" name="shipping_phone_ext" value="" class="number" style="width: 54px" placeholder="ext.">
                                        <br class="clear">
                                        <label>Email</label> <input type="text" name="shipping_email" value="" class="medium"><br class="clear">

                                        <span class="marg_l20 save_shipping hide dblueBtn button_small right">Save Shipping Info</span>
                                        <br class="clear">
                                        <hr class="dotted">
                                        <a class="right shipp_update_contact hide">Update Contact Info.</a>
                                        <a class="marg_r10 right shipp_add_contact hide">Add New Shipping Dest.</a>
                                        <br class="clear">
                                    </div>
                                    <br class="clear">
                                </td>
                                <td class="right_col">
                                    <div>
                                        <label>Order Total, $</label>
                                        <?php if (!empty($job_orders_count)) { ?>
                                            <input type="hidden" name="transaction_num" value="<?= $job_orders_count + 1 ?>">
                                            <?php
                                        }
                                        if (!empty($current_amount)) {
                                            $current_amount = floatval($current_amount);
                                            echo '<input type="hidden" name="order_total" readonly="readonly" value="' . $current_amount . '"> <div style="line-height: 34px; float: left">' . number_format($current_amount, 2) . ' <span class="pointer edit_order_total" style="margin-left: 10px">edit</span></div>';
                                        } else {
                                            echo '<input type="text" style="width:140px" style="width:100px" name="order_total">';
                                        }
                                        ?>
                                        <br class="clear">
                                        <label>EDG?</label>
                                        <div style="margin-top: 4px">
                                            <input type="radio" <?php if (!empty($edg)) echo'checked="checked"' ?> name="edg" value="1"> Yes
                                            <input type="radio" name="edg" value="0" class="marg_l10"> No
                                        </div>
                                        <br class="clear">
                                        <label>Payment type</label>

                                        <span><input type="radio" name="payment_type" value="50">50%</span>
                                        <span class="marg_l5"><input type="radio" name="payment_type" value="30">30%</span>
                                        <span class="marg_l5"><input type="radio" name="payment_type" value="20">20%</span>
                                        <span class="marg_l5"><input type="radio" name="payment_type" value="100">Balance</span>
                                        <span class="marg_l5"><input type="radio" name="payment_type" value="0">none</span>


                                        <br class="clear">

                                        <div class="credit_payments hide">
                                            <label>Credit Payment:</label>
                                            <select name="credit_payment_id" style="width: 150px">
                                                <?php
                                                if (!empty($payment_history)) {
                                                    foreach ($payment_history as $val) {
                                                        if ($val['type'] != 'credit') {
                                                            ?><option value="<?= $val['id'] ?>"><?=
                                                                $val['job_id'] . ': ';
                                                                if (empty($val['description'])) {
                                                                    echo $val['date'];
                                                                } else {
                                                                    echo $val['description'];
                                                                }
                                                                ?></option><?php
                                                        }
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <br class="clear">
                                        </div>

                                        <label>Charge AMT, $</label>
                                        <input type="text" name="charge" class="short number" style="width:140px">
                                        <br class="clear">
                                        <label>Payment Description</label>
                                        <textarea name="description" style="min-width: 60%;width: 60%"></textarea>
                                        <br class="clear"><br>
                                        <label>&nbsp;</label>
                                        <span class="button_small whitishBtn cancel_process_card">Close form</span>
                                        <span class="button_small dblueBtn process_card left">Process</span>
                                        <br class="clear">
                                        <div class="card_err error marg_t10"></div>


                                        <div class="marg_t10">
                                            <?php
                                            if (!empty($payment_history)) {
                                                ?>
                                                <table width="100%" class="table_bordered"><?php
                                                    $paid = $credit = 0;
                                                    $revers_history = array_reverse($payment_history);
                                                    foreach ($revers_history as $key => $val) {
                                                        if ($val['removed'] == 0) {
                                                            if ($val['type'] == 'credit') {
                                                                $credit += $val['summ'];
                                                            } elseif ($val['type'] != 'change_total' && $val['type'] != 'order_confirmed' && $val['type'] != 'failed') {
                                                                $paid += $val['summ'];
                                                            }
                                                        }
                                                    }
                                                    ?></table><?php
                                            }
                                            if (!empty($current_amount)) {
                                                $paids = (empty($paid)) ? 0 : $paid;
                                                $current_amount = (float) str_replace(',', '', $current_amount);
                                                $balance = $current_amount - $paids + @$credit;
                                                ?>
                                                <br>
                                                <table style="font-weight: bold" class="right total_table">
                                                    <tr>
                                                        <td>Order total:</td>
                                                        <td>$<?=
                                                            number_format($current_amount, 2);
                                                            ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total PAID:</td>
                                                        <td>$<?= number_format(@$paid, 2) ?></td>
                                                    </tr>
                                                    <?php if (!empty($credit)) { ?>
                                                        <tr>
                                                            <td>Credit:</td>
                                                            <td>-$<?= number_format(@$credit, 2) ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                    <tr <?php if ($balance < 0) echo 'style="background: #ffaaaa"' ?>>
                                                        <td>Balance:</td>
                                                        <td><?php
                                                            if ($balance < 0) {
                                                                echo '- $' . number_format($balance * (-1), 2);
                                                            } else {
                                                                echo '$' . number_format($balance, 2);
                                                            }
                                                            ?>
                                                            <input type="hidden" name="balance_user" value="<?= $balance ?>">
                                                        </td>
                                                    </tr>
                                                </table>
                                                <br class="clear">
                                            <?php } ?>
                                        </div>

                                    </div>
                                </td>
                            </tr>
                        </table>

                        <br class="clear">
                    </div>
                </form>
            </div>
            <br class="clear">
        </div>
    </li>



    <li>
        <div class="form_input" style="width: 1000px; float: left">
            <?php
            if (!empty($payment_history)) {

                if (!empty($current_job_id)) {
                    //ONE JOB
                    $cookie = new Cookie();
                    $admin = $cookie->get('admin_user');
                    if (!empty($admin)) {
                        $admin_user = unserialize($admin);
                    }


                    ob_start();
                    $trans = 1;
                    $deposit_exists = $balance = $last_balance = 0;

                    $payed = $job_id[$current_job_id]['payments'];

                    foreach ($payment_history as $key => $val) {
                        include DOCROOT . 'application/views/block_admin/one_payment.php';
                    }
                    $content = ob_get_contents();
                    ob_end_clean();
                    ?>

                    <h3><?= $job_id[$current_job_id]['job_id'] . ' - '; ?> Transaction History
                        <div class="right">
                            <b class="right_info">
                                <span class="marg_l10 f18">Balance: <?= ($balance >= 0) ? '$' . number_format($balance, 2, '.', ',') : '-$' . number_format(abs($balance), 2, '.', ','); ?></span>
                                <span class="marg_l10 f18">Payments: <?= ($payed >= 0) ? '$' . number_format($payed, 2, '.', ',') : '-$' . number_format(abs($payed), 2, '.', ','); ?></span>
                                <span class="marg_l10 f18">Order total: $<?= number_format($last_balance, 2, '.', ',') ?></span>
                            </b>
                        </div>
                    </h3>

                    <br class="clear">

                    <table width="1000" class="payment_history">
                        <tr class="title">
                            <td style="text-align: left">Transaction Amount</td>
                            <td>Amount</td>
                            <td width="250">Date/Time</td>
                            <td>Event/Amount</td>
                            <td width='40'>Type</td>
                            <td width='100'>Balance</td>
                            <td width='110'></td>
                        </tr>
                        <?php
                        echo $content;
                        ?>
                    </table>

                    <?php
                } else {
                    $total_balance = $total_payments = $total_order = 0;
                    ?><h3>Transaction History

                        <div class="right">
                            <b class="right_info">
                                <span class="marg_l10 f18">Balance: <span class="get_total_balance"></span></span>
                                <span class="marg_l10 f18">Payments: <span class="get_total_payments"></span></span>
                                <span class="marg_l10 f18">Order total: <span class="get_total_order"></span></span>
                            </b>
                        </div>

                    </h3><?php
                    //ALL JOBs
                    foreach ($payment_history as $v) {
                        $payment_history_sort[$v['job_id']][] = $v;
                    }
                    $sorted = array();
                    foreach ($payment_history_sort as $key => $val) {
                        if (!empty($key)) {
                            $key_tmp = $key;
                            $k = explode('-', $key);
                            $key = preg_replace('/[^0-9]*/', '', $k[1]);
                            $sorted[$key][$key_tmp] = $val;
                        }
                    }
                    ksort($sorted);
                    $sorted = array_reverse($sorted);

                    foreach ($sorted as $one_sorted) {
                        foreach ($one_sorted as $job => $vals) {
                            ob_start();
                            $trans = 1;
                            $deposit_exists = $balance = $last_balance = $payed = 0;
                            foreach ($vals as $val) {
                                include DOCROOT . 'application/views/block_admin/one_payment.php';
                            }
                            $content = ob_get_contents();
                            ob_end_clean();
                            ?>
                            <div class="slide_payments pointer">
                                <b><?= $job ?></b>
                                <b class="right_info">
                                    <span class="w100">Balance: <?php
                                        $bal = ($balance >= 0) ? '$' . number_format($balance, 2, '.', ',') : '-$' . number_format(abs($balance), 2, '.', ',');
                                        echo $bal;
                                        $total_balance += $balance;
                                        ?></span>
                                    <span class="w100">Payments: <?php
                                        $paym = ($payed >= 0) ? '$' . number_format($payed, 2, '.', ',') : '-$' . number_format(abs($payed), 2, '.', ',');
                                        echo $paym;
                                        $total_payments+=$payed;
                                        ?></span>
                                    <span class="green w100">Order total: $<?php
                                        $total = number_format($last_balance, 2, '.', ',');
                                        echo $total;
                                        $total_order+=$last_balance;
                                        ?></span>
                                </b>
                            </div>

                            <div style="display: none">
                                <table width="100%" class="payment_history">
                                    <tr class="title">
                                        <td style="text-align: left">Transaction Amount</td>
                                        <td>Amount</td>
                                        <td width="250">Date/Time</td>
                                        <td>Event/Amount</td>
                                        <td width='40'>Type</td>
                                        <td width='100'>Balance</td>
                                        <td width='110'></td>
                                    </tr>
                                    <?= $content ?>   
                                </table>

                            </div>
                            <?php
                        }
                    }
                    ?>
                    <div class="hide">
                        <span class="set_total_balance"><?= number_format($total_balance, 2, '.', ',') ?></span>
                        <span class="set_total_payments"><?= number_format($total_payments, 2, '.', ',') ?></span>
                        <span class="set_total_order"><?= number_format($total_order, 2, '.', ',') ?></span>
                        <script>
                            $(function() {
                                $('.get_total_balance').text($('.set_total_balance').text());
                                $('.get_total_payments').text($('.set_total_payments').text());
                                $('.get_total_order').text($('.set_total_order').text());
                            });
                        </script>

                    </div><?php
                }
            } else {
                ?><em>*No transactions</em><?php
            }
            ?>
        </div>

    </li>
</ul>