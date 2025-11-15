<?php
$job_data = explode('-', $job['job_id']);
$type = substr($job_data[1], 0, 1);
$job_type = '';
if(in_array($type, array('A','S','W'))){
    $job_type = $type;
    $job_data[1] = substr($job_data[1], 1);
}
?>
<h4>Editing Job</h4>
<hr>

<table width="80%">
    <tr>
        <td width="30%">Job Type:</td>
        <td>
            <select name="job_type">
                <option value="A" <?php if($job_type=='A') echo 'selected="true"'?>>ART</option>
                <option value="" <?php if(empty($job_type)) echo 'selected="true"'?>>PRINT</option>
                <option value="S" <?php if($job_type=='S') echo 'selected="true"'?>>SAMPLE</option>
                <option value="W" <?php if($job_type=='W') echo 'selected="true"'?>>WEB</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>Job#:</td>
        <td>
            
            <input type="text" name="edit_job_abbr" value="<?=$job_data[0]?>" readonly="true" disabled="true" 
                   style="width: 60px; background: rgba(0,0,0,0.1);">
            - <span class="type_job"><?=$job_type?></span>
            <input name="edit_num_job" type="text" class="numeric" style="width: 65px" value="<?=$job_data[1]?>" data-def="<?=$job_data[1]?>">
            <br>
        </td>
    </tr>
    <tr>
        <td>Contact Person:</td>
        <td>
            <select name="job_user">
                <?php
                if(!empty($users)){
                    foreach($users as $val){
                        ?><option value="<?=$val['id']?>" <?php if($val['id']==$job['user_id']) echo 'selected="selected"'?>><?=$val['first_name'].' '.$val['last_name']?></option><?php
                    }
                }
                ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <div class="error edit_message_error"></div>
        </td>
        <td>
            <input type="button" name="edit_current_num" class="button_small greenishBtn" value="Save" data-cid="<?=$cid?>" data-job_id="<?=$job['id']?>" style="margin-left: 0">
            <input type="button" name="close_modal_window" class="button_small redishBtn" value="Cancel">
        </td>
    </tr>
</table>
