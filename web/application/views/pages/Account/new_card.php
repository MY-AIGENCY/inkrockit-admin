<form method="POST">
    <div class="mainPage">
        <div class="content">

            <div class="printit_title_block" id="position1">
                <div class="title_checkout">My Account</div>

            </div>
            <div class="right block_rewards_points">
                <div class="block_redeem">
                    <input class="Reddem_Button" type="submit" name="" value="Redeem">
                    <div class="count_points">4,290</div>

                </div>
                <div class="title_pints">My Rewards Points</div>

                <br class="clear">
            </div>
            <br class="clear">
            <div class="block_content_page_account">
                <div class="left">
                    <ul class="nav_account">
                        <li><a <?php
                            if ($param == 'active_orders') {
                                echo ' class="current_account"';
                            } elseif ($param == 'history_orders') {
                                echo ' class="current_account"';
                            }
                            ?> href="/account/active_orders">My Orders</a></li>
                        <li><a <?php
                            if ($param == 'payment_info') {
                                echo ' class="current_account"';
                            } elseif ($param == 'new_card') {
                                echo ' class="current_account"';
                            }
                            ?> href="/account/payment_info">Payment information</a></li>
                        <li><a href="#">Shipping information</a></li>
                        <li><a href="#">My Rewards</a></li>
                        <li><a href="#">Account Settings</a></li>
                    </ul>
                </div>

                <div class="right pos_rel">
                    <div class="block_payment_info_new_card">
                        <div class="new_card_title_payment_info">
                            PAYMENT INFORMATION
                        </div>
                        <div class="new_card_title_biling_address">
                            BILLING ADDRESS
                        </div>
                        <div class="block_new_card_payment_info"> 
                            <div class="content_payment_info_new_card">

                                <div class="block_view_card_as">
                                    <img class="left marg_t5" src="/images/print_it/cwechin.png">
                                    <div class="bold_title_12 left marg_l6 marg_t6">View Card As</div>
                                    <input type="text" name="" class="small_input_view_card_as left marg_l6">
                                    <input type="checkbox" name="" class="left marg_t6 marg_l13 firefox_cwechin_icon">
                                    <div class="left f12 marg_t6 marg_l6">Make Default</div>
                                </div>
                                <br class="clear">
                                <div class="block_info_from_new_card">
                                    <div class="info_from_new_card">
                                        <table class="right marg_t13" style="padding-bottom: 10px;">
                                            <tr>
                                                <td class="right marg_t5 marg_r5">
                                                    Card Type
                                                </td>
                                                <td>
                                                    <select class="select_card_type">
                                                        <option>American Express</option>
                                                        <option>Master Card</option>
                                                        <option>Visa</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="right marg_t13 marg_r6">
                                                    <img src="/images/print_it/cwechin.png" class="left marg_r6"> 
                                                    Card Number
                                                </td>
                                                <td>
                                                    <input type="text" name="" class="card_number_my_account">
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="right marg_t13 marg_r6">
                                                    Full Name on Card
                                                </td>
                                                <td>
                                                    <input type="text" name="" class="card_number_my_account">
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="right marg_t13 marg_r6">
                                                    Expiration Date
                                                </td>
                                                <td>
                                                    <div class="expiration_date_my_account">
                                                        <select class="expiration_mount">
                                                            <option>01</option>
                                                            <option>02</option>
                                                            <option>03</option>
                                                            <option>04</option>
                                                            <option>05</option>
                                                            <option>06</option>
                                                            <option>07</option>
                                                            <option>08</option>
                                                            <option>09</option>
                                                            <option>10</option>
                                                            <option>11</option>
                                                            <option>12</option>
                                                        </select>

                                                        <select class="expiration_year">
                                                            <option>2015</option>
                                                            <option>2014</option>
                                                            <option>2013</option>
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="right marg_r5 marg_t13">
                                                    <img src="/images/print_it/cwechin.png" class="left marg_r7"> 
                                                    Security Code
                                                </td>
                                                <td>
                                                    <input type="text" name="" class="small_input_security_code"> 
                                                </td>
                                            </tr>


                                            <tr>
                                                <td class="right marg_t13 marg_r6">
                                                    Name if Not Cardholder
                                                </td>
                                                <td>
                                                    <input type="text" name="" class="card_number_my_account">
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>


                                <div class="left marg_t17 marg_l6">
                                    <img src="/images/print_it/icon_clod_mini.png" class="left">
                                    <div class="ink_protected">
                                        InkRockit uses SSL encryption to make sure your credit
                                        card information is well protected.
                                    </div>
                                </div>


                            </div>

                        </div>



                        <div class="block_new_card_billing_address"> 

                            <table class="billing_address_my_account">

                                <tr>
                                    <td>
                                        <div class="label_input_box_ship">First Name</div>
                                    </td>
                                    <td>
                                        <div style="" class="one_form_input"><input type="text" class="input_billing_address" name="company"></div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="label_input_box_ship">Last Name</div>
                                    </td>
                                    <td>
                                        <div style="" class="one_form_input"><input type="text" class="input_billing_address" name="company"></div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="label_input_box_ship">Company</div>
                                    </td>
                                    <td>
                                        <div style="" class="one_form_input"><input type="text" class="input_billing_address" name="company"></div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="label_input_box_ship">Address 1</div>
                                    </td>
                                    <td>
                                        <div style="" class="one_form_input"><input type="text" class="input_billing_address" name="company"></div>
                                    </td>
                                </tr>


                                <tr>
                                    <td>
                                        <div class="label_input_box_ship">Address 2</div>
                                    </td>
                                    <td>
                                        <div style="" class="one_form_input"><input type="text" class="input_billing_address" name="company"></div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="label_input_box_ship">City</div>
                                    </td>
                                    <td>
                                        <div style="" class="one_form_input"><input type="text" class="input_billing_address" name="company"></div>
                                    </td>
                                </tr>


                                <tr>
                                    <td>
                                        <div class="label_input_box_ship">State</div>
                                    </td>
                                    <td>
                                        <div style="" class="one_form_input">
                                            <select class="select_state_box_ship">
                                                <option></option>
                                                <option>1</option>
                                                <option>2</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="label_input_box_ship" style="margin-top: 7px;">ZIP Code</div>
                                    </td>
                                    <td>
                                        <div style="" class="one_form_input">
                                            <input type="text"class="small_input input_box_ship">
                                        </div>
                                    </td>
                                </tr>


                                <tr>
                                    <td>
                                        <div class="label_input_box_ship">Country</div>
                                    </td>
                                    <td>
                                        <div style="" class="one_form_input">
                                            <select class="big_select_my_account select_state_box_ship">
                                                <option>United States</option>
                                                <option>1</option>
                                                <option>2</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                        </div>
                        <div class="block_saved_info">
                            Saved information will show the last four digits
                            of the account number
                            and will not include the security code.
                        </div>
                        <div class="right marg_r15 marg_t15 firefox_bottom">
                            <input type="submit" class="bottom_save_new_card" name="save" value=" ">
                            <input type="submit" class="bottom_cansel_new_card" name="cansel" value=" ">
                        </div>
                    </div>
                    <br class="clear">
                    <div style="margin-bottom: 300px;float: left; width: 713px;"></div>
                </div>
            </div>
        </div>
    </div>
</form>