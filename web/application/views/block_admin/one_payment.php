<?php
if ($val['removed'] == 0) {
    ?>
    <tr class="show_trans_details <?php
    switch ($val['type']) {
        case 'credit':
        case 'failed':
            echo 'red';
            break;
        case 'change_total':

            $change_tmp = $last_balance - $val['summ'];
            if ($change_tmp > 0) {
                echo 'red bold';
            } else {
                echo 'green bold';
            }
            break;
        case 'order_confirmed':
            echo 'green bold';
            break;
    }
    if ($key % 2 == 0) {
        echo ' grey_bg';
    }
    ?>">
        <td class="bordered">
            <?php
            if ($val['type'] == 'change_total') {
                echo 'ORDER TOTAL MOD: ';
            } elseif ($val['type'] == 'order_confirmed') {
                echo 'ORDER CONFIRMED: ';
            } else {
                echo'<span class="marg_l20" data-trans="T' . $trans . '">TRANSACTION ' . $trans . ':</span>';
            }
            ?></td>
        <td style="text-align: center">
            <?php
            if ($val['type'] == 'change_total' || $val['type'] == 'order_confirmed') {
                echo '$' . number_format($val['summ'], 2);
            } else {
                if($val['summ'] < 0){
                    echo '-$' . number_format(abs($val['summ']), 2);
                }else{
                    echo '$' . number_format($val['summ'], 2);
                }


                $trans++;
            }
            ?>
        </td>
        <td width='200' class="bordered"><?= $val['date'] ?></td>
        <td style="text-transform: uppercase"><?php
            switch ($val['type']) {
                case 'credit':
                    echo'Credit';
                    if(!empty($val['small_descr'])){
                        echo ' ('.$val['small_descr'].')';
                    }
                    $balance += $val['summ'];
//                    $payed -= $val['summ'];
                    break;
                case 'change_total':
                    $change = $last_balance - $val['summ'];
                    if ($change > 0) {
                        echo '-$' . abs($change);
                        $balance -= abs($change);
                    } else {
                        echo '+$' . abs($change);
                        $balance += abs($change);
                    }
                    $last_balance = $val['summ'];
                    break;
                case 'payment':
                case 'manual_check':
                case 'manual_cash':
                case 'manual_cc':
                case 'manual_misc':
                    echo 'Payment';
                    $balance -= $val['summ'];
//                    $payed += $val['summ'];
                    break;
                case 'order_confirmed':
                    echo 'Order Confirmed';
                    $balance = $val['summ'];
                    $last_balance = $val['summ'];
                    break;
                case 'failed':
                    echo 'Failed';
                    break;
                case 'redistribute':
                    echo 'Redistribute';
                    if(!empty($val['small_descr'])){
                        echo ' ('.$val['small_descr'].')';
                    }
                    $balance += $val['summ'];
//                    $payed -= $val['summ'];
                    break;
                case 'redistr_add':
                    echo 'Redistribute';
                    if(!empty($val['small_descr'])){
                        echo ' ('.$val['small_descr'].')';
                    }
                    $balance -= $val['summ'];
//                    $payed += $val['summ'];
                    break;
            }
            ?></td>
        <td class="bordered"><?php
            if (!empty($val['card_number'])) {
                $num = str_replace(' ', '', $val['card_number']);
                echo 'X' . substr($num, -4);
            } elseif ($val['type'] != 'change_total' && $val['type'] != 'order_confirmed') {
                if ($val['type'] == 'manual_check') {
                    echo 'check';
                } elseif ($val['type'] == 'manual_cc') {
                    echo 'credit card';
                } elseif ($val['type'] == 'manual_cash') {
                    echo 'cash';
                } elseif ($val['type'] == 'manual_misc') {
                    echo 'misc';
                } else {
                    echo '-----';
                }
            } else {
                echo '-----';
            }
            ?></td>
        <td>
            <div class="marg_l20">
                <?php
                $balance_show = number_format($balance, 2, '.', ',');
                echo ($balance >= 0) ? '$' . $balance_show : '-$' . abs($balance_show); ?>
            </div>
        </td>
        <td style="text-align: right" class="bordered"><?php
            if (strpos($val['type'], 'manual_') !== FALSE || $val['type']=='failed') {
                ?><a class="edit_manual_payment pointer marg_r10" data-id="<?= $val['id'] ?>">Edit</a><?php
            }
            if ($val['summ'] > 0 && $val['type'] != 'change_total' && $val['type'] != 'credit' && $val['type'] != 'redistribute' && $val['type'] != 'redistr_add' && $val['type'] != 'order_confirmed' && $val['type'] != 'failed' && strpos($val['type'], 'manual_') === FALSE) {
                ?>
                <a data-id="<?= $val['id'] ?>" class="credit_payment pointer marg_r10">Return</a> | 
                <a data-id="<?= $val['id'] ?>" class="redestribute_payment pointer marg_r10 marg_l10">Edit</a>
                <?php
            }
            
            if ($val['type'] == 'order_confirmed') {
                ?><a class="edit_manual_payment pointer marg_r10" data-id="<?= $val['id'] ?>">Edit</a><?php
            }
            
            ?></td>
    </tr>
    <tr class="trans_details">
        <td colspan="7">
            <?= $val['description'] ?>
        </td>
    </tr>
    <?php
}
    