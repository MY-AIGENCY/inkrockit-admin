<div class="mainPage">
    <div class="drop">
        <span class="drop_text">Drop the Files here</span>
        <div class="files_list"><?php
if (!empty($uploaded)) {
    foreach ($uploaded as $key=>$val) {
       ?>
            <div class="img_block">
                <small class="remove">remove</small> <small class="title"><?= $val ?></small>
                <?php /*<img src="/files/upload/<?= $val ?>" style="width: 100%"><br> */?>
            </div>
                    <?php
                    if(($key+1) % 7 ==0){
                        echo '<br class="clear">';
                    }
                }
            }
            ?></div>

        <br class="clear">
    </div>
    <div id="progress">
        <div class="bar" style="width: 0%;"></div>
    </div>
    <div class="proc"></div>
    <br class="clear">

    <input id="fileupload" type="file" name="file" multiple
           data-url="/upload/upload"
           data-sequential-uploads="true"
           data-form-data='{"script": "true"}'>
</div>