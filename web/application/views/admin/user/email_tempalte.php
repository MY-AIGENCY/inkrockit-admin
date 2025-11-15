<form method="POST">
    <h3>Request email template</h3><br>
    %name% - User name<br>
    %company% - Company<br>

    <br>
    <textarea name="request_email_template" class="tinimce" style="width: 98%;height: 400px;"><?= $email_template['val'] ?></textarea>
    <br>
    <input type="submit" value="Save" class="dblueBtn button_small"/>
</form> 