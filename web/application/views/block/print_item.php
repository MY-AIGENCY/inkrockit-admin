<ul class="insp_more_block">
    <li style="margin: 8px 0">

        <div class="title">
            <img src="/images/customArrov.png" class="arrow_right"> 
            <span class="left">Quantity</span>
            <span class="right item_quantity">
                <?php
                $items_count = (!empty($_SESSION['save_quantity'])) ? $_SESSION['save_quantity'] : $item_prices[0]['count'];
                echo $items_count;
                ?>
            </span>
            <input type="checkbox" name="save_quantity" <?php if (!empty($_SESSION['save_quantity'])) echo 'checked="checked"' ?> class="save_quantity">
            <span class="right">x</span>
            <br class="clear">
        </div>
        <div class="sub_select hide" style="width: 210px; top: -100px;">
            <div class="print_inn">
                <span class="sub_selectArrow" style="top: 100px">
                    <div class="sel_show"></div>
                </span>
                <h2>Quantity</h2>
            </div>
            <dl class="sub_ul">
                <dt style="min-height: 150px" class="lh22 mg_b6">
                Select Quantity:<br>
                <?php
                if (!empty($item_prices)) {
                    $base_custom_count = (empty($item['min_count'])) ? 0 : $item['min_count']; //!!!!!!!
                    $finded = 0;
                    $last_used = 0;
                    foreach ($item_prices as $key => $val) {
                        $price = ($item['min_price'] > $val['price'] * $val['count']) ? $item['min_price'] : $val['price'] * $val['count'];
                        if (!empty($items_count) && $items_count <= $val['count'] && $last_used < $items_count) {
                            $base_custom_price = ($item['min_price'] > $val['price'] * $items_count) ? $item['min_price'] : $val['price'] * $items_count;
                            $last_used = $val['count'];
                        }
                        ?>
                        <label>
                            <input type="radio" data-baseprice="<?= $price ?>" class="sel_count" <?php
                if ($val['count'] == $items_count) {
                    echo 'checked="checked"';
                    $finded = 1;
                }
                        ?> name="items_count[<?= $item['id'] ?>]" value="<?= $val['count'] ?>"> 
                            <?= $val['count'] ?> = <strong class="yellow">$<?= $price ?></strong>
                        </label>
                        <br><?php
                }
            }
                    ?>
                <label>
                    <input type="radio" data-baseprice="<?= $base_custom_price ?>" class="sel_count" name="items_count[<?= $item['id'] ?>]" <?php if (empty($finded)) echo'checked="checked"' ?> value="on"> 
                    Custom: <input type="number" min="0" step="50" class="items_custom_count" maxlength="5" name="items_custom_count[<?= $item['id'] ?>]" value="<?php if (empty($finded)) echo $base_custom_count ?>" style="width: 50px"> 
                    = <strong class="yellow">$<span class="base_custom_price"><?= $base_custom_price ?></span></strong>
                </label>
                <br>
                </dt>
            </dl>
            <br class="clear">
        </div>

    </li>
    <li class="dark">

        <div class="title">
            <span class="left"><?= $item['category']; ?></span>
            <span class="right item_size">
                <?= $item['title'] ?>
            </span>
            <br class="clear">
        </div>

    </li>
    <li>

        <div class="title">
            <img src="/images/customArrov.png" class="arrow_right"> 
            <span class="left">Pockets</span>
            <span class="right pockets_count">1</span>
            <br class="clear">
        </div>

        <div class="sub_select hide" style="width: 211px; top: -100px;">
            <div class="print_inn">
                <span class="sub_selectArrow" style="top: 100px">
                    <div class="sel_show"></div>
                </span>
                <h2>Pockets</h2>
            </div>
            <dl class="sub_ul">
                <dt style="min-height: 150px; line-height: 20px">
                <label><input type="radio" name="pockets_count[<?= $item['id'] ?>]" value="1" checked="checked" class="set_pockets_count"> 1</label><br>
                <label><input type="radio" name="pockets_count[<?= $item['id'] ?>]" value="2" class="set_pockets_count"> 2</label>
                </dt>
            </dl>
            <br class="clear">
        </div>


    </li>
    <li>
        <div class="title">
            <img src="/images/customArrov.png" class="arrow_right"> 
            <span class="left">BC/CD Slits</span>
            <span class="right slits_name">
                <?= $slits[$item['slits_def']]['name'] ?>
            </span>
            <br class="clear">
        </div>

        <?php
        $aval_slits = explode(',', $item['slits_aval']);
        ?>
        <div class="sub_select hide" style="width: 211px; top: -100px;">
            <div class="print_inn">
                <span class="sub_selectArrow" style="top: 100px">
                    <div class="sel_show"></div>
                </span>
                <h2>BC/CD Slits</h2>
            </div>
            <dl class="sub_ul">
                <?php
                if (!empty($aval_slits)) {
                    ?>
                    <dt style="min-height: 150px">
                    <img src="/files/print/slits/<?= $item['slits_def'] ?>.jpg"><br>
                    <p><?= $slits[$item['slits_def']]['description']; ?>&nbsp;</p>
                    <hr>
                    <div class="lh22 mg_b6">
                        Select your option:<br>
                        <?php foreach ($aval_slits as $val) { ?>
                            <label>    
                                <input type="radio" name="slits[<?= $item['id'] ?>]" class="set_slits" value="<?= $val ?>" <?php if ($val == $item['slits_def']) echo'checked="checked"' ?>> <?= $slits[$val]['name'] ?><br>
                            </label>
                        <?php } ?>
                    </div>

                    </dt>
                    <?php
                }
                ?>
            </dl>
            <br class="clear">
        </div>


    </li>
    <li>
        <div class="title">
            <img src="/images/customArrov.png" class="arrow_right"> 
            <span class="left">Paper</span>
            <span class="right paper_name">
                <?= $papers[$item['paper_def']]['name'] ?>
            </span>
            <br class="clear">
        </div>

        <?php
        $paper_list = explode(',', $item['paper_aval']);
        ?>
        <div class="sub_select hide" style="width: 211px; top: -100px;">
            <div class="print_inn">
                <span class="sub_selectArrow" style="top: 100px">
                    <div class="sel_show"></div>
                </span>
                <h2>Paper</h2>
            </div>
            <dl class="sub_ul">
                <?php
                if (!empty($paper_list)) {
                    ?>
                    <dt style="min-height: 150px">
                    <p><?= $papers[$item['paper_def']]['description']; ?>&nbsp;</p>
                    <hr>
                    <div class="lh22 mg_b6">
                        Select your option:<br>
                        <?php foreach ($paper_list as $val) { ?>
                            <label>
                                <input type="radio" class="set_paper" name="paper[<?= $item['id'] ?>]" value="<?= $val ?>" <?php if ($val == $item['paper_def']) echo'checked="checked"' ?>> 
                                <?= $papers[$val]['name'] ?> = <b class="yellow">$</b><br>
                            </label>
                        <?php } ?>
                    </div>

                    </dt>
                    <?php
                }
                ?>
            </dl>
            <br class="clear">
        </div>


    </li>
    <li>
        <div class="title">
            <img src="/images/customArrov.png" class="arrow_right"> 
            <span class="left">Ink Colors</span>
            <span class="right ink_name">
                <?= $inks[$item['inks_def']]['name'] ?>
            </span>
            <br class="clear">
        </div>

        <?php
        $inks_list = explode(',', $item['inks_aval']);
        ?>
        <div class="sub_select hide" style="width: 211px; top: -100px;">
            <div class="print_inn">
                <span class="sub_selectArrow" style="top: 100px">
                    <div class="sel_show"></div>
                </span>
                <h2>Ink Colors</h2>
            </div>
            <dl class="sub_ul">
                <?php
                if (!empty($inks_list)) {
                    ?>
                    <dt style="min-height: 150px">
                    <p><?= $inks[$item['inks_def']]['description']; ?>&nbsp;</p>
                    <hr>
                    <div class="lh22 mg_b6">
                        Select your option:<br>
                        <?php foreach ($inks_list as $val) { ?>
                            <label>
                                <input type="radio" class="set_inks" name="inks[<?= $item['id'] ?>]" value="<?= $val ?>" <?php if ($val == $item['inks_def']) echo'checked="checked"' ?>> <?= $inks[$val]['name'] ?><br>
                            </label>
                        <?php } ?>
                    </div>

                    </dt>
                    <?php
                }
                ?>
            </dl>
            <br class="clear">
        </div>

    </li>
    <li>
        <div class="title">
            <img src="/images/customArrov.png" class="arrow_right"> 
            <span class="left">Coatings</span>
            <span class="right coating_sel">
                <?php
                if (!empty($item['coating_def'])) {
                    echo $coats[$item['coating_def']]['title'];
                }
                ?>
            </span>
            <br class="clear">
        </div>

        <div class="sub_select hide" style="width: 211px; top: -280px;">
            <div class="print_inn">
                <span class="sub_selectArrow" style="top: 280px">
                    <div class="sel_show"></div>
                </span>
                <h2>Coatings</h2>
            </div>
            <dl class="sub_ul">
                <dt>
                <img src="/images/coatingImg.png">
                <div class="coatTitle">Full Coverage</div>
                <p>
                    This is FPO copy. A brief description of this item will go here. This is FPO copy. 
                    A brief description of this item will go here. This is FPO copy. A brief description of this item will 
                    go here. This is FPO copy.
                </p>
                <hr>
                <div class="lh22 mg_b6">
                    Select your option:<br>
                    <label><input type="radio" name="coating[<?= $item['id'] ?>]" value=""><span class="title">none</span></label><br>
                    <?php
                    if (!empty($coats)) {
                        foreach ($coats as $val) {
                            ?><label>
                                <input type="radio" name="coating[<?= $item['id'] ?>]" value="<?= $val['id'] ?>" <?php if ($val['id'] == @$item['coating_def']) echo'checked="checked"' ?>> <span class="title"><?= $val['title'] ?></span>
                                = <strong class="yellow"></strong>
                            </label><br><?php
                        }
                    }
                    ?>
                    <p>*price based on selected quantity</p>
                </div>
                </dt>
            </dl>
            <br class="clear">
            <div class="footer">
                SPECIAL OFFERS: <span class="desct">Free spot UV with quantity of 3000 or more.</span>
            </div>
        </div>


    </li>
    <li>
        <div class="title">
            <img src="/images/customArrov.png" class="arrow_right"> 
            <span class="left">Custom Finishes</span>
            <span class="right finishes_sel"><?php
            if(!empty($item['finishes_def']) && !empty($finish[$item['finishes_def']]['title'])){
                echo @$finish[$item['finishes_def']]['title'];
            }
            ?></span>
            <br class="clear">
        </div>


        <div class="sub_select hide" style="width: 211px; top: -235px;">
            <div class="print_inn">
                <span class="sub_selectArrow" style="top: 235px">
                    <div class="sel_show"></div>
                </span>
                <h2>Custom Finishes</h2>
            </div>
            <dl class="sub_ul">
                <dt>
                <img src="/images/coatingImg.png">
                <div class="coatTitle">Full Coverage</div>
                <p>
                    This is FPO copy. A brief description of this item will go here. This is FPO copy. 
                    A brief description of this item will go here. This is FPO copy. A brief description of this item will 
                    go here. This is FPO copy.
                </p>
                <hr>
                <div class="lh22 mg_b6">
                    Select your option:<br>
                    <label><input type="radio" name="finishes[<?= $item['id'] ?>]" value=""> none</label><br>
                    <?php
                    if (!empty($finish)) {
                        foreach ($finish as $val) {
                            ?><label>
                                <input type="radio" name="finishes[<?= $item['id'] ?>]" value="<?= $val['id'] ?>" <?php if ($val['id'] == $item['finishes_def']) echo'checked="checked"' ?>> <span class="title"><?= $val['title'] ?></span> 
                                = <strong class="yellow"></strong>
                            </label><br><?php
                        }
                    }
                    ?>
                    <p>*price based on selected quantity</p>
                </div>

                </dt>
            </dl>
            <br class="clear">
            <div class="footer">
                SPECIAL OFFERS: <span class="desct">Free spot UV with quantity of 3000 or more.</span>
            </div>
        </div>

    </li>
    <li>
        <div class="title">
            <img src="/images/customArrov.png" class="arrow_right"> 
            <span class="left">Proof</span>
            <span class="right">
