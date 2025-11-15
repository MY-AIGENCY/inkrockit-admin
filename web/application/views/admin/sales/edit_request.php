<div class="one_wrap">
    <div id="activity_stats">
        <h3>Edit Requests</h3>
    </div>    

    <div class="widget">
        <div class="widget_body">
            <?php
            if (!empty($err)) {
                ?><br><div class="msgbar msg_Error hide"><p></p></div><?php
            } elseif (Request::initial()->post('save')) {
                ?><br><div class="msgbar msg_Success hide_onC"><p>Information Saved</p></div><?php
                    }

                    if (!empty($fedex_content)) {
                        ?><div style="padding: 30px; margin: 20px; background: #EEE;"><?php
                echo $fedex_content;
                ?></div><?php
            }
            ?>

            <form method="post" class="form_big">
                <div style="float: left; width: 600px">
                    <label>Request Date:</label> <p><?= $item['request_date'] ?></p>
                    <input type="hidden" name="user[id]" value="<?= $item['user_id'] ?>">
                    <label>Company:</label> <input type="text" name="user[company]" value="<?= $item['company'] ?>"><br class="clear">
                    <label>First Name:</label> <input type="text" name="user[first_name]" value="<?= $item['first_name'] ?>"><br class="clear">
                    <label>Last Name:</label> <input type="text" name="user[last_name]" value="<?= $item['last_name'] ?>"><br class="clear">
                    <label>Position:</label> <input type="text" name="user[position]" value="<?= $item['position'] ?>"><br class="clear">
                    <label>Street Address:</label> <input type="text" name="user[street]" value="<?= $item['street'] ?>"><br class="clear">
                    <label>Street Address2:</label> <input type="text" name="user[street2]" value="<?= $item['street2'] ?>"><br class="clear">
                    <label>City:</label> <input type="text" name="user[city]" value="<?= $item['city'] ?>"><br class="clear">
                    <label>State:</label> <input type="text" name="user[state]" value="<?= $item['state'] ?>"><br class="clear">
                    <label>Zipcode:</label> <input type="text" name="user[zipcode]" value="<?= $item['zipcode'] ?>"><br class="clear">
                    <label>Country:</label> <input type="text" name="user[country]" value="<?= $item['country'] ?>"><br class="clear">
                    <label>Fax:</label> 
                    <input type="text" name="user[fax]" value="<?= $item['fax'] ?>" class="phone_num" style="width: 260px"> 
                    <br class="clear">
                    <label>Phone Number:</label> 
                    <input type="text" name="user[phone]" value="<?= $item['phone'] ?>" class="phone_num" id="phone"> 
                    <input type="text" name="user[phone_ext]" value="<?= $item['phone_ext'] ?>" class="phone_num_ext" placeholder="ext.">
                    <span class="iconsweet add_alternative" data-type="phone">+</span>
                    <br class="clear">
                    <div class="alt_phone_block">
                        <?php
                        if (!empty($alt['phone'])) {
                            foreach ($alt['phone'] as $val) {
                                ?><div>
                                    <label>
                                        <select name="addtional_phone_type[]">
                                            <option value="cell">cell</option>
                                            <option value="home" <?php if ($val['content_type'] == 'home') echo'selected="selected"' ?>>home</option>
                                            <option value="office" <?php if ($val['content_type'] == 'office') echo'selected="selected"' ?>>office</option>
                                        </select>
                                    </label>
                                    <input type="text" name="phone_alt[]" value="<?= $val['value'] ?>" class="phone_num"> 
                                    <input type="text" name="phone_ext_alt[]" value="<?= $val['ext'] ?>" class="phone_num_ext" placeholder="ext.">
                                    <span class="iconsweet rem_alternative">-</span>
                                    <br class="clear">
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <label>Email:</label> <input type="text" name="user[email]" value="<?= $item['email'] ?>"> <span class="iconsweet add_alternative" data-type="email">+</span><br class="clear">
                    <div class="alt_email_block">
                        <?php
                        if (!empty($alt['email'])) {
                            foreach ($alt['email'] as $val) {
                                ?><div>
                                    <label>&nbsp;</label>
                                    <input type="text" name="email_alt[]" value="<?= $val ?>"> 
                                    <span class="iconsweet rem_alternative">-</span>
                                    <br class="clear">
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>

                    <label>User Industry:</label> <input type="text" name="user[industry]" value="<?= $item['user_industry'] ?>"><br class="clear">
                    <hr style="width: 80%">
                    <label>Operating System:</label> <input type="text" name="item[operating_sys]" value="<?= $item['operating_sys'] ?>"><br class="clear">
                    <label>Graphics Applications:</label> <input type="text" name="item[graphics_app]" value="<?= $item['graphics_app'] ?>"><br class="clear">
                    <label>Referral Source:</label> <input type="text" name="item[ref_source]" value="<?= $item['ref_source'] ?>"><br class="clear">
                    <label>Other Source:</label> <input type="text" name="item[other_source]" value="<?= $item['other_source'] ?>"><br class="clear">
                    <label>Industry:</label> <input type="text" name="item[industry]" value="<?= $item['industry'] ?>"><br class="clear">
                    <label>Email Special Offers:</label> <input type="text" name="item[offers]" value="<?= $item['offers'] ?>"><br class="clear">
                    <label>Products Interested In:</label> 
                    <p style="display: block; float: left">
                        <?php
                        $interest = unserialize($item['order_data']);
                        if (!empty($interest)) {
                            foreach ($interest as $val) {
                                echo $val . ';<br> ';
                            }
                        }
                        ?>
                    </p>
                    <br class="clear">

                    <label>Is this duplicate?</label>
                    <input type="radio" name="duplicate" value="1" <?php if ($item['duplicate'] == 1) echo'checked="true"' ?>> yes &nbsp;&nbsp;
                    <input type="radio" name="duplicate" value="0" <?php if ($item['duplicate'] == 0) echo'checked="true"' ?>> no
                    <br class="clear">

                </div>

                <div style="float: left; width: 500px">
                    <?php
                    $processeddate = ($item['processed_date'] != '0000-00-00' && !empty($item['processed_date']));
                    if ($processeddate && !empty($item['tracking_number'])) {
                        ?>
                                        <!--<input checked="checked" disabled="disabled" type="checkbox" name="processed"><label style="text-align: left; margin-left: 10px">Is Processed</label>-->
                        <a data-id="<?= $item['id'] ?>" data-more="" class="redishBtn button_small close_ship right" style="margin-left: 50px">Cancel Shipment</a>
                        <?php
                    }

                    if ($processeddate) {
                        ?>
                        Processed Date: <?= $item['processed_date'] ?><br>
                        Tracking Numbers:<br>
                        <p><?php
                            if (!empty($item['tracking_number'])) {
                                $number = explode(',', $item['tracking_number']);
                                $usps = substr($number[0], strpos($number[0], ":") + 1);
                                $uspsurl = 'http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=';
                                if (!empty($usps)) {
                                    echo 'USPS: <a href="' . $uspsurl . $usps . '" target="_blank">' . $usps . '</a><br/>';
                                }
                                $fdx = substr($number[1], strpos($number[1], ":") + 1);
                                $fdxurl = 'http://www.fedex.com/Tracking?language=english&cntry_code=us&tracknumbers=';
                                echo 'GROUND: <a href="' . $fdxurl . $fdx . '" target="_blank">' . $fdx . '</a><br/>';
                                echo 'Label: <a href="/admin/sales/label/' . $item['id'] . '" target="_blank">Print</a>; <a href="/files/fedex/png/' . $item['id'] . '.png" target="_blank">Show</a>; ';

                                if (!empty($item_more_req)) {
                                    foreach ($item_more_req as $v) {
                                        ?>
                                    <hr>
                                    <a data-id="<?= $item['id'] ?>" data-more="<?= $v['id'] ?>" class="redishBtn button_small close_ship right" style="margin-left: 50px">Cancel Shipment</a>
                                    Processed Date: <?= $v['processed_date'] ?><br>
                                    Tracking Numbers:<br>
                                    <?php
                                    $number = explode(',', $v['tracking_number']);
                                    $usps = substr($number[0], strpos($number[0], ":") + 1);
                                    $uspsurl = 'http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=';
                                    if (!empty($usps)) {
                                        echo 'USPS: <a href="' . $uspsurl . $usps . '" target="_blank">' . $usps . '</a><br/>';
                                    }
                                    if (!empty($number[1])) {
                                        $fdx = substr($number[1], strpos($number[1], ":") + 1);
                                        $fdxurl = 'http://www.fedex.com/Tracking?language=english&cntry_code=us&tracknumbers=';
                                        echo 'GROUND: <a href="' . $fdxurl . $fdx . '" target="_blank">' . $fdx . '</a><br/>';
                                        echo 'Label: <a href="/admin/sales/label/' . $item['id'] . '_' . $v['id'] . '" target="_blank">Print</a>; <a href="/files/fedex/png/' . $item['id'] . '_' . $v['id'] . '.png" target="_blank">Show</a>; ';
                                    }
                                    ?>
                                    <br><?php
                }
            }
        }
                        ?></p><br class="clear">
                        <?php
                    }
                    ?><br>
                    Conversations:<br>
                    <textarea name="item[conversations]"><?= $item['conversations'] ?></textarea><br>
                    Complete Address:<br>

                    <div style="margin-left: 10px; margin-top: 5px">
                        <?= $item['company'] ?><br>
                        <?= $item['street'] ?><br>
                        <?= $item['city'] ?>, <?= $item['state'] ?> <?= $item['zipcode'] ?><br>
                        <?= $item['first_name'] . ' ' . $item['last_name'] ?><br>
                        <?= $item['phone'] ?>
                    </div>
                    <br>

                    Search Engine Referral:<br>
                    <textarea name="item[search_id]"><?= $item['search_id'] ?></textarea><br class="clear">
                    Lead Source:<br>
                    <textarea name="item[search_keyword]"><?= $item['search_keyword'] ?></textarea><br class="clear">
                    Client IP: <?= $item['user_ip'] ?>
                </div>
                <br class="clear">
                <div style="margin-top: 20px; margin-left: 155px">
                    <input type="submit" class="button_small whitishBtn" name="save_prev" value="< Save and Prev">
                    <input type="submit" class="button_small whitishBtn" name="save" value="Save">
                    <input type="submit" class="button_small whitishBtn" name="save_next" value="Save and Next >">
                    <a href="<?= (empty($_GET['proc'])) ? '/admin/sales/all' : '/admin/sales/process'; ?>" class="button_small whitishBtn">Cancel</a>
                </div>
            </form>

        </div>
    </div>
</div>