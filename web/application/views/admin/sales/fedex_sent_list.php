<!DOCTYPE html>
<html>
    <head>
        <title>Sample Shipping Manifest</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="/css/admin/reset.css">
        <style>
            body{
                font-family: Arial;
            }
            .info td{
                padding-top: 20px;
            }
            .check{
                border: 1px solid #333; width: 20px; height: 20px; margin-right: 5px; float: left; position: relative;
            }
            .check .checked{
                position: absolute; width: 24px; height: 24px; bottom: 0; left: 1px; background: url(/images/check.png);
            }
            .item{
                width: 50px; float: left; overflow: hidden; height: 80px; margin: 2px 10px 2px 2px; text-align: center; margin-bottom: 5px;
            }
            .item img{
                margin-top: 5px;
            }
            .pad_left td{
                padding-left: 20px;
            }
            table{
                margin-left: 20px;
                margin-top: 20px;
            }
            table td{
                padding: 5px 0;
                vertical-align: middle
            }
            .samples{
                border-bottom: 1px solid #333; padding-bottom: 8px;
            }
            .angle{
                display: block;
                font-size: 12px;
                -moz-transform: rotate(-40deg);
                -webkit-transform: rotate(-40deg);
                -o-transform: rotate(-40deg);
                -ms-transform: rotate(-40deg);
                transform: rotate(-40deg);
                position: absolute;
                top: 10px;
                left: 0;
            }
            .angle:last-child{
                left: 32px;
                top: 20px;
            }
        </style>
    </head>
    <body <?php if (!empty($requests)) { ?>onload="javascript: print();"<?php } ?>>

        <div class="one_wrap">

            <div class="widget">
                <div class="widget_body">
                    <?php
                    if (!empty($requests)) {
                        $num = 1;
                        ?>
                        <table width ="1000">
                            <tr class="pad_left">
                                <td width="90" style="position: relative">
                                    <span class="angle">Label Printed</span>
                                    <span class="angle">Shipped</span>
                                </td>
                                <td width="120">Date</td>
                                <td width="320">Customer</td>
                                <td>Industry</td>
                                <td width="170">Custom Additions</td>
                            </tr>
                            <?php
                            foreach ($requests as $req) {
                                ?>
                                <tr class="info">
                                    <td>
                                        <div class="check"><?php if (in_array($req['id'], $printed)) echo '<span class="checked"></span>'; ?></div>
                                        <div class="check" style="margin-right: 10px"><?php if (!empty($req['processed_date']) && $req['processed_date'] != '0000-00-00') echo'<span class="checked"></span>' ?></div>
                                        <?= $num; ?>
                                    </td>
                                    <td><?= $req['request_date'] ?></td>
                                    <td><?= $req['username'] ?></td>
                                    <td><?= $req['industry'] ?></td>
                                    <td><?php
                                        if (!empty($samples[$req['id']])) {
                                            $count = 0;
                                            foreach ($samples[$req['id']] as $key => $val) {
                                                foreach ($val as $v) {
                                                    if ($count == 3) {
                                                        break;
                                                    }
                                                    if (!empty($sample_details[$key][$v])) {
                                                        ?>
                                                        <div class="item">
                                                            <?= $sample_details[$key][$v]['item_id'] ?><br>
                                                            <img class="left" src="/files/items/thumbnails/<?= $v ?>.png" width="50">
                                                            <br class="clear">
                                                        </div>
                                                        <?php
                                                    }
                                                    $count++;
                                                }
                                            }
                                            ?><br class="clear"><?php
                                        }
                                        ?></td>
                                </tr>

                                <tr>
                                    <td class="samples" colspan="5">
                                        <?php
                                        $count = 0;
                                        if (!empty($samples[$req['id']])) {
                                            foreach ($samples[$req['id']] as $key => $val) {
                                                foreach ($val as $v) {
                                                    if (!empty($sample_details[$key][$v]) && $count > 2) {
                                                        ?>
                                                        <div class="item">
                                                            <?= $sample_details[$key][$v]['item_id'] ?><br>
                                                            <img class="left" src="/files/items/thumbnails/<?= $v ?>.png" width="50" style="margin-right: 4px">
                                                            <br class="clear">
                                                        </div>
                                                        <?php
                                                    }
                                                    $count++;
                                                }
                                            }
                                            ?><br class="clear"><?php
                                        }
                                        ?>
                                        <br>
                                    </td>
                                </tr>
                                <?php
                                $num++;
                            }
                            ?></table><?php
                    } else {
                        ?><br>*No requests<?php
                        }
                        ?>
                </div>
            </div>
        </div>

    </body>
</html>