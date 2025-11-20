<div class="jobs_id ui-tabs" style="display: flex; align-items: center; gap: 20px;">
    <div class="left" style="display: flex; align-items: center; gap: 15px;">
        <select name="select_one_job" style="min-width: 90px; width: 90px;">
            <option value="">All</option>
            <?php
            if (!empty($job_id)) {
                foreach ($job_id as $val) {
                    $current_id = (empty($val['job_id'])) ? $val['estimate_id'] : $val['job_id'];
                    $k = explode('-', $current_id);
                    $key = preg_replace('/[^0-9]*/', '', $k[1]);
                    $sorted[$key][$k[0] . $k[1]] = $val;
                }
                ksort($sorted);
                $sorted = array_reverse($sorted);
                foreach ($sorted as $key => $val) {
                    ksort($val);
                    foreach ($val as $k => $v) {
                        $current_id = (empty($v['job_id'])) ? $v['estimate_id'] : $v['job_id'];
                        ?><option value="<?= $v['id'] ?>" <?php if (!empty($current_job_id) && $current_job_id == $v['id']) echo'selected="selected"' ?>><?= $current_id ?></option><?php
                    }
                }
            }
            ?>
        </select>
        <img src="/images/admin/<?= (empty($eye_company)) ? 'no_eye' : 'eye' ?>.png" class="eye_company_change" title="<?= (empty($eye_company)) ? 'Enable to be viewable in Active Customers tab' : 'Disable displaying at Active Customers tab' ?>">
        <span class="add_job pointer" style="background: #4CAF50; color: white; padding: 6px 12px; border-radius: 4px; border: 1px solid #45a049; white-space: nowrap; font-weight: 500;">Add Job</span>
        <!--<span class="iconsweet add_job_manual">+</span>-->
        <!--<span class="add_estimate pointer">add estimate</span>-->
    </div>

    <div class="jobs_list">
        <ul class="job_menu ui-tabs-nav">
            <li data-id="" class="clickable <?php if (empty($current_job_id)) echo'ui-tabs-selected' ?>"><a>All</a></li>
            <?php
            if (!empty($job_id)) {
                if (!empty($sorted)) {
                    foreach ($sorted as $key => $val) {
                        ksort($val);
                        foreach ($val as $k => $v) {
                            $current_id = (empty($v['job_id'])) ? $v['estimate_id'] : $v['job_id'];
                            ?>
                            <li data-id="<?= $v['id'] ?>" class="clickable <?php if (!empty($current_job_id) && $current_job_id == $v['id']) echo'ui-tabs-selected' ?>">
                                <a><?= $current_id ?></a>
                                <span class="ui-icon ui-icon-pencil pointer marg_t5 edit_job" style="margin-right: 0">edit</span>
                                <span class="ui-icon ui-icon-close pointer marg_t5 remove_job">del</span>
                            </li>
                            <?php
                        }
                    }
                }
            }
            ?>
        </ul>
    </div>
    <?php if (!empty($job_id) && count($job_id) > 5) { ?>
        <div class="job_slider">
            <a class="right_arrow"></a>
            <a class="left_arrow"></a>
        </div>
    <?php } ?>
    <br class="clear">
</div>

<table class="tasks_table">
    <tr>
        <td width="30" style="position: relative">
            <div data-type="S" class="user_type sales <?php if ($current_user_type === 'S') echo'active'; ?>">SALES</div>
            <div data-type="O" class="user_type orders <?php if ($current_user_type === 'O') echo'active'; ?>">ORDERS</div>
            <div data-type="A" class="user_type admin <?php if ($current_user_type === 'A') echo'active'; ?>">ALL</div>
        </td>        
        <td width="70" class="buttons">
            <span class="blue pj_cat <?php if($tpl == 'task_details_email') echo 'visible_block';?>" id="send_email">Send<br> E-mail</span><br>
            <span class="green pj_cat <?php if($tpl == 'task_details_estimate') echo 'visible_block';?>" id="send_estimate">Send<br> Estimate</span><br>
            <span class="orange pj_cat <?php if($tpl == 'task_details_ship') echo 'visible_block';?>" id="send_ship_note">Send<br> Ship Note</span><br><br>

            <span class="blue2 pj_cat <?php if($tpl == 'task_details_quote') echo 'visible_block';?>" id="request_quote">Request<br> Quote</span><br>
            <span class="orange pj_cat <?php if($tpl == 'task_details_proof') echo 'visible_block';?>" id="request_proof">Request<br> Proof</span><br>
            <span class="green pj_cat <?php if($tpl == 'task_details_order') echo 'visible_block';?>" id="submit_order">Submit<br> Order</span><br>
            <span class="blue pj_cat <?php if($tpl == 'task_details_process_cc') echo 'visible_block';?>" id="precess_cc">Process<br> Credit Card</span><br>
        </td>
        <td class="main_block_info">
            <div id="main_block">
                <?php require $tpl.'.php';?>
            </div>
        </td>
    </tr>
</table>