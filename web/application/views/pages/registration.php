<style>
    span.big{
        display: block;
        width:200px;
        float:left;
    }
    span.error{
        color:red;
    }
</style>
<?phpif(!empty($reg_ok)){ 
    echo 'You have successfully registered'; 
    } else {?>
<form method="post" >
    <span class="error"><?php if(!empty($message)) echo $message ?></span><br><br>
    <span>Fields with * are required.</span><br><br>
    <span class="big">Username*</span><input type="text" name="login" value="<?=(!empty($post['login']))?$post['login']:''?>"><br>
    <span class="big">Password*</span><input type="password" name="pass"><br>
    <span class="big">Repeat Password*</span><input type="password" name="pass2"><br>
    <span class="big">E-mail*</span><input type="text" name="email" value="<?=(!empty($post['email']))?$post['email']:''?>"><br>
    <span class="big">First Name*</span><input type="text" name="first_name" value="<?=(!empty($post['first_name']))?$post['first_name']:''?>"><br>
    <span class="big">Last Name*</span><input type="text" name="last_name" value="<?=(!empty($post['last_name']))?$post['last_name']:''?>"><br>
    <span class="big">Verification Code</span><?php echo $captcha; ?><br>
    <span class="big">Code*</span><input type="text" name="captcha"><br>
    <input type="submit" name="register" value="Register">
</form>
<?php } ?>