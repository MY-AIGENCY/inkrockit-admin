<?php

function print_data($table, $id, $type = '') {
    $rez = DB::sql_row('SELECT * FROM ' . $table . ' WHERE id=:id', array(':id' => $id));
    if (!empty($rez) && is_array($rez)) {
        echo '<strong>' . $type . ' details:</strong><br>';
        foreach ($rez as $key => $val) {
            if (!empty($val)) {
                $k = str_replace('_', ' ', $key);
                echo $k . ' = ' . $val . '<br>';
            }
        }
    } else {
        echo $type . ' Removed';
    }
}

function print_array($arr) {
    $data = unserialize($arr);
    if (!empty($data) && is_array($data)) {
        foreach ($data as $key => $val) {
            if (!empty($val)) {
                $k = str_replace('_', ' ', $key);
                echo $k . ' = ' . $val . '<br>';
            }
        }
    }
}

if (!empty($data)) {
    switch ($data['type']) {
        case 'fedex_ship':
        case 'fedex_pickup':
        case 'close_ship':
        case 'new_request':
        case 'update_request':
            print_data('requests', $data['type_id'], 'Request');
            break;
        case 'card_payment':
            print_data('payment_history', $data['type_id'], 'Payment History');
            break;
        case 'new_user':
            print_data('users', $data['type_id'], 'User');
            break;
        case 'payment_removed':
        case 'creditcard_removed':
        case 'request_removed':
        case 'job_removed':
        case 'user_removed':
            if (!empty($data['text'])) {
                print_array($data['text']);
            }
            break;
        case 'add_creditcard':
            print_data('credit_card', $data['type_id'], 'Credit Card');
            break;
        case 'user_updated':
            print_data('users', $data['type_id'], 'User');
            break;
        case 'new_job':
            echo $data['text'];
            break;
        case 'order_confirmed':
        case 'order_modif':
        case 'cash_payment':
        case 'check_payment':
        case 'misc_payment':
        case 'redistr_payment':
        case 'edit_transaction':
        case 'order_failed':
            print_data('payment_history', $data['type_id'], 'Details');
            break;
    }
}
?>