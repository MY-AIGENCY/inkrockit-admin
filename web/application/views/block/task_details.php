<table class="tasks_table">
    <tr>
        <td width="70" class="buttons">
            <span class="blue pj_cat" id="send_email">Send<br> E-mail</span><br>
            <span class="green pj_cat" id="send_estimate">Send<br> Estimate</span><br>
            <span class="orange pj_cat" id="send_ship_note">Send<br> Ship Note</span><br><br>
            
            <span class="blue2 pj_cat" id="request_quote">Request<br> Quote</span><br>
            <span class="orange pj_cat" id="request_proof">Request<br> Proof</span><br>
            <span class="green pj_cat" id="submit_order">Submit<br> Order</span><br>
        </td>
        <td>
            <div id="main_block" class="task_block">
                <input name="request_id" type="hidden" value="<?=$order['id']?>">
                <div class="left" style="width: 880px">
                    <h6 style="margin-bottom: 5px">Notes</h6>
                    <textarea name="new_note" class="left"></textarea>
                    <a class="left button_big" id="add_new_note" style="margin-left: 4px; margin-top: 1px">Add Note</a>
                    <br class="clear"><br>
                    <div class="order_notes">
                    <?php
                    if(!empty($notes)){
                        foreach($notes as $val){
                            ?><div class="line">
                                <span class="note_date"><?=$val['date']?></span> <?=$val['text']?><br class="clear">
                            </div><?php
                        }
                    }
                    ?>
                    </div>
                </div>
                <div class="main_task_info">
                    <h6 style="margin-bottom: 5px">Address:</h6>
                    <?php 
                    echo $order['completeaddress'].'<br>';
                    ?>
                </div>
            </div>
            <div id="send_email_block" class="task_block hide">
                Email
            </div>
            <div id="send_estimate_block" class="task_block hide">
                Estimate
            </div>
            <div id="send_ship_note_block" class="task_block hide">
                Note
            </div>
            <div id="request_quote_block" class="task_block hide">
                Quote
            </div>
            <div id="request_proof_block" class="task_block hide">
                Proof
            </div>
            <div id="submit_order_block" class="task_block hide">
                Order
            </div>
        </td>
    </tr>
</table>