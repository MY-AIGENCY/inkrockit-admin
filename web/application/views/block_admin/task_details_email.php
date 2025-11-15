<ul class="form_fields_container">
    <li>
        <label>Subject:</label><div class="form_input"><input type="text" name="mess_subject" value=""></div>
        <br class="clear">
        <label>Message:</label><div class="form_input"><textarea name="mess_message"></textarea></div>
        <br class="clear">
        <label>&nbsp;</label>
        <div class="form_input">
            <a style="margin-left: 0" class="whitishBtn button_small send_client_message" data-email="<?= $order['email'] ?>">Send</a>
        </div>
    </li>
</ul>