<?= $proofs[$item['proof_def']]['name'] ?>
            </span>
            <br class="clear">
        </div>

        <?php
        $proof_list = explode(',', $item['proof_aval']);
        ?>
        <div class="sub_select hide" style="width: 211px; top: -100px;">
            <div class="print_inn">
                <span class="sub_selectArrow" style="top: 100px">
                    <div class="sel_show"></div>
                </span>
                <h2>Proof</h2>
            </div>
            <dl class="sub_ul">
                <?php
                if (!empty($proof_list)) {
                    ?>
                    <dt style="min-height: 150px">
                    <img src="/files/print/proof/<?= $item['proof_def'] ?>.jpg">
                    <p><?= $proofs[$item['proof_def']]['description']; ?>&nbsp;</p>
                    <hr>
                    <div class="lh22 mg_b6">
                        Select your option:<br>
    <?php foreach ($proof_list as $val) { ?>
                            <label>
                                <input type="radio" name="proof[<?= $item['id'] ?>]" value="<?= $val ?>" <?php if ($val == $item['proof_def']) echo'checked="checked"' ?>> <?= $proofs[$val]['name'] ?><br>
                            </label>
    <?php } ?>
                    </div>
                    </dt>
                    <?php
                }
                ?>
            </dl>
            <br class="clear">
        </div>

    </li>


