<h4>Adding a new Job</h4>
<hr>
<table width="80%">
    <tr>
        <td width="30%">Job Type:</td>
        <td>
            <select name="job_type">
                <option value="A">ART</option>
                <option value="" selected="true">PRINT</option>
                <option value="S">SAMPLE</option>
                <option value="W">WEB</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>Job#:</td>
        <td>
            
            <input type="text" name="job_abbr" maxlength="3" value="<?=$abbr['abbr']?>" <?php if(empty($abbr['new'])){
                echo 'readonly="true" disabled="true"';
            }?> style="width: 60px; <?php if(empty($abbr['new'])){
                echo 'background: rgba(0,0,0,0.1);';
            }?>">
            <span class="additional"></span>
            - <span class="type_job"></span><span class="num_job numeric"><?=$abbr['num']?></span>
            <input type="text" name="prefix" style="width: 40px; display: none">
            <br>
            <div class="exist_job_company error" style="margin-bottom: 10px"></div>
        </td>
    </tr>
    <tr>
        <td>Contact Person:</td>
        <td>
            <select name="job_user">
                <?php
                if(!empty($users)){
                    foreach($users as $val){
                        ?><option value="<?=$val['id']?>"><?=$val['first_name'].' '.$val['last_name']?></option><?php
                    }
                }
                ?>
            </select>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <input type="button" name="save_current_num" class="button_small greenishBtn" value="Save" data-cid="<?=$cid?>" data-new="<?=(empty($abbr['new']))? 0 : 1 ;?>" style="margin-left: 0">
             <input type="button" name="close_modal_window" class="button_small redishBtn" value="Cancel">
        </td>
    </tr>
</table>