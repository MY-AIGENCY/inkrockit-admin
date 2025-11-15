<h4>Issue Credit. <span class="coment">Transaction <?=$pay['transaction_code']?></span></h4><br>
<table width='100%'>
    <tr>
        <td width='120'>Total amount:</td>
        <td>$<span class="order_totals"><?=$pay['summ']?></span></td>
    </tr>
    <tr>
        <td>Credit amount:</td>
        <td>$<input type="text" name="credit_amount" value="<?=$pay['summ']?>"></td>
    </tr>
    <tr>
        <td style="vertical-align: top">Note:</td>
        <td><textarea name="credit_note"></textarea></td>
    </tr>
    <tr>
        <td>
            <button class="close_shipping_form button_small whitishBtn right marg_r10">Cancel</button><br>
        </td>
        <td>
            <button name="run_credit" class="greenishBtn button_small left" style="margin-left: 0" data-id='<?=$pay['id']?>'>Credit</button><br class="clear"><br>
            <span class="error marg_r15 marg_t10"></span>
        </td>
    </tr>
</table>