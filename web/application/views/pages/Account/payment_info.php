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
                            }
                            ?> href="/account/payment_info">Payment information</a></li>
                        <li><a href="#">Shipping information</a></li>
                        <li><a href="#">My Rewards</a></li>
                        <li><a href="#">Account Settings</a></li>
                    </ul>
                </div>

                <div class="right pos_rel">

                    <div class="edit_my_ship_adress">
                        <div class="close_block_new_ship">
                            <img src="/images/print_it/close_box_new_adress.png" style="margin-top: 9px;">
                        </div>
                        <div class="title_box_ship">
                            Edit > SHIP TO Address
                        </div>
                        <div class="content_box_ship">
                            <div class="check_to_my_account_no">
                                <!--<input type="checkbox" class="add_my_acaunt" style="margin-right: 6px;">-->
                                <!--Add this Ship To Address to My Account.-->
                                <div class="view_as_edit">
                                    <table>
                                        <tr>
                                            <td>
                                                <div style="margin-top: 6px;"><img width="14" height="15" src="/images/print_it/cwechin.png" style="margin-top: -1px; position: relative; padding-right: 2px;"> View as</div>
                                            </td>
                                            <td>
                                                <div style="margin-top: 3px" class="my_acount_slide_down"> <input type="text" class="input_box_ship" name=""></div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <br class="clear">
                            </div>  


                            <table class="form_input_box_ship">
                                <tr>
                                    <td>
                                        <div class="label_input_box_ship">Company</div>
                                    </td>
                                    <td>
                                        <div style="" class="one_form_input"><input type="text" class="input_box_ship" name="company"></div>
                                    </td>
                                </tr>


                                <tr>
                                    <td>
                                        <div class="label_input_box_ship">First Name</div>
                                    </td>
                                    <td>
                                        <div style="" class="one_form_input"><input type="text" class="input_box_ship" name="company"></div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="label_input_box_ship">Last Name</div>
                                    </td>
                                    <td>
                                        <div style="" class="one_form_input"><input type="text" class="input_box_ship" name="company"></div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="label_input_box_ship">Address 1</div>
                                    </td>
                                    <td>
                                        <div style="" class="one_form_input"><input type="text" class="input_box_ship" name="company"></div>
                                    </td>
                                </tr>


                                <tr>
                                    <td>
                                        <div class="label_input_box_ship">Address 2</div>
                                    </td>
                                    <td>
                                        <div style="" class="one_form_input"><input type="text" class="input_box_ship" name="company"></div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="label_input_box_ship">City</div>
                                    </td>
                                    <td>
                                        <div style="" class="one_form_input"><input type="text" class="input_box_ship" name="company"></div>
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
                                            <select class="big_select select_state_box_ship">
                                                <option>United States</option>
                                                <option>1</option>
                                                <option>2</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>


                                <tr>
                                    <td>
                                        <div class="label_input_box_ship" style="margin-top: 7px;">Phone 1</div>
                                    </td>
                                    <td>
                                        <div style="" class="one_form_input">

                                            <input type="text"class="small_input input_box_ship">
                                            <div style="margin-left: 138px; margin-top: -20px;">
                                                <span style="margin-left: -2px;">Ext</span>
                                                <input type="text"class="small_input_ext">
                                            </div>
                                        </div>
                                    </td>



                                </tr>

                                <tr>
                                    <td>
                                        <div class="label_input_box_ship">Phone 2</div>
                                    </td>
                                    <td>
                                        <div style="" class="one_form_input">
                                            <input type="text"class="small_input input_box_ship">
                                            <div style="margin-left: 138px; margin-top: -21px;">
                                                <span style="margin-left: -2px;">Ext</span>
                                                <input type="text"class="small_input_ext">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <div class="block_apply_cancel">
                                <a href="" class="cancel_buttom">Cancel</a>
                                <input class="Apply_Button" type="submit" value="Apply">
                            </div>

                            <div class="text_intertnational_shipping">
                                <b style="color: #000000; font-size: 12px;">INTERNATIONAL SHIPPING:</b> Please call us at <br>1-800-900-5632.<br><br>

                                Go to your <a href="" class="read_more_full_terms">My Account</a> page to manage all of your shipping adresses.
                            </div>

                        </div>
                    </div>







                    <div class="right add_new_card"><a href="/account/new_card" class="edit_pocket_folders">Add New Card</a></div>
                    <br class="clear">
                    <div class="block_payment_info_my_acconunt">
                        <table class="left marg_t4" style="width: 713px; margin-left: 4px;">
                            <tr style="border-bottom: 2px solid #7d7d7d;">
                                <td class="title_credit_card_td">
                                    CREDIT CARD
                                </td>

                                <td class="title_payment_info_td">
                                    PAYMENT INFORMATION
                                </td>

                                <td class="title_biling_adress">
                                    BILLING ADDRESS
                                </td>

                                <td class="action_paymnt_info">

                                </td>
                            </tr>
                            <tr style="border-bottom: 4px solid #7d7d7d;">
                                <td class="content_credit_card">
                                    <div>
                                        <p class="bold_title_14 marg_l10 marg_t8 left">AmEx 7890</p>
                                        <p class="left marg_l10 marg_t5">Default for Payments</p> 
                                    </div>
                                </td>



                                <td class="content_payment_info">
                                    <div>
                                        <p class="marg_l10 marg_t8 left">
                                            American Express<br>
                                            ******7890<br>
                                            Exp. Date: 01/2015<br>

                                        <p class="marg_t6 marg_l10 left">John Smith</p>
                                        </p>
                                    </div>
                                </td>


                                <td class="content_biling_adress">
                                    <div>
                                        <p class="marg_l10 marg_t8 left">
                                            John Smith<br>
                                            ABC Corporation<br>
                                            1234 Industrial Park Blvd<br>
                                            Suite 100<br>
                                            Orlando, Florida  12345<br>
                                            United States
                                        </p>
                                    </div>
                                </td>
                                <td class="content_action_edit_delete">
                                    <div>
                                        <div class="right marg_r15 marg_t10">
                                            <a href="#" class="edit_pocket_folders marg_r20 edit_my_ship_adress_submit">Edit</a>
                                            <a href="#" class="edit_pocket_folders">Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr style="border-bottom: 4px solid #7d7d7d;">
                                <td class="content_credit_card">
                                    <div>
                                        <p class="bold_title_14 marg_l10 marg_t8 left">Visa 2465</p>
                                        <!--<p class="left marg_l10 marg_t5">Default for Payments</p>--> 
                                    </div>
                                </td>



                                <td class="content_payment_info">
                                    <div>
                                        <p class="marg_l10 marg_t8 left">
                                            Visa<br>
                                            ******2465<br>
                                            Exp. Date: 10/2017<br>

                                        <p class="marg_t6 marg_l10 left">John Smith</p>
                                        </p>
                                    </div>
                                </td>


                                <td class="content_biling_adress">
                                    <div>
                                        <p class="marg_l10 marg_t8 left">
                                            John Smith<br>
                                            ABC Corporation<br>
                                            1234 Industrial Park Blvd<br>
                                            Suite 100<br>
                                            Orlando, Florida  12345<br>
                                            United States
                                        </p>
                                    </div>
                                </td>
                                <td class="content_action_edit_delete">
                                    <div>
                                        <div class="right marg_r15 marg_t10">
                                            <a  class="edit_pocket_folders marg_r20 edit_my_ship_adress_submit">Edit</a>
                                            <a href="#" class="edit_pocket_folders">Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td class="content_credit_card">
                                    <div>
                                        <p class="bold_title_14 marg_l10 marg_t8 left">Visa 7862</p>
                                        <!--<p class="left marg_l10 marg_t5">Default for Payments</p>--> 
                                    </div>
                                </td>



                                <td class="content_payment_info">
                                    <div>
                                        <p class="marg_l10 marg_t8 left">
                                            Visa<br>
                                            ******7862<br>
                                            Exp. Date: 11/2017<br>

                                        <p class="marg_t6 marg_l10 left">John Smith</p>
                                        </p>
                                    </div>
                                </td>


                                <td class="content_biling_adress">
                                    <div>
                                        <p class="marg_l10 marg_t8 left">
                                            John Smith<br>
                                            ABC Corporation<br>
                                            1234 Industrial Park Blvd<br>
                                            Suite 100<br>
                                            Orlando, Florida  12345<br>
                                            United States
                                        </p>
                                    </div>
                                </td>
                                <td class="content_action_edit_delete">
                                    <div>
                                        <div class="right marg_r15 marg_t10">
                                            <a href="#" class="edit_pocket_folders marg_r20 edit_my_ship_adress_submit">Edit</a>
                                            <a href="#" class="edit_pocket_folders">Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <br class="clear">
                    <div style="margin-bottom: 290px;float: left; width: 713px;"></div>
                </div>
            </div>
        </div>
    </div>
</form>