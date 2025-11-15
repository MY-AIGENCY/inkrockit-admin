<?php
if (!empty($finded)) {
    ?>
    <table class="activity_datatable" style="width: 100%; min-width: 1300px" id="sortTable">
    <thead>
    <tr>
        <th><input type="checkbox" name="check_all" style="margin-right: 25px"></th>
        <th>#</th>
        <th>Request Date</th>
        <th>Company</th>
        <th>Industry</th>
        <th>Name</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Processed Date</th>
        <th>Tracking Numbers</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($finded as $key => $val) {
        ?>
        <tr data-id="<?=$val['id']?>" data-uid="<?=$val['user_id']?>" data-cid="<?=$val['company_id']?>"
            class="print_row" <?php
        if (!empty($val['order_data'])) {
//            $need_req = unserialize($val['order_data']);
            if (strpos($val['order_data'], "Photo Frame") !== false) {
                echo 'style="background-color: #f7df65"';
            }
        } elseif ($val['ref_source'] == 'photoframepro.com') {
            echo 'style="background-color: #ffb020"';
        }
        if ($val['duplicate']) {
            echo 'style="background-color:#ffcccc"';
        }
        ?>>
            <td width="50" class="center">
                <input type="checkbox" name="fedex[]" data-check="all" value="<?=$val['id']?>">
                <a class="ui-icon ui-icon-pencil right" href="/admin/sales/all/edit/<?=$val['id']?>">edit</a>
            </td>
            <td width="20"><?=($open_page * $for_page) + ($key + 1)?></td>
            <td width="100" class="load_tasks"><?=$val['request_date']?></td>
            <td class="load_tasks"><?=$val['company']?></td>
            <td width="200" class="load_tasks industry_field">
                <?=@$val['req_industry']?>
                <span class="industry_title"><?=@$val['industry_send']?></span>
                <span class="iconsweet right" style="opacity: 0">8</span>
            </td>
            <td class="load_tasks show_users"><?=$val['first_name'] . ' ' . $val['last_name']?>
                <div class="hide aval_company_users">
                    <img src="/images/admin/load6.gif">
                </div>
            </td>
            <td width="110" class="additional_phone">
                <small class="v1">
                    <?=$val['phone'];
                    if (!empty($val['phone_ext'])) {
                        echo ' ext ' . $val['phone_ext'];
                    }
                    echo '<br>';
                    //additional
                    if (!empty($alt[$val['user_id']]['phone'])) {
                        foreach ($alt[$val['user_id']]['phone'] as $v) {
                            if (!empty($v['value'])) {
                                echo $v['value'];
                                if (!empty($v['ext'])) {
                                    echo ' ext ' . $v['ext'];
                                }
                                echo '<br>';
                            }
                        }
                    }?></small>
                <span class="iconsweet right" style="opacity: 0">8</span>
            </td>
            <td width="80" class="additional_email">
                <div class="v1">
                    <a href="mailto:<?=$val['email']?>"><?=$val['email']?></a><br>
                    <?php
                    if (!empty($alt[$val['user_id']]['email'])) {
                        foreach ($alt[$val['user_id']]['email'] as $v) {
                            if (!empty($v['value'])) {
                                ?><a href="mailto:<?=$v['value']?>"><?=$v['value']?></a><br><?php
                            }
                        }
                    }
                    ?>
                </div>
                <span class="iconsweet right" style="opacity: 0">8</span>
            </td>
            <td width="110"><?=($val['processed_date'] == '0000-00-00') ? '-' : $val['processed_date'];?></td>
            <td width="80"><?php
                if (!empty($val['tracking_number'])) {
                    $number = explode(',', $val['tracking_number']);
                    $usps = substr($number[0], strpos($number[0], ":") + 1);
                    $fdx = substr($number[1], strpos($number[1], ":") + 1);
                    $uspsurl = 'http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=';
                    $fdxurl = 'http://www.fedex.com/Tracking?language=english&cntry_code=us&tracknumbers=';

                    if (!empty($usps)) {
                        echo 'USPS: <a href="' . $uspsurl . $usps . '" target="_blank">' . $usps . '</a><br/>';
                    }
                    echo 'GROUND: <a href="' . $fdxurl . $fdx . '" target="_blank">' . $fdx . '</a><br/>';
                }
                ?></td>
            <td width="20">
                <a class="ui-icon ui-icon-closethick left remove"
                   href="/admin/sales/all/remove/<?=$val['id']?>">delete</a>
            </td>
        </tr>
        <?php
    }
    ?></tbody>
    </table>
    <?php
    if (!empty($pages)) {
        ?>
        <input type="hidden" name="page_sel" value="">
        <div class="content_pad text_center">
        <ul class="pagination ajax_pagin"><?php
            echo $pages;
            ?>
        </ul></div><?php
    }
} else {
    ?><p style="padding: 20px">*Nothing found</p><?php
}
?>