<?php if (!empty($sub_order[$val])) { ?>
        <ul class="stitched">
            <li class="dark" style="margin-top: 8px">
                <div class="title">
                    <span class="left">Stitched-in Booklet</span>
                    <span class="right stick_size">

                    </span>
                    <br class="clear">
                </div>
            </li>
            <li>
                <div class="title">
                    <img src="/images/customArrov.png" class="arrow_right"> 
                    <span class="left"># of Pages (including cover)</span>
                    <span class="right">

                    </span>
                    <br class="clear">
                </div>
            </li>
            <li>
                <div class="title">
                    <img src="/images/customArrov.png" class="arrow_right"> 
                    <span class="left">Paper</span>
                    <span class="right">

                    </span>
                    <br class="clear">
                </div>
            </li>
            <li>
                <div class="title">
                    <img src="/images/customArrov.png" class="arrow_right"> 
                    <span class="left">Ink Colors</span>
                    <span class="right">

                    </span>
                    <br class="clear">
                </div>
            </li>
            <li>
                <div class="title">
                    <img src="/images/customArrov.png" class="arrow_right"> 
                    <span class="left">Coatings</span>
                    <span class="right">

                    </span>
                    <br class="clear">
                </div>
            </li>
            <li>
                <div class="title">
                    <img src="/images/customArrov.png" class="arrow_right"> 
                    <span class="left">Custom Finishes</span>
                    <span class="right">

                    </span>
                    <br class="clear">
                </div>
            </li>
            <li>
                <div class="title">
                    <img src="/images/customArrov.png" class="arrow_right"> 
                    <span class="left">Proof</span>
                    <span class="right">

                    </span>
                    <br class="clear">
                </div>
            </li>
        </ul>
    <?php }
    ?>



