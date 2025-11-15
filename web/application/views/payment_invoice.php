<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="/css/normalize.css">
        <link rel="stylesheet" href="/css/admin/payment_invoice.css">
    </head>
    <body>
        <div class="main_h">
            <header>
                <table width="100%">
                    <tr>
                        <td width="180"><img src="/images/ink_logo.png" width="170"></td>
                        <td class="po_box">
                            <div>
                                P O Box 951353<br>
                                <?= $pay['card_billing']['first_name'] . ' ' . $pay['card_billing']['last_name'] ?>, __ ___________<br>
                                <?= $pay['card_billing']['phone'] ?>
                                <br class="clear">
                            </div>
                        </td>
                        <td style="text-align: right">
                            <div class="invoice">
                                invoice
                            </div>
                            <small class="inv_page">(page 1 of 2)</small>
                        </td>
                    </tr>
                </table>
            </header>

            <section>
                <table width="100%" style="font-size: 11px;">
                    <tr>
                        <td width="188">
                            <table width="170" border="1">
                                <tr>
                                    <td class="table_head">
                                        Bill To:
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ink_req">
                                        <strong>Don Traub</strong><br>
                                        InkRokit.com LLC<br>
                                        205 Springview Dr.<br>
                                        Sanford, FL 32773<br>
                                        <br>
                                        407-603-7202 x 120
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="200">
                            <table width="200" border="1">
                                <tr>
                                    <td class="table_head">
                                        <div style="">
                                            Payment Information
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="dtraub">
                                        <?= $myorder['name'] ?><br>
                                        <?= $pay['card']['title'] ?> ######<?= substr($pay['card']['card_number'], -4) ?><br>
                                        <br>
                                        <b class="purpl">PAID this transaction: $<?= number_format($myorder['subtotal'], 2) ?></b>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td >
                            <table width="300" style="font-size: 10px">
                                <tr>
                                    <td colspan="2" class="trans_rec" align="right">
                                        Transaction Receipt
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" class="date_time">
                                        <br><br>
                                        Date/Time:<br>
                                        <br>
                                        Transaction ID:<br>
                                        <br>
                                        INVOICE#
                                    </td>
                                    <td align="right"> 
                                        <br><br>
                                        <table border="1" width="180" class="time_est_tab">
                                            <tr>
                                                <td width="180" class="time_est">
                                                    <?= date('m/d/Y') ?><br>
                                                    at <?= date('g:ia') ?> (EST)
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="time_est">
                                                    <?= $trans_id ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="time_est">
                                                    <?= $pay['ponumber'] ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <table border="1" width="670" class="tab2">
                    <tr>
                        <td class="table_head" align="center" width="30"><b>Qty</b></td>
                        <td class="table_head" align="center" width="100"><b>Item</b></td>
                        <td class="table_head" align="center"><b>Item Description</b></td>
                        <td class="table_head" align="center" width="100"><b>Amount</b></td>
                    </tr>
                    <tr>
                        <td class="inn_table" align="center">100,000</td>
                        <td class="inn_table">
                            <b>ITEM I</b><br>
                            <br>
                            <br>
                            Pocket Folder
                        </td>
                        <td class="inn_table">
                            <b>Custom 3 Panel Folder with (1)4" Pocket inside</b><br>
                            <br>
                            <!--<span class="title_col">Size:</span>-->
                            <p class="descr_col">9"(W)x12"(H)(Folded);27.75"(W)X16"(H)(Flat)</p>
                            <!--<span class="title_col">Paper:</span>-->
                            <p class="descr_col">14pt C2S</p>
                            <!--<span class="title_col">Inks:</span>-->
                            <p class="descr_col">
                                4/4 + 1/S Matte Lamination + 1/S Spot UV(extra large area)<br>
                                +Emboss(extra large area)
                            </p>
                            <!--<span class="title_col">Finish:</span>-->
                            <p class="descr_col">Custom Die Cut, Scope, Fold & Glue</p>
                        </td>
                        <td class="inn_table" align="center">$32,269.00</td>
                    </tr>
                    <tr>
                        <td class="inn_table" align="center">100,000</td>
                        <td class="inn_table">
                            <b>ITEM I</b><br>
                            <br>
                            <br>
                            Pocket Folder
                        </td>
                        <td class="inn_table">
                            <b>Custom 3 Panel Folder with (1)4" Pocket inside</b><br>
                            <br>
                            <!--<span class="title_col">Size:</span>-->
                            <p class="descr_col">9"(W)x12"(H)(Folded);27.75"(W)X16"(H)(Flat)</p>
                            <!--<span class="title_col">Paper:</span>-->
                            <p class="descr_col">14pt C2S</p>
                            <!--<span class="title_col">Inks:</span>-->
                            <p class="descr_col">
                                4/4 + 1/S Matte Lamination + 1/S Spot UV(extra large area)<br>
                                +Emboss(extra large area)
                            </p>
                            <!--<span class="title_col">Finish:</span>-->
                            <p class="descr_col">Custom Die Cut, Scope, Fold & Glue</p>
                        </td>
                        <td class="inn_table" align="center">$32,269.00</td>
                    </tr>
                    <tr>
                        <td class="inn_table" align="center">100,000</td>
                        <td class="inn_table">
                            <b>ITEM I</b><br>
                            <br>
                            <br>
                            Pocket Folder
                        </td>
                        <td class="inn_table">
                            <b>Custom 3 Panel Folder with (1)4" Pocket inside</b><br>
                            <br>
                            <!--<span class="title_col">Size:</span>-->
                            <p class="descr_col">9"(W)x12"(H)(Folded);27.75"(W)X16"(H)(Flat)</p>
                            <!--<span class="title_col">Paper:</span>-->
                            <p class="descr_col">14pt C2S</p>
                            <!--<span class="title_col">Inks:</span>-->
                            <p class="descr_col">
                                4/4 + 1/S Matte Lamination + 1/S Spot UV(extra large area)<br>
                                +Emboss(extra large area)
                            </p>
                            <!--<span class="title_col">Finish:</span>-->
                            <p class="descr_col">Custom Die Cut, Scope, Fold & Glue</p>
                        </td>
                        <td class="inn_table" align="center">$32,269.00</td>
                    </tr>
                </table>

                <div class="continue">
                    <em>continues on another page...</em>
                </div>

                <hr class="nomarg">

                <table width="100%">
                    <tr>
                        <td align="right" class="receiv" valign="top">
                            <b>Payments Received<br>
                                & Applied to date:</b>
                        </td>
                        <td align="right" width="500">
                            <table border="1" width="500" class="tab_sec_pad">
                                <?php
                                $paid = 0;
                                if (!empty($history)) {

                                    foreach ($history as $val) {
                                        if ($val['type'] != 'credit' && $val['type'] != 'order_confirmed' && $val['type'] != 'change_total') {
                                            ?>
                                            <tr>
                                                <td><?= $val['date'] ?> - <?= $val['first_name'] . ' ' . $val['last_name'] ?> <?= $val['title'] ?> ######<?= substr($val['card_number'], -4); ?> (<?php
                                                    $total = (empty($val['total'])) ? $job['order_total'] : $val['total'];
                                                    $proc = $total / $val['summ'];
                                                    if ($proc == 1) {
                                                        echo '100% Balance';
                                                    } elseif ($proc == 0.5) {
                                                        echo '50% Deposit';
                                                    } elseif ($proc == 0.3) {
                                                        echo '30% Installment';
                                                    } elseif ($proc == 0.2) {
                                                        echo '20% Installment';
                                                    } else {
                                                        echo 'Installment';
                                                    }
                                                    ?>)</td>
                                                <td width="100" align="right">$<?= number_format($val['summ'], 2) ?></td>
                                            </tr>
                                            <?php
                                            $paid += $val['summ'];
                                        }
                                    }
                                }
                                $total_payed = $paid + $myorder['subtotal'];
                                ?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" class="today_pay">
                            <b>Today's Payment:</b>
                        </td>
                        <td>
                            <table border='1' width="500" class="tab_sec_pad">
                                <tr>
                                    <td class="pin"><?= date('m/d/Y') ?> - <?= $myorder['name'] ?> <?= $pay['card']['title'] ?> ######<?= substr($pay['card']['card_number'], -4); ?> (<?php
                                        $proc = $job['order_total'] / $myorder['subtotal'];
                                        if ($proc == 1) {
                                            echo '100% Balance';
                                        } elseif ($proc == 0.5) {
                                            echo '50% Deposit';
                                        } elseif ($proc == 0.3) {
                                            echo '30% Installment';
                                        } elseif ($proc == 0.2) {
                                            echo '20% Installment';
                                        } else {
                                            echo 'Installment';
                                        }
                                        ?>)</td>
                                    <td align="right" width="100" class="pin">$<?= number_format($myorder['subtotal'], 2) ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <table width="100%" class="tab_sec_pad">
                    <tr>
                        <td align="right" class="pin_col1">Total payments made to date:</td>
                        <td width="100" align="right" class="pin_col2">$<?= number_format($total_payed, 2) ?></td>
                    </tr>
                </table>

                <b>Notes:</b>                
                <hr style="border: none; margin: 3px 0 0 0">

                <table width='100%'>
                    <tr>
                        <td width='460' style="font-style: italic">
                            This invoice shows a different order total than the previous invoice for this same job number. It includes the addition of item III.<br>
                            <br>
                            Thank you foe allowing us this opportunity to serve you; we look forward to our next!
                        </td>
                        <td align="right">
                            <table class="tab_sec_pad" border='1' width='195' style="font-weight: normal; margin-top: 16px">
                                <tr>
                                    <td>Order Total</td>
                                    <td width='100' align='right'>$<?= number_format($job['order_total'], 2) ?></td>
                                </tr>
                                <tr>
                                    <td>Total PAID</td>
                                    <td align='right'>-$<?= number_format($total_payed, 2) ?></td>
                                </tr>
                                <tr>
                                    <td class="table_head"><b>Balance:</b></td>
                                    <td class="table_head" align='right'>$<?= number_format($job['order_total'] - $total_payed, 2) ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

            </section>
            <br>
        </div>

    </body>
</html>