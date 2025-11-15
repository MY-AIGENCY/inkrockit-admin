<?php

class Model_User extends Model{
    
    /*
     * Check registration form
     * @param (-) &$message: return message
     */
    public function check_registration_form(&$message){
        $post = Request::initial()->post();
        $on_empty = array('login'=>'Username','pass'=>'Password','pass2'=>'Retry Password','email'=>'E-mail','first_name'=>'First Name','last_name'=>'Last Name');
        foreach($on_empty as $input=>$span){
            if(empty($post[$input])){
                $message = 'Field "'.$span.'" is empty!';
                return false;
            }
        }
        if($post['pass2']!=$post['pass']){
            $message = 'Password not match!';
            return false;
        }
        if(!preg_match('/[a-z0-9_-]*@[a-z]*.[a-z]*/i', $post['email'])){
            $message = 'Incorrect email!';
            return false;
        }
        $user = DB::get('users', 'email='.$post['email']);
        if(!empty($user)){
            $message = 'This email is already registered in the site!';
            return false;
        }
        $message = Captcha::valid($post['captcha'])? '' : 'Incorrect Verification Code';
    }
    
    /*
     * Register user
     */
    public function user_register(){
        $post = Request::initial()->post();
        DB::sql('INSERT INTO users(login,password,email,first_name,last_name,group_id) VALUES("'.$post['login'].'",MD5("'.$post['pass'].'"),"'.$post['email'].'","'.$post['first_name'].'","'.$post['last_name'].'",1)');
    }
    
}
?>
