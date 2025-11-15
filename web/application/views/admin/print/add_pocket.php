<strong><?=(empty($pocket['title']))? 'Add New Pocket' : 'Edit Pocket - '.$pocket['title'] ;?></strong>
<br><br>

<?php
if(@$_SERVER['HTTP_REFERER'] == 'http://'.$_SERVER['SERVER_NAME'].@$_SERVER['REQUEST_URI'] && !empty($_POST['title'])){ ?>
<div class="msgbar msg_Success hide_onC">
    <span class="iconsweet">=</span><p>Information Saved</p>
</div>
<?php }?>

<form method="POST">
    <ul class="form_fields_container" style="width: 800px">
        <li>
            <label>Title</label>
            <div class="form_input">
                <input type="text" name="title" <?= (!empty($pocket['title'])) ? 'value="' . $pocket['title'] . '"' : '' ?>>
            </div>
            <br class="clear">
            
            <label>Type</label>
            <div class="form_input">
                <select name="type">
                    <option value="standard" <?php if(!empty($pocket['type']) && $pocket['type']=='standard') echo 'selected="selected"'; ?>>Standard</option>
                    <option value="expandable" <?php if(!empty($pocket['type']) && $pocket['type']=='expandable') echo 'selected="selected"'; ?>>Expandable</option>
                    <option value="box" <?php if(!empty($pocket['type']) && $pocket['type']=='box') echo 'selected="selected"'; ?>>Box</option>
                </select>
            </div>
            <br class="clear">
            
            <label>Active?</label>
            <div class="form_input">
                <select name="active">
                    <option value="1">Yes</option>
                    <option value="0" <?= (!empty($pocket) && $pocket['active']==0) ? 'selected' : '' ?>>No</option>
                </select>
            </div>
        </li>
        <li>
            <input class="button_small whitishBtn" type="submit" value="Save" name="add_pocket" style="margin-left: 20px">
            <a href="/admin/print/pockets" class="button_small whitishBtn">Cancel</a>
        </li>
    </ul>
</form>