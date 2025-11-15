<div class="line <?php if ($val['removed'] == 1) echo'removed' ?>">

    <div class="job_type">
        <span class="left user_cher"><?= $val['type_user'] ?></span>
        <span class="info_block <?php
        if (in_array($val['type'], array('credit', 'call_in', 'call_out', 'email_in', 'email_out', 'shipping_in', 'shipping_out'))) {
            echo $val['type'];
        } elseif (!empty($val['type'])) {
            echo 'payment';
        }
        ?>"><?php
                  if (in_array($val['type'], array('credit', 'call_in', 'call_out', 'email_in', 'email_out', 'shipping_in', 'shipping_out'))) {
                      echo Kohana::message('en', 'note_types.' . $val['type']);
                  } elseif (!empty($val['type'])) {
                      echo 'Payment';
                  }
                  ?>
        </span>
        <select name="edit_type" style="width: 70px; font-size: 10px" class="hide">
            <option value="">-</option>
            <?php /*
              <option value="balance" <?php if ($val['type'] == 'balance') echo'selected="selected"' ?>>Balance</option>
              <option value="installment" <?php if ($val['type'] == 'installment') echo'selected="selected"' ?>>Installment</option>
              <option value="deposit" <?php if ($val['type'] == 'deposit') echo'selected="selected"' ?>>Deposit</option>
             */ ?>
            <option value="payment" <?php if ($val['type'] == 'payment') echo'selected="selected"' ?>>Payment</option>
            <option value="credit" <?php if ($val['type'] == 'credit') echo'selected="selected"' ?>>Credit</option>
            <option value="call_in" <?php if ($val['type'] == 'call_in') echo'selected="selected"' ?>>CALL (IN)</option>
            <option value="call_out" <?php if ($val['type'] == 'call_out') echo'selected="selected"' ?>>CALL (OUT)</option>
            <option value="email_in" <?php if ($val['type'] == 'email_in') echo'selected="selected"' ?>>EMAIL (IN)</option>
            <option value="email_out" <?php if ($val['type'] == 'email_out') echo'selected="selected"' ?>>EMAIL (OUT)</option>
            <option value="shipping_in" <?php if ($val['type'] == 'shipping_in') echo'selected="selected"' ?>>SHIPPING (IN)</option>
            <option value="shipping_out" <?php if ($val['type'] == 'shipping_out') echo'selected="selected"' ?>>SHIPPING (OUT)</option>
        </select>
    </div>

    <a class="ui-icon ui-icon-pencil left edit_note pointer" data-id="<?= $val['id'] ?>">edit</a>
    <a class="ui-icon ui-icon-close left del_note pointer" data-id="<?= $val['id'] ?>">del</a>

    <span class="note_date">
        <span class="date" data-formated="<?= $val['date_orig'] ?>"><?= $val['date'] ?>&nbsp;</span>
        <div class="center">
            <strong><?php
                if (!empty($val['job_id'])) {
                    echo $val['job_id'];
                } elseif (!empty($val['estimate_id'])) {
                    echo $val['estimate_id'];
                }
                ?></strong>
        </div>
        <?php
        if ($val['removed'] == 1) {
            echo'<br><span class="restore_note white underline pointer" data-id="' . $val['id'] . '">restore</span>';
        }
        ?>
    </span> 


    <?php if (strlen($val['text']) > 250) {
        ?><div class="left note_text"><?= nl2br(substr($val['text'], 0, 250)) . '<span class="show_more_note"><i>Show more</i></span><span class="hide">'.nl2br(substr($val['text'], 250)).'</span>' ?></div><?php
    } else {
        ?><div class="left note_text"><?= nl2br($val['text']) ?></div><?php }
    ?>

    <div class="left"><?= $val['first_name'] . ' ' . $val['last_name'] ?>
<?php
if (!empty($val['required_username'])) {
    echo ' -> ' . $val['required_username'];
    if (!empty($notes_required[$val['id']][0])) {
        echo '<br>' . $notes_required[$val['id']][0]['date'];
    }
}
?>
    </div>
    <br class="clear">

<?php
if (!empty($notes_required[$val['id']])) {
    $cookie = new Cookie();
    $admin = $cookie->get('admin_user');
    if (!empty($admin)) {
        $admin_user = unserialize($admin);
    }

    foreach ($notes_required[$val['id']] as $key => $val) {

        if ($val['text'] != "") {
            ?><div class="required_block">
                    <div class="left" style="width: 375px">&nbsp;</div>
                    <div class="left required_message" style="<?php
            if ($key % 2 != 0)
                echo'background: #FFF';
            ?>">
                        <img src="/images/admin/reply_small.gif" class="left" style="margin-right: 4px">
                         <?= $val['text'] ?>
                    </div>
                    <div class="left">
            <?php
            echo $val['from_username'];
            if (!empty($val['for_username'])) {
                echo ' -> ' . $val['for_username'];
            }
            ?>
                        <br>
                        <?= $val['date'] ?>
                    </div>
                    <br class="clear">
                </div>
            <?php
        }

        if ($val['for_uid'] == $admin_user['id'] && $val['status'] == 0) {
            ?>
                <div class="required_block active_block">
                    <img src="/images/admin/reply.png" class="left" style="margin-top: 3px">
                    <select name="required_ansver" class="left" style="background: red;">
                        <option value="">Action Required</option>
                        <option value="resolved">Resolved</option>
                        <option value="clarify">Clarify</option>
                    </select>

                    <div class="required_reassign">
                        <div class="hide re_assign">
                            <select name="reassign_uid">
                                <option value="">Assign to</option>
            <?php
            if (!empty($admin_users)) {
                foreach ($admin_users as $v) {
                    ?><option value="<?= $v['id'] ?>"><?= $v['first_name'] . ' ' . $v['last_name'] ?></option><?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        &nbsp;
                    </div>

                    <div class="left hide required_text">
                        <textarea name="required_text"></textarea><br>
                        <input type="button" value="Send" name="save_required_req" data-id="<?= $val['id'] ?>">
                    </div>
                    <br class="clear">
                </div>
            <?php
        }
    }
}
?>



</div>