</ul>

<div class="order_info_1">
    Order today and<br>
    receive item by<br>
    <strong class="yellow">

    </strong>
</div>

<div class="order_info_2">
    <ul class="insp_more_block">
        <li>
            <span class="title">Subtotal</span>
            <span class="right">$<span class="subtotal_price">

                </span></span>
            <br class="clear">
        </li>
        <li>
            <span class="left"> 
                <img src="/images/customArrov.png" class="arrow_right"> Shipping<br>
                <span class="blue"><span class="shipping_time">

                    </span> Bus. Days</span>
            </span>
            <span class="shipping_price right">

            </span>
            <br class="clear">
        </li>
        <li>
            <span class="title">
                <img src="/images/customArrov.png" class="arrow_right"> 
                Discounts
            </span>
            <span class="right">- $<span class="discount_summ">

                </span></span>
            <br class="clear">
        </li>
        <li class="yellow dark">
            <b>YOU PAY ONLY</b>
            <strong class="right">$<span class="total">

                </span></strong>
            <br class="clear">
        </li>
        <li>
            <span class="title">Per Item Cost</span>
            <span class="right">$<span class="price_one">

                </span></span>
            <br class="clear">
        </li>
    </ul>
    <div class="line"></div>
</div>

<img src="/images/cart_add.png" class="right" style="margin: 12px;">
<br class="clear">