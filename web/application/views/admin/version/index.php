<div class="one_wrap">

    <div class="widget" style="margin-top: 0">
        <div class="widget_body" style="padding: 20px 30px">
            <form action="" method="post">
                <h3>Bug reports:</h3><br>
                <?php
                if (!empty($add_rez) && !empty($add_rez->rez)) {
                    ?><strong>Task added successful!</strong><?php
                }
                ?>
                <em class="error"></em>
                <ul class="form_fields_container" style="width: 800px">
                    <li>
                        <label>Title:</label>
                        <div class="form_input"><input type="text" name="title">
                            <input placeholder="Deadline" type="text" name="deadline" class="small right" id="datepicker">
                        </div>
                        <br class="clear">

                        <label>Description:</label>
                        <div class="form_input"><textarea name="text"></textarea></div>
                        <br class="clear">

                        <label>Priority:</label>
                        <div class="form_input"><select name="priority">
                                <?php for ($x = 1; $x <= 5; $x++) {
                                    ?><option value="<?= $x ?>" <?php if ($x == 2) echo 'selected="selected"' ?>><?= $x ?></option><?php }
                                ?>
                            </select></div>
                        <br class="clear">

                        <input type="submit" class="dblueBtn button_small" style="margin-left: 176px; margin-top: 10px" value="Post a Bug" name="bug_add">
                    </li>
                </ul>
            </form>
        </div>
    </div>
    
    <div class="widget">
        <div class="widget_body" style="padding: 20px 30px">
            <h2>Current Version: 1.2.1 [Build 06-04-2014]</h2>
            <div style="line-height: 16px">
                <strong>1.1. Contact list</strong><br>
                <br>
                1.1.1 Search - by default allows backend user to do search query in Data Base in all fields.<br>
                a) search field send the search request (AJAX request) into DB after one character been put into the input field. Results of the search request are displayed in Contact List section under the search field. <br>
                NOTE: You may use Enter Key (computer keyboard or the Return Key on Macs) to update the search query results. This function works only if the input field is active.<br>
                b) the search field trigger an action each 0.5 sec after the last action been taken.<br>
                1.1.2 Advanced Search - allows backend user to specify fields for the search query.<br>
                a) grouped by 3 general criteria (Contact Info, Address, Other)<br>
                b) checkboxes allow to specify the search request to Data Base and to do search in specified fields.<br>
                c) do a search by date or date range.<br>
                d) saving selected search criteria until user sign out<br>
                <br>
                1.1.3 Contact List section<br>
                a) displaying the search query results. 50 results per one page.<br>
                b) sorting functionality<br>
                c) switching between pages does not refresh the page<br>
                d) view/edit/remove DB record functionality<br>
                NOTE: View/Edit DB record - open a new page with information about the client (clients profile information).<br>
                <br>
                e) assign/create/edit/save sample pack contents under the column Industry<br>
                NOTE: industry column has two variables:<br>
                1) industry which client select from sample pack request from <br>
                2) assign/create/edit/save sample pack contents  <br>
                <br>
                f) add alternative Phone and Email<br>
                NOTE: not visible on clients profile information only at contact list section<br>
                <br>
                1.2. Process requests<br>
                1.2.1 Process request section (list)<br>
                a) display only the new requests which have not been processes.<br>
                b) there are two colors white and yellow:<br>
                - white - regular request which we receive from sample pack request form (../request)<br>
                - yellow - submitted from specific sale page (now it goes from ../custom_photo_frames)<br>
                c) to process the request and ship the sample pack request via FedEx form.<br>
                d) user to select the sample pack contents <br>
                e) multiple select functionality allows to process all requests which user have picked (checked the checkbox)<br>
                f) view/edit/remove DB record functionality  <br>
                <br>
                1.2.2 FedEx Create a Shipment form<br>
                a) pick up<br>
                - autofilling with clients profile information<br>
                - autofilling with sender information<br>
                - input fields are editable<br>
                NOTE: form filling out with the data which we have (clients profile information)<br> 
                b) ship<br>
                - autofilling with clients profile information<br>
                - autofilling with sender information<br>
                - input fields are editable<br>
                NOTE: form filling out with the data which we have (clients profile information)<br> 
                c) create/process/calculate the shipping cost using FedEx API<br>
                d) save the template for the form (not finished)<br>
                e) generating a shipping labels<br>
                - print label<br>
                f) store the tracking number under the clients profile information page<br>
                g) cancel shipping functionality<br>
                h) pint shipping manifest<br>
                i) save/display the sample pack contents (not finished) <br>
                j) track the shipping event and post it into Note section. (for now we just make a note that sample pack was sent. Will be updated soon.) <br>
                <br>
                <strong>2. Inspiration Station</strong><br>
                <strong>2.1. Product Types</strong><br>
                <strong>2.2. Industries</strong><br>
                <strong>2.3. Sample Inventory</strong><br>
                <strong>3.Print It</strong><br>
                <strong>3.1 Products</strong><br>
                <strong>3.2 Product Properties</strong><br>
                <strong>3.3 Main Settings</strong><br>
                <strong>4. Users</strong><br>
                <strong>4.1 All users</strong><br>
                <strong>4.2 Add New User</strong><br>

            </div>
        </div>
    </div>

    <div class="widget">
        <div class="widget_body" style="padding: 20px 30px">
            <h3 class="left" style="margin-bottom: 2px">Tasks in progress:</h3>
            <div class="right">
                <span class="color status x0"></span> New
                <span class="color status x1"></span> In progress
                <span class="color status x2"></span> Done, need to test
                <span class="color status x3"></span> Error
                <span class="color status x4"></span> Finish
            </div><br class="clear">
            <hr>
            <?php
            if (!empty($bugs->err)) {
                foreach ($bugs['err'] as $val) {
                    ?><em class="err"><?= $val ?></em><br><?php
                }
            } elseif (!empty($bugs->tasks)) {
                foreach ($bugs->tasks as $val) {
                    ?><div class="bug_task">
                        <div class="status x<?= $val->status ?>"></div>
                        <h5>
                            <a target="_blank" href="http://jobs.ukiepro.com/projects/view/<?= $val->project_id ?>/<?= $val->id ?>"><?= $val->title ?></a>

                            <span class="ui-icon ui-icon-battery-<?php
                            if ($val->priority == 5) {
                                $val->priority = 4;
                            }
                            echo ($val->priority - 1)
                            ?> right"></span>
                        </h5>
                        <div>
                            <?php if (!empty($val->deadline) && $val->deadline != "0000-00-00") { ?>
                                <span class="grey">Deadline: </span><?= date("d-m-Y", strtotime($val->deadline)); ?><br>
                            <?php } ?>
                            <span class="grey">Description: </span> <?= nl2br($val->description) ?>
                            <?php
                            if (!empty($bugs->comments->{$val->id})) {
                                ?><br><hr>
                                <span class="ui-icon ui-icon-comment"></span>
                                <span class="grey"><?= $bugs->comments->{$val->id}->username ?>:</span>
                                <em><?= nl2br($bugs->comments->{$val->id}->text) ?></em>
                                <span class="right grey"><?= date("d-m-Y H:i", strtotime($bugs->comments->{$val->id}->date)); ?></span>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>

        </div>
    </div>

</div>