<h3>Redistribute Payment</h3><br>

<table width="100%">
    <tr>
        <td width="50%">JOB# <?= $job['job_id'] ?> [<?=$trans?>]</td>
        <td style="border-bottom: 1px dashed #CCC">AMOUNT 
            <input type="text" name="redist_amount" disabled="true" readonly="true" value="<?= $payment['summ'] ?>">
            <br>
            <br>
        </td>
    </tr>
    <tr>
        <td><br>
            JOB# 
            <select name="job1">
                <?php
                if (!empty($jobs)) {
                    foreach ($jobs as $val) {
                        if ($val['id'] != $payment['job_id']) {
                            ?><option value="<?= $val['id'] ?>"><?= $val['job_id'] ?></option><?php
                        }
                    }
                }
                ?>
            </select>
        </td>
        <td><br>
            AMOUNT <input type="text" name="redis_summ1" value="" class="numeric">
            <input type="button" class="split_redistr" value="SPLIT">
            <br>
            <br>
        </td>
    </tr>
    <tr style="display: none">
        <td class="bord_t">
            JOB# 
            <select name="job2">
                <?php
                if (!empty($jobs)) {
                    foreach ($jobs as $val) {
                        if ($val['id'] != $payment['job_id']) {
                            ?><option value="<?= $val['id'] ?>"><?= $val['job_id'] ?></option><?php
                        }
                    }
                }
                ?>
            </select>
        </td>
        <td class="bord_t">
            AMOUNT <input type="text" name="redis_summ2" value="" class="numeric">
            <img src="/images/rem.png" class="remove_redistr_job marg_l10 pointer">
            <br>
            <br>
        </td>
    </tr>
    <tr>
        <td class="bord_t">
            <span class="left" style="line-height: 60px">JOB# <?= $job['job_id'] ?> [<span class="trans_code"><?=$trans?></span>]</span>            
            <small style="width: 150px; display: block; float: left; margin-left: 20px">
                <span style="font-size: 11px">Balance of Transaction</span><br>
                <input type="text" value="" name="trans_balance" style="width: 130px" readonly="true" disabled="true">
            </small>
        </td>
        <td class="bord_t" style="vertical-align: top">
            <input type="button" class="right button_small greenishBtn run_redistribute marg_t10" data-id="<?=$payment['id']?>" value="Submit Redistribution"><br class="clear">
            <span class="error redist_err right"></span>
        </td>
    </tr>
</table>