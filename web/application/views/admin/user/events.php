<h2 class="left">Events</h2>

<div class="one_wrap">
    <div class="widget" style="width: 800px">
        <div class="widget_body">
            <table class="activity_datatable" width="100%">
                <tbody>
                    <tr>
                        <th>Type</th>
                        <th>User</th>
                        <th width="150">Date</th>
                    </tr>
                    <?php
                    if (!empty($events)) {
                        foreach ($events as $val) {
                            ?>
                            <tr>
                                <td>
                                    <a class="event_details" data-id="<?=$val['id']?>">
                                    <?php
                                    switch ($val['type']) {
                                        case 'fedex_ship':
                                            echo 'Fedex Shipment';
                                            break;
                                        case 'fedex_pickup':
                                            echo 'Fedex Pickup';
                                            break;
                                        case 'close_ship':
                                            echo 'Shipment Closed';
                                            break;
                                        case 'card_payment':
                                            echo 'Credit Card Payment';
                                            break;
                                        case 'new_request':
                                            echo 'New Request';
                                            break;
                                        case 'new_user':
                                            echo 'New User';
                                            break;
                                        case 'payment_removed':
                                            echo 'Payment History Removed';
                                            break;
                                        case 'creditcard_removed':
                                            echo 'Credit Card Removed';
                                            break;
                                        case 'request_removed':
                                            echo 'Request Removed';
                                            break;
                                        case 'add_creditcard':
                                            echo 'New Credit Card';
                                            break;
                                        case 'user_updated':
                                            echo 'User Data Updated';
                                            break;
                                        case 'user_removed':
                                            echo 'User Removed';
                                            break;
                                        case 'update_request':
                                            echo 'Request Data Updated';
                                            break;
                                        case 'new_job':
                                            echo 'New Job Added';
                                            break;
                                        case 'job_removed':
                                            echo 'Job Removed';
                                            break;
                                        case 'order_confirmed':
                                            echo 'Order Confirmed';
                                            break;
                                        case 'order_modif':
                                            echo 'Order Modification';
                                            break;
                                        case 'cash_payment':
                                            echo 'Cash Payment';
                                            break;
                                        case 'check_payment':
                                            echo 'Check Payment';
                                            break;
                                        case 'misc_payment':
                                            echo 'Misc Payment';
                                            break;
                                        case 'redistr_payment':
                                            echo 'Payment Redistributed';
                                            break;
                                        case 'edit_transaction':
                                            echo 'Edit Transaction';
                                            break;
                                        case 'failed':
                                        case 'order_failed':
                                            echo 'Order Failed';
                                            break;
                                        case 'credit':
                                            echo 'Credit Transaction';
                                            break;
                                    }
                                    ?></a>
                                </td>
                                <td><?= (empty($val['username'])) ? 'System' : $val['username']; ?></td>
                                <td><?= $val['date'] ?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>

                </tbody>
            </table>
            <div class="content_pad text_center">
                <ul class="pagination">
                <?php
                if (!empty($pages)) {
                    echo $pages;
                }
                ?>
                </ul>
            </div>
        </div>
    </div>
</div>