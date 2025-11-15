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



                    <div class="edit_order_account_box">
                        <div class="close_block_new_ship">
                            <img src="/images/print_it/close_box_new_adress.png" style="margin-top: 9px;">
                        </div>
                        <div class="title_box_ship">
                            New SHIP TO Address
                        </div>
                        <div class="content_box_ship">
                            <div class="check_to_my_account">
                                <input type="checkbox" class="add_my_acaunt" style="margin-right: 6px;">
                                Add this Ship To Address to My Account.
                                <div class="view_as">
                                    <table>
                                        <tr>
                                            <td>
                                                <div style="margin-top: 18px;"><img width="14" height="15" src="/images/print_it/cwechin.png" style="margin-top: -1px; position: relative; padding-right: 2px;"> View as</div>
                                            </td>
                                            <td>
                                                <div style="margin-top: 14px" class="my_acount_slide_down"> <input type="text" class="input_box_ship" name=""></div>
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


















                    <div class="block_my_account_order">
                        <a href="/account/active_orders">
                            <div class="active_order_active">
                                <span class="title_tabs_active">ACTIVE ORDERS</span>
                            </div>
                        </a>
                        <a href="/account/history_orders">
                            <div class="history_information">
                                <span class="title_tabs_history">ORDER HISTORY</span>
                            </div>
                        </a>
                        <div class="line_bottom_tabs_account"></div>

                        <!--                        <div class="active_order">
                                                    <span class="title_tabs_active">ACTIVE ORDERS</span>
                                                </div>
                                                <div class="history_information_active">
                                                    <span class="title_tabs_history">ORDER HISTORY</span>
                                                </div>
                                                <div class="line_bottom_tabs_account"></div>-->

                        <br class="clear">
                        <?php if (empty($_GET['datails_active'])) { ?>
                            <div class="marg_t17 left">
                                <div class="marg_l12 left bold_title_14">Order # ITA-004</div>
                                <div class="marg_l44 left marg_t3">
                                    <a href="?datails_active=1" class="edit_pocket_folders">Order Details</a>
                                    <a class="details_order_my_acount box_details_order_my_acount">Edit</a>
                                </div>

                                <div class="block_order_account_view">
                                    <table class="left marg_t8" style="width: 713px; margin-left: 4px;">
                                        <tr style="border-bottom: 2px solid #7d7d7d;">
                                            <td class="title_artwork_order">
                                                ARTWORK
                                            </td>

                                            <td class="title_product_quantity_order">
                                                PRODUCT / QUANTITY
                                            </td>

                                            <td class="delivery_spping_order">
                                                DELIVERY / SHIPPING
                                            </td>

                                            <td class="price_order">
                                                PRICE
                                            </td>
                                        </tr>
                                        <tr style="border-bottom: 4px solid #7d7d7d">
                                            <td class="content_artwork_order">
                                                <div class="marg_l8 marg_t10 ">
                                                    <img src="/images/print_it/artwork.png">
                                                    <a href="" class="edit_pocket_folders marg_t6 marg_l3">Upload Artwork</a>
                                                </div>
                                            </td>

                                            <td class="content_product_quantity_order">
                                                <div class="marg_l8 marg_t10 ">
                                                    <div class="bold_title_12">POCKET FOLDERS</div>
                                                    Quantity: <strong>1000 (Split Ship)</strong><br>
                                                    Status: <strong class="red">PENDING</strong>

                                                    <div class="alert_block_izi">
                                                        <strong>ALERT!</strong> This item is pending until artwork is received.
                                                        A delay in artwork submission may affect the delivery date.
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="content_delivery_spping_order">
                                                <div class="marg_l8 marg_t10">
                                                    <div class="border_booto_delivery_spping">
                                                        Deliver By: <strong>Fri, Dec 21, 2012</strong><br>
                                                        Deliver To: <strong>ABC Orlando</strong><br>
                                                        Qty: <strong>600</strong>
                                                    </div>
                                                    <div class="border_booto_delivery_spping">
                                                        Deliver By: <strong>Wed, Dec 26, 2012</strong><br>
                                                        Deliver To: <strong>ABC Denver</strong><br>
                                                        Qty: <strong>400</strong><br>
                                                        Note: <strong>Blind Ship</strong>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="content_price_order">
                                                <div class="marg_l8 marg_t10">
                                                    <div class="right marg_r5">
                                                        <div class="bold_title_12">$2,467.95</div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="content_artwork_order">
                                                <div class="marg_l8 marg_t10 ">
                                                    <img src="/images/print_it/product_img.png">
                                                    <a href="" class="edit_pocket_folders marg_t6 marg_l3">Upload New Artwork</a>
                                                </div>
                                            </td>

                                            <td class="content_product_quantity_order">
                                                <div class="marg_l8 marg_t10 ">
                                                    <div class="bold_title_12">POCKET FOLDERS</div>
                                                    Quantity: <strong>400</strong><br>
                                                    Status: <strong class="green">PENDING</strong>

                                                </div>
                                            </td>

                                            <td class="content_delivery_spping_order">
                                                <div class="marg_l8 marg_t10">
                                                    <div class="border_booto_delivery_spping">
                                                        Deliver By: <strong> Wed, Dec 26, 2012</strong><br>
                                                        Deliver To: <strong>ABC Orlando</strong><br>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="content_price_order">
                                                <div class="marg_l8 marg_t10">
                                                    <div class="right marg_r5">
                                                        <div class="bold_title_12">$992.48</div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <div class="right block_order_totla_my_account">
                                        <div class="bold_title_12_wit order_total_my_account_title ">
                                            ORDER TOTAL
                                        </div>
                                        <div class="order_total_my_account">
                                            <div class="right bold_title_16_wit marg_t5 marg_r5">$3,460.43</div>
                                            <br class="clear">
                                        </div>
                                    </div>
                                </div>




                                <div class="marg_t17 left">
                                    <div class="marg_l12 left bold_title_14">Order # ITA-003</div>
                                    <div class="marg_l44 left marg_t3">
                                        <a href="/account/active_orders?datails_active=1" class="edit_pocket_folders">Order Details</a>
                                        <!--<a href="" class="details_order_my_acount">Edit</a>-->
                                        <div class="text_not_editable">NOT EDITABLE: Order has been shipped</div>

                                    </div>

                                    <div class="block_order_account_view">
                                        <table class="left marg_t8" style="width: 713px; margin-left: 4px;">
                                            <tr style="border-bottom: 2px solid #7d7d7d;">
                                                <td class="title_artwork_order">
                                                    ARTWORK
                                                </td>

                                                <td class="title_product_quantity_order">
                                                    PRODUCT / QUANTITY
                                                </td>

                                                <td class="delivery_spping_order">
                                                    DELIVERY / SHIPPING
                                                </td>

                                                <td class="price_order">
                                                    PRICE
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="content_artwork_order">
                                                    <div class="marg_l8 marg_t10 ">
                                                        <img src="/images/print_it/product_img.png">
                                                        <a href="" class="edit_pocket_folders marg_t6 marg_l3">Upload New Artwork</a>
                                                    </div>
                                                </td>

                                                <td class="content_product_quantity_order">
                                                    <div class="marg_l8 marg_t10 ">
                                                        <div class="bold_title_12">POCKET FOLDERS</div>
                                                        Quantity: <strong>400</strong><br>
                                                        Status: <strong class="green">SHIPPED</strong>

                                                    </div>
                                                </td>

                                                <td class="content_delivery_spping_order">
                                                    <div class="marg_l8 marg_t10">
                                                        <div class="border_booto_delivery_spping">
                                                            Deliver By: <strong> Mon, Dec 17, 2012</strong><br>
                                                            Deliver To: <strong>ABC San Francisco</strong><br>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="content_price_order">
                                                    <div class="marg_l8 marg_t10">
                                                        <div class="right marg_r5">
                                                            <div class="bold_title_12">$864.72</div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        <div class="right block_order_totla_my_account">
                                            <div class="bold_title_12_wit order_total_my_account_title ">
                                                ORDER TOTAL
                                            </div>
                                            <div class="order_total_my_account">
                                                <div class="right bold_title_16_wit marg_t5 marg_r5">$864.72</div>
                                                <br class="clear">
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        <?php } else { ?>
                            <div>
                                <div class="marg_b7 left" style="width: 722px;">
                                    <div class="left marg_t10" style="width: 189px;">
                                        <a href="/account/active_orders" class="back_bottom_dateils"></a>
                                        <a href="/account/active_orders?datails_active=1" class="next_bottom_dateils"></a>

                                    </div>

                                    <div class="right marg_t10" style="width: 236px;">
                                        <a href="javascript:window.print()" class="print_bottom_dateils"></a>
                                        <a href="/account/active_orders?datails_active=1" class="downloasd_bottom_dateils"></a>
                                    </div>
                                </div>
                                <br class="clear">
                                <div class="block_order_total_title">
                                    <div class="bold_title_18 left">
                                        ORDER # ITA-004
                                    </div>
                                    <div class="bold_title_18 right">
                                        Order Total: $3,460.43
                                    </div>
                                    <br class="clear">
                                </div>



                                <table class="left marg_t10 table_prit" style="width: 722px;">
                                    <tr style="border-bottom: 1px solid #000;">

                                        <td class="block_info_products">
                                            <div class="bold_title_14 left"style="margin-bottom: 11px;">ITEM 1</div>
                                            <br class="clear">
                                            <img src="/images/print_it/artwork.png">
                                            <div class="bold_title_14 marg_t10">POCKET FOLDERS</div>
                                            <div class="bold_title_14">Cost: $2,467</div>
                                            <strong class="f14">Quantity: 1000</strong> <span class="f14">(Split Ship)</span><br>

                                            Size: 9x12 (closed)<br>
                                            Style: 2-panel<br>
                                            Pockets: 2<br>
                                            Paper: 10pt. Gloss<br>
                                            Color: 4/0<br>
                                            Coating: Gloss Varnish<br>
                                            BC Slot: Horiz/Right<br>
                                            Special: Die Cut<br>
                                            Proof: Digital

                                            <div class="alert_block_print">
                                                <strong>ALERT!</strong> This item is pending until artwork is received.
                                                A delay in artwork submission may affect the delivery date.
                                            </div>
                                        </td>


                                        <td class="block_info_deliver_to_by">
                                            <div class="block_one_deliver_to_by">
                                                <div class="left_block_deliveri_to">
                                                    <div class="bold_title_14">Deliver To (1): ABC Orlando</div>
                                                    <div class="bold_title_12">Quantity: 600</div>
                                                    John Smith<br>
                                                    ABC Corporation<br>
                                                    1234 Industrial Park Blvd<br>
                                                    Suite 100<br>
                                                    Orlando, FL  12345<br>
                                                    234-567-8901
                                                </div>
                                                <div class="right_block_deliveri_by">
                                                    <div class="bold_title_14">Deliver By: Fri, Dec 21,2012</div>
                                                    <div class="bold_title_12">Delivery Type: Expedited</div>
                                                    The above guaranteed delivery date is based on submission of the following items by the dates listed.<br>
                                                    1. Press-ready Artwork:<br>
                                                    &nbsp;&nbsp;&nbsp; 5pm EST, Mon, December 10<br>
                                                    2. Proof Approval:<br>
                                                    &nbsp;&nbsp;&nbsp; 5pm EST, Tue, December 11
                                                </div>
                                                <br class="clear">
                                            </div>


                                            <div class="block_one_deliver_to_by">
                                                <div class="left_block_deliveri_to">
                                                    <div class="bold_title_14">Deliver To (2): ABC Denver</div>
                                                    <div class="bold_title_12">Quantity: 400</div>
                                                    Mary Thomas<br>
                                                    ABC Corporation<br>
                                                    678 Mountain Park Blvd<br>
                                                    Suite 21<br>
                                                    Denver, CO  67809<br>
                                                    345-678-9012


                                                    <div class="block_blin_ship_order_confirmation">
                                                        <strong>Blind Ship:</strong> This item will be shipped in generic packaging instead
                                                        of InkRockit packaging.
                                                    </div>
                                                </div>
                                                <div class="right_block_deliveri_by">
                                                    <div class="bold_title_14">Deliver By: Fri, Dec 26,2012</div>
                                                    <div class="bold_title_12">Delivery Type: Standard</div>
                                                    The above guaranteed delivery date is based on submission of the following items by the dates listed.<br>
                                                    1. Press-ready Artwork:<br>
                                                    &nbsp;&nbsp;&nbsp; 5pm EST, Mon, December 10<br>
                                                    2. Proof Approval:<br>
                                                    &nbsp;&nbsp;&nbsp; 5pm EST, Tue, December 11
                                                </div>
                                                <br class="clear">
                                            </div>
                                        </td>
                                    </tr>

                                    <!--ITEM 2-->
                                    <tr style="margin-top: 12px;display: block;">
                                        <td class="block_info_products">
                                            <div class="bold_title_14 left" style="margin-bottom: 11px;">ITEM 2</div>
                                            <br class="clear">
                                            <img src="/images/print_it/product_img.png">
                                            <div class="bold_title_14 marg_t10">POCKET FOLDERS</div>
                                            <div class="bold_title_14">Cost: $992.48</div>
                                            <div class="bold_title_14">Quantity: 500</div>

                                            Size: 9x12 (closed)<br>
                                            Style: 2-panel<br>
                                            Pockets: 2<br>
                                            Paper: 12pt. Gloss<br>
                                            Color: 4/1<br>
                                            Coating: Gloss Varnish<br>
                                            BC Slot: Horiz/Right<br>
                                            Proof: Digital<br>

                                        </td>


                                        <td class="block_info_deliver_to_by">
                                            <div class="block_one_deliver_to_by">
                                                <div class="left_block_deliveri_to">
                                                    <div class="bold_title_14">Deliver To: ABC Orlando</div>
                                                    John Smith<br>
                                                    ABC Corporation<br>
                                                    1234 Industrial Park Blvd<br>
                                                    Suite 100<br>
                                                    Orlando, FL  12345<br>
                                                    234-567-8901

                                                </div>
                                                <div class="right_block_deliveri_by">
                                                    <div class="bold_title_14">Deliver By: Fri, Dec 26,2012</div>
                                                    <div class="bold_title_12">Delivery Type: Standard</div>
                                                    The above guaranteed delivery date is based on submission of the following items by the dates listed.<br>
                                                    1. Press-ready Artwork:<br>
                                                    &nbsp;&nbsp;&nbsp; 5pm EST, Mon, December 10<br>
                                                    2. Proof Approval:<br>
                                                    &nbsp;&nbsp;&nbsp; 5pm EST, Tue, December 11
                                                </div>
                                                <br class="clear">
                                            </div>
                                        </td>
                                    </tr>



                                </table>
                                <br class="clear">
                                <div class="block_payment_info_title">
                                    <div class="bold_title_18 left">
                                        PAYMENT INFORMATION
                                    </div>
                                    <br class="clear">
                                </div>
                                <div class="payment_info_footer">
                                    <div class="left">
                                        <div class="bold_title_14 marg_t10">Payment Method</div>
                                        American Express<br>
                                        ******7890<br>
                                        John Smith
                                    </div>
                                    <div class="left" style="margin-left: 126px;">
                                        <div class="bold_title_14 marg_t10">Billing Information</div>
                                        ABC Corporation<br>
                                        John Smith<br>
                                        1234 Industrial Park Blvd<br>
                                        Suite 100<br>
                                        Orlando, Florida  12345<br>
                                        United States
                                    </div>
                                    <br class="clear">
                                </div>
                            </div>

                        <?php }
                        ?>
                    </div>


                </div>



            </div>
        </div>
    </div>
</form>