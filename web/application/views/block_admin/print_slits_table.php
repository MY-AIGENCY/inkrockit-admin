<?php
if (!empty($slits)) {
    ?>
    <table class="activity_datatable small_datatable slits_db" width="600" border="0" cellspacing="0" cellpadding="3" style="background: #FFF">
        <tr>
            <th width="84">Avaliable</th>
            <th>Name</th>
            <th width="100">Type</th>
            <th width="80">Default</th>
        </tr> 
        <?php
        foreach ($slits as $val) {
            ?>
            <tr>
                <td><input type="checkbox" name="slits_aval[<?=$type?>][<?=$num?>][]" <?php if(!empty($aval) && in_array($val['id'], $aval)) echo 'checked="checked"'?> value="<?= $val['id'] ?>"></td>
                <td><?= $val['name'] ?></td>
                <td><?= $val['type'] ?></td>
                <td>
                    <input type="radio" name="slits_default[<?=$type?>][<?=$num?>]"  <?php if(!empty($def) && $val['id']==$def) echo 'checked="checked"'?> value="<?= $val['id'] ?>">
                </td>
            </tr>
            <?php
        }
        ?></table><?php
}else {
    ?><em>*No slits in Database</em><?php
}
?>