<div class="one_wrap" style="width: 1000px">
    <div class="widget">
        <div class="widget_body">
            <?php
            if (!empty($requests)) {
                ?><table class="activity_datatable" width="100%">
                    <tr>
                        <th>Item #</th>
                        <th>Customer</th>
                        <th>Industry</th>
                        <th width="100">Action</th>
                    </tr>
                    <?php
                    foreach ($requests as $val) {
                        ?>
                        <tr>
                            <td><?= $val['id'] ?></td>
                            <td><?= $val['username'] ?></td>
                            <td><?= $val['industry'] ?></td>
                            <td>
                                <a target="_blank" href="/admin/sales/label/<?= $val['id'] ?>" class="button_small whitishBtn print_button">Print</a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?></table><?php
            } else {
                ?><br><span class="err">*No requests</span><br><br><?php
                }
                ?>
        </div>
    </div><br>
    <a target="_blank" href="/admin/Sales/fedex_sentlist" class="button_small dblueBtn">Continue &raquo; Sample Shippeng Manifest</a>
</div>