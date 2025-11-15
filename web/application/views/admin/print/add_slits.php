<strong><?=(empty($slit['name']))? 'Add New Slits' : 'Edit Slits - '.$slit['name'] ;?></strong>
<br><br>

<?php
if(@$_SERVER['HTTP_REFERER'] == 'http://'.$_SERVER['SERVER_NAME'].@$_SERVER['REQUEST_URI'] && !empty($_POST['name'])){ ?>
<div class="msgbar msg_Success hide_onC">
    <span class="iconsweet">=</span><p>Information Saved</p>
</div>
<?php }?>

<form method="POST" enctype="multipart/form-data">
    <ul class="form_fields_container" style="width: 800px">
        <li>
            <label>Name:</label>
            <div class="form_input">
                <input type="text" name="name" <?= (!empty($slit['name'])) ? 'value="' . $slit['name'] . '"' : '' ?>>
            </div>
        <br class="clear">
            <label>Preview:<br>
                <small>244x198 px</small>
            </label>
            <div class="form_input">
                <?php
                if(is_file(APPPATH.'files/print/slits/'.@$slit['id'].'.jpg')){
                    ?><img src="/files/print/slits/<?=$slit['id']?>.jpg" width="150"><br><?php
                }
                ?>
                <input type="file" name="preview">
            </div>
        <br class="clear">
            <label>Type:</label>
            <div class="form_input">
                <select name="type">
                    <option value="vetical">Vetical</option>
                    <option value="horizontal" <?php if(@$slit['type']=='horizontal')echo'selected="selected"'?>>Horizontal</option>
                    <option value="universal" <?php if(@$slit['type']=='universal')echo'selected="selected"'?>>Universal</option>
                </select>
            </div>
        <br class="clear">
            <label>Description:</label>
            <div class="form_input">
                <textarea name="description"><?=@$slit['description']?></textarea>
            </div>
        </li>
        <li>
            <input class="button_small whitishBtn" type="submit" value="Save" name="save_slits" style="margin-left: 20px">
            <a href="/admin/print/slits" class="button_small whitishBtn">Cancel</a>
        </li>
    </ul>
</form>