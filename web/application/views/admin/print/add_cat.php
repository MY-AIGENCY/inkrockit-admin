<strong><?=(empty($cat['title']))? 'Add New Product Type' : 'Edit Product Type - '.$cat['title'] ;?></strong>
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
                <input type="text" name="title" <?= (!empty($cat['title'])) ? 'value="' . $cat['title'] . '"' : '' ?>>
            </div>
            <br class="clear">
            
            <label>ABBR</label>
            <div class="form_input">
                <input type="text" name="abbr" <?= (!empty($cat['abbr'])) ? 'value="' . $cat['abbr'] . '"' : '' ?>>
            </div>
            <br class="clear">

            <label>Active?</label>
            <div class="form_input">
                <select name="active">
                    <option value="1">Yes</option>
                    <option value="0" <?= (!empty($cat) && $cat['active']==0) ? 'selected' : '' ?>>No</option>
                </select>
            </div>
        </li>
        <li>
            <input class="button_small whitishBtn" type="submit" value="Save" name="add_cat" style="margin-left: 20px">
            <a href="/admin/print/category" class="button_small whitishBtn">Cancel</a>
        </li>
    </ul>
</form>