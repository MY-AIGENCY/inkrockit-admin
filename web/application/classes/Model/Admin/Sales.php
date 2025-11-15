<?php

class Model_Admin_Sales extends Model_Sales {
    /*
     * Get all credit cards for user
     * @param (int) $cid: company id
     * @return (array) all cards details
     */

    public function getCreditCards($uid) {
        return DB::sql('SELECT credit_card.*, users.first_name, users.last_name FROM credit_card 
            LEFT JOIN users ON users.id=credit_card.user_id
            WHERE user_id=:uid', array(':uid' => $uid));
    }

    public function getOneCreditCard($id) {
        return DB::sql_row('SELECT * FROM credit_card WHERE id=:id', array(':id' => $id));
    }

    public function getCreditCardsJob($job_id) {
        return DB::sql('SELECT * FROM credit_card WHERE user_id=(SELECT user_id FROM user_jobs WHERE id=:id)', array(':id' => $job_id));
    }

    public function getJobInfo($job_id) {
        return DB::sql_row('SELECT * FROM user_jobs WHERE id=:id', array(':id' => $job_id));
    }

    /*
     * Get one credit card data by ID
     * @return (array) card details
     */

    public function getCreditCard($id) {
        return DB::sql_row('SELECT * FROM credit_card WHERE id=:id', array(':id' => $id));
    }

    /*
     * Get Credit Card ID from number
     * @param (string) $num: card number
     * @return (string) card id
     */

    public function getCreditCardId($num) {
        $rez = DB::sql_row('SELECT id FROM credit_card WHERE card_number=:card_number', array(':card_number' => $num));
        return $rez['id'];
    }

    /*
     * Add credit card for user
     * @return (array) $rez: inserted card data
     */

    public function addCreditCard() {
        $post = Request::initial()->post();

        $result_data = array();
        $arr = explode('&', $post['data']);
        if (!empty($arr)) {
            foreach ($arr as $val) {
                $values = explode('=', $val);
                $result_data[$values[0]] = urldecode($values[1]);
            }
        }


        $user_job = DB::sql_row('SELECT user_id FROM user_jobs WHERE id=:id', array(':id' => $post['job_id']));
        $uid = $user_job['user_id'];

        $def_card = ($post['def_card']) ? 1 : 0;
        if (!empty($def_card)) {
            DB::sql('UPDATE credit_card SET `default`=0 WHERE user_id=:uid', array(':uid' => $uid));
        }
        $r = DB::sql('INSERT INTO credit_card (user_id, `title`, card_number, ccv, exp_date, full_card_name, full_user_name, billing_id, `default`, view_as,
            bill_company, bill_fname, bill_lname, bill_address, bill_address2, bill_city, bill_state, bill_suite, bill_zip, bill_country, bill_email, bill_phone, bill_phone_ext) 
            VALUES (:user_id, :title, :card_number, :ccv, :exp_date, :full_card_name, :full_user_name, :billing_id, :default, :view_as,
            :bill_company, :bill_fname, :bill_lname, :bill_address, :bill_address2, :bill_city, :bill_state, :bill_suite, :bill_zip, :bill_country, :bill_email, :bill_phone, :bill_phone_ext)', array(
                    ':user_id' => $uid, ':title' => $result_data['card_description'], ':card_number' => $post['card_number'],
                    ':ccv' => $result_data['cc_ccv'], ':exp_date' => $result_data['cc_date'], ':full_card_name' => $result_data['full_card_name'],
                    ':full_user_name' => $result_data['full_user_name'], ':billing_id' => @$post['autofill_billing'], ':default' => $def_card, ':view_as' => $post['view_as'],
                    ':bill_company' => $result_data['billing_company'], ':bill_fname' => $result_data['billing_fname'], ':bill_lname' => $result_data['billing_lname'], ':bill_address' => $result_data['billing_address'],
                    ':bill_address2' => $result_data['billing_address2'], ':bill_city' => $result_data['billing_city'], ':bill_state' => $result_data['billing_state'], ':bill_zip' => $result_data['billing_zip'],
                    ':bill_country' => $result_data['billing_country'], ':bill_email' => $result_data['billing_email'], ':bill_phone' => $result_data['billing_phone'], ':bill_phone_ext' => $result_data['billing_phone_ext'],
                    ':bill_suite' => $result_data['billing_suite']));

        $rez['id'] = $r[0];

        //add event
        Model::factory('Admin_Event')->add_event('add_creditcard', $rez['id'], "");

        $rez['card_number'] = $result_data['billing_fname'] . ' ' . $result_data['billing_lname'] . ' - ';
        if (!empty($result_data['card_description'])) {
            $rez['card_number'] .= $result_data['card_description'] . ' - ';
        }
        $rez['card_number'] .= ' ' . substr($post['card_number'], -4);
        return $rez;
    }

    /*
     * Delete user credit card by ID
     * @return 1
     */

    public function deleteCreditCard() {
        $post = Request::initial()->post();
        $card_data = DB::sql_row('SELECT * FROM credit_card WHERE id=:id', array(':id' => $post['id']));
        DB::sql('DELETE FROM credit_card WHERE id=:id', array(':id' => $post['id']));

        //add event
        Model::factory('Admin_Event')->add_event('creditcard_removed', $post['id'], serialize($card_data));
        return 1;
    }

    /*
     * First Data payment from customer (SALE)
     * @param (array)  $result_data: all data array from payment form
     * @return (array) $rez: payment result
     */

    public function cardPayment($result_data) {
        $post = Request::initial()->post();
        $rez = array();

        if (!empty($result_data)) {

            $result_data['user_type'] = $post['user_type'];
            $result_data['client_id'] = $post['client_id'];
            $result_data['request_id'] = $post['request_id'];
            $result_data['card'] = DB::sql_row('SELECT * FROM credit_card WHERE id=:id', array(':id' => $post['card_id']));
            if (!empty($result_data['card']['billing_id'])) {
                $result_data['card_billing'] = DB::sql_row('SELECT * FROM credit_card_billing WHERE id=:id', array(':id' => $result_data['card']['billing_id']));
            } else {
                $result_data['card_billing']['first_name'] = $result_data['card']['bill_fname'];
                $result_data['card_billing']['last_name'] = $result_data['card']['bill_lname'];
                $result_data['card_billing']['company'] = $result_data['card']['bill_company'];
                $result_data['card_billing']['email'] = $result_data['card']['bill_email'];
                $result_data['card_billing']['address'] = $result_data['card']['bill_address'];
                $result_data['card_billing']['address2'] = $result_data['card']['bill_address2'];
                $result_data['card_billing']['city'] = $result_data['card']['bill_city'];
                $result_data['card_billing']['state'] = $result_data['card']['bill_state'];
                $result_data['card_billing']['country'] = $result_data['card']['bill_country'];
                $result_data['card_billing']['zip'] = $result_data['card']['bill_zip'];
                $result_data['card_billing']['phone'] = $result_data['card']['bill_phone'];
                $result_data['card_billing']['phone_ext'] = $result_data['card']['bill_phone_ext'];
            }

            $phone = $result_data['card_billing']['phone'];
            if (!empty($result_data['card_billing']['phone_ext'])) {
                $phone.=$result_data['card_billing']['phone_ext'];
            }

            $card_num = str_replace(array(' ', '-', '.'), array(''), $result_data['card']['card_number']);
            $phone = str_replace(array('-', '(', ')'), array(''), $phone);
            $sphone = str_replace(array('-', '(', ')'), array(''), $result_data['shipping_email']);
            $summ = str_replace(array(','), '', $post['charge']);

            if (empty($summ) || $summ < 0) {
                return array('err' => 'Error in order amount');
            }

            $print_model = Model::factory('Admin_Print');
            $current_job = $print_model->getJob($result_data['payment_job_id']);

            $exp = explode('/', $result_data['card']['exp_date']);
            if (count($exp) != 2) {
                $rez = array('err' => 'Invalid Exp. Date field');
            } else {
                require_once VNDPATH . 'first_data/lphp.php';
                $mylphp = new lphp;
                # constants
                $summ = number_format(floatval($summ), 2, '.', '');
//                $myorder["result"] = "good"; //TEST MODE
                $myorder["host"] = "secure.linkpt.net";
                $myorder["port"] = "1129";
                $myorder["keyfile"] = APPPATH . "files/first_data/1001270436.pem"; # name and location of your certificate file 
                $myorder["configfile"] = "1001341195";        # store number
//                $myorder["configfile"] = "1001270436";        # store number
                # form data
                $myorder["ordertype"] = "SALE";
                $myorder['userid'] = $result_data['client_id']; #A user ID allowing merchants to track their customers
                $myorder["cardnumber"] = $card_num;
                $myorder["cardexpmonth"] = $exp[0];
                $myorder["cardexpyear"] = $exp[1];
                $result_data['oid'] = $current_job['job_id'];
                $result_data['transaction_num'] = (empty($result_data['transaction_num'])) ? 1 : $result_data['transaction_num'];
                $result_data['ponumber'] = $current_job['job_id'] . ' [TRAN: ' . $result_data['transaction_num'] . ']'; //The order ID of the transaction. Job ID
                $myorder['oid'] = time()+1;
                $result_data['chargetotal'] = $myorder["chargetotal"] = $myorder["subtotal"] = $summ;
                $myorder['comments'] = $result_data['description'];
                $myorder["debug"] = $myorder["debugging"] = "false";

                //user data
                //Shipping
                $myorder["sname"] = $result_data['shipping_fname'] . ' ' . $result_data['shipping_lname'];
                if (!empty($result_data['shipping_company'])) {
                    $myorder["scompany"] = $result_data['shipping_company'];
                }
                $myorder["saddress1"] = $result_data['shipping_address'];
                $myorder["saddress2"] = $result_data['shipping_address2'];

                $myorder["scity"] = $result_data['shipping_city'];
                $myorder["sstate"] = $result_data['shipping_state'];
                if (!empty($result_data['shipping_country'])) {
                    $myorder["scountry"] = $result_data['shipping_country'];
                }
                $myorder["sphone"] = $result_data['shipping_phone'];
                $myorder["semail"] = $sphone;
                $myorder["szip"] = $result_data['shipping_zip'];

                //Billing data
                $myorder["name"] = $result_data['card_billing']['first_name'] . ' ' . $result_data['card_billing']['last_name'];
                if (!empty($result_data['card_billing']['company'])) {
                    $myorder["company"] = $result_data['card_billing']['company'];
                }
                $myorder["phone"] = $phone;
                $myorder["email"] = $result_data['card_billing']['email'];
                $myorder["address1"] = $result_data['card_billing']['address'];
                $myorder["address2"] = $result_data['card_billing']['address2'];
                $myorder["city"] = $result_data['card_billing']['city'];
                $myorder["state"] = $result_data['card_billing']['state'];
                if (!empty($result_data['card_billing']['country'])) {
                    $myorder["country"] = $result_data['card_billing']['country'];
                }

                $myorder["zip"] = $result_data['card_billing']['zip'];
                $result = $mylphp->curl_process($myorder);    # use curl methods
                //add order total
                $job_order = DB::sql_row('SELECT order_total FROM user_jobs WHERE id=:id', array(':id' => $result_data['payment_job_id']));
                if (empty($job_order['order_total'])) {
                    DB::sql('UPDATE user_jobs SET order_total=:total, edg=:edg WHERE id=:id', array(':total' => $result_data['order_total'], ':id' => $result_data['payment_job_id'], ':edg' => $result_data['edg']));
                    //add to payment history ORDER CONFIRMED
                    DB::sql('INSERT INTO payment_history (job_id,`type`,user_type,`date`,client_id,`summ`,description,`total`) VALUES (:job_id,:type,:user_type,NOW(),:client_id,:summ,:description,:total)', array(
                        ':job_id' => $result_data['payment_job_id'], ':type' => 'order_confirmed', ':user_type' => 'sys', ':client_id' => $result_data['client_id'],
                        ':summ' => $result_data['order_total'], ':description' => 'ORDER TOTAL $' . $result_data['order_total'] . '. Description: ' . $result_data['description'], ':total' => $result_data['order_total']
                    ));
                }
                //Add transactions count
                DB::sql('UPDATE user_jobs SET order_counts=order_counts+1 WHERE id=:id', array(':id' => $result_data['payment_job_id']));

                if ($result["r_approved"] != "APPROVED") {    // transaction failed, print the reason
//                    $rez['err'] = "Status:" . $result['r_approved'] . "<br>
//                    Error:" . $result['r_error'];
                    //ADD Transaction Failed
                    $this->check_error($result['r_error'], $result_data, $exp, $rez['err']);

                    $card_num_short = substr($result_data['card']['card_number'], -4);
                    $note = 'Payment FAILED ' . $result_data['card']['title'] . ' xxxxxxxxx' . $card_num_short . '; AMOUNT: $' . $result_data['chargetotal'] . '; JOB ID: ' . $current_job['job_id'] . '; X:Payment Description: ' . $result_data['description'] . '; Payment error: ' . $rez['err'];
                    $r = DB::sql('INSERT INTO payment_history (job_id,`type`,user_type,`date`,client_id,summ,description, card_id, edg, transaction_code, procent, total) 
                        VALUES (:job_id,:type,:user_type,NOW(),:client_id,:summ,:description,:card_id, :edg, :transaction_code, :procent, :total)', array(':job_id' => $current_job['id'], ':type' => 'failed', ':user_type' => $result_data['user_type'], ':client_id' => $result_data['client_id'],
                                ':summ' => $result_data['chargetotal'], ':description' => $note, ':card_id' => $result_data['card']['id'], ':edg' => '', ':transaction_code' => '', ':procent' => '', ':total' => $result_data['chargetotal']));

                    //event
                    $job_order = DB::sql_row('SELECT * FROM user_jobs WHERE id=:id', array(':id' => $result_data['payment_job_id']));
                    $this->addSmallNotifications($result_data['request_id'], $current_job['id'], $job_order['company_id'], $r[0], 'order_failed', $note);
                } else { // success
//                  Transaction Code:" . $result['r_code'] . "<br><br>\n";
                    //Payment notification
                    $this->paymentNotifications($result_data, $result['r_code'], $summ);
                    $rez = array('ok' => 'Payment success!');
                }
            }
        }
        return $rez;
    }

    /*
     * First Data return payment to customer
     * @param (array)  $result_data: all data array from payment form
     * @return (array) $rez: payment result
     */

    public function cardReturnPayment() {
        $post = Request::initial()->post();

        //get payment data
        $payment = DB::sql_row('SELECT payment_history.*, credit_card.card_number, credit_card.exp_date, credit_card.ccv, credit_card.title'
                        . ' FROM payment_history '
                        . ' LEFT JOIN credit_card ON credit_card.id=payment_history.card_id '
                        . ' WHERE payment_history.id=:id', array(':id' => $post['id']));
        //check total payments
        $pays = DB::sql_row('SELECT SUM(summ) pay_summ FROM payment_history WHERE job_id=:job_id AND `type` NOT IN ("credit","change_total")', array(':job_id' => $payment['job_id']));
        $credits = DB::sql_row('SELECT SUM(summ) pay_summ FROM payment_history WHERE job_id=:job_id AND `type`="credit"', array(':job_id' => $payment['job_id']));
        $pay_true = $pays['pay_summ'] - ($credits['pay_summ'] + $post['amount']);

        if ($pay_true < 0) {
            return array('err' => 'You cant Credit this amount!');
        } elseif (empty($payment['card_number'])) {
            return array('err' => 'This credit card is removed!');
        }

        if (!empty($payment)) {

            $payment['request_id'] = $post['req_id'];
            $payment['user_type'] = $post['user_type'];

            //Billing and shipping
            $user = DB::sql_row('SELECT users.*, users_company.company FROM users '
                            . '  LEFT JOIN users_company ON users.company_id=users_company.id'
                            . ' WHERE users.id=:id', array(':id' => $payment['client_id']));
            //Job #
            $print_model = Model::factory('Admin_Print');
            $current_job = $print_model->getJob($payment['job_id']);

            $exp = explode('/', $payment['exp_date']);
            $amount = str_replace(array(','), '', $post['amount']);
            $amount = number_format(floatval($amount), 2, '.', '');
            $phone = str_replace(array('-', '(', ')'), array(''), $user['phone']);

//            $myorder["result"] = "good"; //TEST MODE
            $myorder["host"] = "secure.linkpt.net";
            $myorder["port"] = "1129";
            $myorder["keyfile"] = APPPATH . "files/first_data/1001270436.pem"; # name and location of your certificate file 
//            $myorder["configfile"] = "1001270436"; # store number
            $myorder["configfile"] = "1001341195"; # store number
            # form data
            $myorder["ordertype"] = "CREDIT";
            $myorder["oid"] = $payment['transaction_code']; //sale order ID
            $myorder["cardnumber"] = $payment['card_number'];
            $myorder["cardexpmonth"] = @$exp[0];
            $myorder["cardexpyear"] = @$exp[1];
            $result_data['oid'] = $myorder["chargetotal"] = $amount;
            $myorder["debug"] = $myorder["debugging"] = "false";

            $myorder["name"] = $myorder["sname"] = $user['first_name'] . ' ' . $user['last_name'];
            $myorder["company"] = $myorder["scompany"] = $user['company'];
            $myorder["address1"] = $myorder["saddress1"] = $user['street'];
            $myorder["city"] = $myorder["scity"] = $user['city'];
            $myorder["state"] = $myorder["sstate"] = $user['state'];
            if (!empty($user['country'])) {
                $myorder["country"] = $myorder["scountry"] = $user['country'];
            }
            $myorder["zip"] = $myorder["szip"] = $user['zipcode'];
            $myorder["sphone"] = $myorder["phone"] = $phone;
            $myorder["semail"] = $myorder["email"] = $user['email'];

            require_once VNDPATH . 'first_data/lphp.php';
            $mylphp = new lphp;
            $result = $mylphp->curl_process($myorder);

            if ($result["r_approved"] != "APPROVED") {
                // transaction failed, print the reason
//                    $rez['err'] = "Status:" . $result['r_approved'] . "<br>
//                        Error:" . $result['r_error'];
                $this->check_error($result['r_error'], $result_data, $exp, $rez['err']);
            } else {
                // success
                $myorder['title'] = $payment['title'];
                $this->creditNotifications($myorder, $result['r_ordernum'], $post['note'], $current_job, $payment);
                $rez = array('ok' => 'Payment success!');
            }
        } else {
            $rez['err'] = 'Original payment not found';
        }
        return $rez;
    }

    private function check_error($result, $result_data, $exp, &$err) {
        if (strpos($result, 'SGS-000001') !== FALSE) { //D:Declined:X:
            $err = 'The transaction was actually declined by the issuing credit card bank.';
        } elseif (strpos($result, 'SGS-000002') !== FALSE) { //R:Referral (call voice center)
            $err = 'The transaction was actually declined by the issuing credit card bank. The merchant needs to call for a voice authorization and then manually enter the transaction.';
        } elseif (strpos($result, 'SGS-020005') !== FALSE) { //Error (Merchant config file is missing, empty or cannot be read).
            $err = 'You are trying to use your live account with the staging server. Make sure you entered the correct store number. ';
        } elseif (strpos($result, 'SGS-020006') !== FALSE) { //Please contact merchant services.
            $err = 'Make sure you entered the correct store number. This error normally occurs when contact information is updated with your merchant bank. ';
        } elseif (strpos($result, 'SGS-020003') !== FALSE) { //Invalid XML
            //Make sure the amount for chargetotal is not blank.
            if (empty($result_data['oid'])) {
                $err = 'Make sure the amount for chargetotal is not blank.';
            }
            //Make sure expiration year is only 2 digits
            if (length(@$exp[1]) !== 2) {
                $err = 'Make sure expiration year is only 2 digits.';
            }
            if (empty($exp[1])) {
                $err = 'Exp. date is empty!';
            }
            //Make sure there is no dollar sign for the amount.
            if (strpos($result_data['oid'], '$') !== FALSE) {
                $err = 'Make sure there is no dollar sign for the amount.';
            }
            //Make sure there are no commas in the amount for chargetotal
            if (strpos($result_data['oid'], ',') !== FALSE) {
                $err = 'Make sure there are no commas in the amount for chargetotal.';
            }
            //Make sure there are no symbols like an ampersand, apostrophe, or letters with accents
            if (empty($err)) {
                $err = 'Make sure there are no symbols like an ampersand, apostrophe, or letters with accents';
            }
        } elseif (strpos($result, 'SGS-020003') !== FALSE) { //Invalid XML - invalid tag installments
            $err = 'At the moment you can only set installments from 1-99 or -1 for an infinite amount of times.';
        } elseif (strpos($result, 'SGS-002200') !== FALSE) { //There was a gateway configuration error
            $err = 'The gateway is experiencing problems.';
        } elseif (strpos($result, 'SGS-002300') !== FALSE) { //No credit card expiration year provided
            $err = 'The credit card entered is expired or cancelled.';
        } elseif (strpos($result, 'SGS-002301') !== FALSE) { //Charge total must be the sum of subtotal, tax, vallue added tax, and shipping.
            $err = 'Make sure all the amounts add up correctly. Also make sure you round up to 2 decimal places.';
        } elseif (strpos($result, 'SGS-002303') !== FALSE) { //Invalid credit card number.
            $err = 'Make sure the correct amount of digits was entered for the credit card.';
        } elseif (strpos($result, 'SGS-002304') !== FALSE) { //Credit card is expired.
            $err = 'Make sure the expiration date has not passed.';
        } elseif (strpos($result, 'SGS-002000') !== FALSE) { //The server encountered an error: Unsupported credit card type.
            $err = 'Your merchant account may not be seutp for American Express or Discover. Please contact merchant services to verify this.';
        } elseif (strpos($result, 'SGS-002000') !== FALSE) { //The server encountered an error: General Processor Error.
            $err = 'The merchant account may be disabled in one of the databases with your merchat account provider. This is sometimes generated for a declined transaction.';
        } elseif (strpos($result, 'SGS-005999') !== FALSE) { //There was an unknown error in the database
            $err = 'You enter the wrong order number (OID) when trying to modify or cancel a periodic bill. Make sure the order number is for an active periodic bill.';
        } elseif (strpos($result, 'SGS-005005') !== FALSE) { //Duplicate transaction.
            $err = 'The default fraud settings do not allow the same amount on the same credit card to be submitted more than once within 10 minutes. You can change this setting on LinkPoint Central.';
        } elseif (strpos($result, 'SGS-005003') !== FALSE) { //The order already exists in the database.
            $err = 'The LinkPoint gateway will not allow the same order number to be submitted more than once.';
        } elseif (strpos($result, 'SGS-005002') !== FALSE) { //The merchant is not setup to support the requested service.
            $err = 'There maybe a problem with the credit card entered. Verify the credit card number with the customer.';
        } elseif (strpos($result, 'SGS-005002') !== FALSE) { //No approved authorization found
            $err = 'Either no order number or the incorrect one was specified when trying to do a postauth.';
        } elseif (strpos($result, 'SGS-005002') !== FALSE) { //No transaction to void found
            $err = 'Either no order number or the incorrect one was specified when trying to do a void.';
        } elseif (strpos($result, 'SGS-005000') !== FALSE) { //The server encountered a database error.
            $err = 'Make sure you have not exceeded the character limit for any of the fields. The "items", "description" field has a limit of 120 characters.';
        } elseif (strpos($result, 'SGS-011202') !== FALSE) { //Not authorized to run a Credit Transaction.
            $err = 'You need to contact LinkPoint/Yourpay support to have the credit option enabled on your account.';
        } elseif (strpos($result, 'SGS-020011') !== FALSE) { //Creditcard or check information is required.
            $err = 'When modifying a periodic bill the credit card information must be included.';
        } elseif (strpos($result, 'SGS-020013') !== FALSE) { //Unexpected AuthService Response.
            $err = 'The gateway may possibly be experiencing problems.';
        } else {
            $err = 'The gateway may possibly be experiencing problems!';
        }
    }

    /*
     * Add payment notes and history
     * @param (array)  $result_data: all data array from payment form
     * @param (string) $result_id: transaction id from First Data
     * @param (float)  $summ: payment summ
     * @param (int)    $job: job ID, if not set - will get it from $result_data['payment_job_id'] (job title)
     */

    private function paymentNotifications($result_data, $result_id, $summ) {
        $cookies = new Cookie();
        $admin = $cookies->get('admin_user');
        if (!empty($admin)) {
            $admin = unserialize($admin);
        }
        $card_id = $result_data['card']['id'];
        $note_message = 'Payment success ' . $result_data['card']['title'] . ' xxxxxxxxx' . substr($result_data['card']['card_number'], -4) . '; Summ: $' . $summ . '; Job ID: ' . $result_data['ponumber'] . '; Transaction Code: ' . $result_id . '; ' . $result_data['description'];

        //get company
        $user = DB::sql_row('SELECT company_id FROM users WHERE id=:id', array(':id' => $result_data['client_id']));

        //add notes
        DB::sql('INSERT INTO request_notes (request_id,`text`,date,type_user,author_id,job_id, `type`, company_id) VALUES (:request_id,:text,NOW(),"sys",:author_id,:job_id, :type, :company_id)', array(
            ':job_id' => $result_data['payment_job_id'], ':text' => $note_message,
            ':author_id' => $admin['id'], ':request_id' => $result_data['request_id'], ':type' => 'payment', ':company_id' => $user['company_id']
        ));
        //add payment history
        $r = DB::sql('INSERT INTO payment_history (job_id,`type`,user_type,`date`,client_id,summ,description, card_id, edg, transaction_code, procent, total) 
                        VALUES (:job_id,:type,:user_type,NOW(),:client_id,:summ,:description,:card_id, :edg, :transaction_code, :procent, :total)', array(':job_id' => $result_data['payment_job_id'], ':type' => 'payment', ':user_type' => $result_data['user_type'], ':client_id' => $result_data['client_id'],
                    ':summ' => $summ, ':description' => $note_message, ':card_id' => $card_id, ':edg' => $result_data['edg'], ':transaction_code' => $result_id, ':procent' => @$result_data['payment_type'], ':total' => $result_data['order_total']));
        $pay_id = $r[0];
        DB::sql('UPDATE user_jobs SET order_counts=order_counts+1, payments=payments+'.$summ.' WHERE id=:id', array(':id' => $result_data['payment_job_id']));
        //add event
        Model::factory('Admin_Event')->add_event('card_payment', $pay_id, $note_message);
    }

    private function addSmallNotifications($req_id, $job_id, $company_id, $pay_id, $type, $note_message) {
        $cookies = new Cookie();
        $admin = $cookies->get('admin_user');
        if (!empty($admin)) {
            $admin = unserialize($admin);
        }
        //add notes
        DB::sql('INSERT INTO request_notes (request_id,`text`,date,type_user,author_id,job_id, `type`, company_id) VALUES (:request_id,:text,NOW(),"sys",:author_id,:job_id, :type, :company_id)', array(
            ':job_id' => $job_id, ':text' => $note_message,
            ':author_id' => $admin['id'], ':request_id' => $req_id, ':type' => 'payment', ':company_id' => $company_id
        ));
        //add event
        Model::factory('Admin_Event')->add_event($type, $pay_id, $note_message);
    }

    private function creditNotifications($order, $result, $note, $current_job, $payment) {
        $cookies = new Cookie();
        $admin = $cookies->get('admin_user');
        if (!empty($admin)) {
            $admin = unserialize($admin);
        }
        $note_message = 'Credit Return ' . $order['title'] . ' xxxxxxxxx' . substr($order['cardnumber'], -4) . '; AMOUNT: $' . $order['chargetotal'] . '; JOB ID: ' . $current_job['job_id'] . '; X:Payment Description: ' . $note;
        //add notes
        DB::sql('INSERT INTO request_notes (request_id,`text`,date,type_user,author_id,job_id, `type`, company_id) VALUES (:request_id,:text,NOW(),"sys",:author_id,:job_id, :type, :company_id)', array(
            ':job_id' => $current_job['id'], ':text' => $note_message,
            ':author_id' => $admin['id'], ':request_id' => $payment['request_id'], ':type' => 'credit', ':company_id' => $current_job['company_id']
        ));
        //add payment history
        $r = DB::sql('INSERT INTO payment_history (job_id,`type`,user_type,`date`,client_id,summ,description, card_id, edg, transaction_code, procent, total) 
                        VALUES (:job_id,:type,:user_type,NOW(),:client_id,:summ,:description,:card_id, :edg, :transaction_code, :procent, :total)', array(':job_id' => $current_job['id'], ':type' => 'credit', ':user_type' => $payment['user_type'], ':client_id' => $payment['client_id'],
                    ':summ' => $order['chargetotal'], ':description' => $note_message, ':card_id' => $payment['card_id'], ':edg' => '', ':transaction_code' => $result, ':procent' => '', ':total' => $order['chargetotal']));
        DB::sql('UPDATE user_jobs SET order_counts=order_counts+1, payments=payments-'.$order['chargetotal'].' WHERE id=:id', array(':id' => $current_job['id']));
        $pay_id = $r[0];
        //add event
        Model::factory('Admin_Event')->add_event('card_payment', $pay_id, $note_message);
    }

    public function card_jobs_list($id_list) {
        $all = array();
        foreach ($id_list as $v) {
            $rez = DB::sql('SELECT user_jobs.id, user_jobs.job_id FROM payment_history 
                LEFT JOIN user_jobs ON user_jobs.id = payment_history.job_id
                WHERE card_id=:card_id', array(':card_id' => $v['id']));
            if (!empty($rez)) {
                foreach ($rez as $val) {
                    $all[$v['id']][$val['id']] = $val['job_id'];
                }
            }
        }
        return $all;
    }

    /*
     * Save changes to credit card
     * @return true
     */

    public function saveCardChanges() {
        $post = Request::initial()->post();
        $result_data = array();
        $arr = explode('&', $post['data']);
        if (!empty($arr)) {
            foreach ($arr as $val) {
                $values = explode('=', $val);
                $result_data[$values[0]] = urldecode($values[1]);
            }
        }
        //save new user info
        DB::sql('UPDATE credit_card SET title=:title, card_number=:card_number, ccv=:ccv, exp_date=:exp_date, full_card_name=:full_card_name,
                            full_user_name=:full_user_name, billing_id=:billing_id WHERE id=:id', array(
            ':title' => $result_data['card_description'], ':card_number' => $post['card_number'], ':ccv' => $result_data['cc_ccv'], ':exp_date' => $result_data['cc_date'],
            ':full_card_name' => $result_data['full_card_name'], ':full_user_name' => $result_data['full_user_name'], ':id' => $post['id'], ':billing_id' => @$post['autofill_billing']
        ));
        if (empty($post['autofill_billing'])) {
            //update billing
            DB::sql('UPDATE credit_card SET bill_company=:bill_company, bill_fname=:bill_fname, bill_lname=:bill_lname, bill_address=:bill_address, bill_address2=:bill_address2,  bill_suite=:bill_suite, '
                    . ' bill_city=:bill_city, bill_state=:bill_state, bill_zip=:bill_zip, bill_country=:bill_country, bill_email=:bill_email, bill_phone=:bill_phone, bill_phone_ext=:bill_phone_ext WHERE id=:id', array(
                ':id' => $post['id'],
                ':bill_company' => $result_data['billing_company'], ':bill_fname' => $result_data['billing_fname'], ':bill_lname' => $result_data['billing_lname'], ':bill_address' => $result_data['billing_address'],
                ':bill_address2' => $result_data['billing_address2'], ':bill_city' => $result_data['billing_city'], ':bill_state' => $result_data['billing_state'], ':bill_zip' => $result_data['billing_zip'],
                ':bill_country' => $result_data['billing_country'], ':bill_email' => $result_data['billing_email'], ':bill_phone' => $result_data['billing_phone'], ':bill_phone_ext' => $result_data['billing_phone_ext'],
                ':bill_suite' => $result_data['billing_suite']
            ));
        }

        if (!empty($post['autofill_billing'])) {
            $where_id = $post['autofill_billing'];
            DB::sql('UPDATE credit_card SET billing_id=:billing_id WHERE id=:id', array(':billing_id' => $where_id, ':id' => $post['id']));
        } else {
            $rez_billing = DB::sql_row('SELECT billing_id FROM credit_card WHERE id=:id', array(':id' => $post['id']));
            $where_id = $rez_billing['billing_id'];
        }

        if (!empty($result_data['set_default'])) {
            $card = DB::sql_row('SELECT * FROM credit_card WHERE id=:id', array(':id' => $post['id']));
            DB::sql('UPDATE credit_card SET `default`=0 WHERE user_id=:uid', array(':uid' => $card['user_id']));
            DB::sql('UPDATE credit_card SET `default`=1 WHERE id=:id', array(':id' => $post['id']));
        }

        DB::sql('UPDATE credit_card_billing SET first_name=:first_name, last_name=:last_name, address=:address, address2=:address2, city=:city, state=:state, zip=:zip, country=:country, email=:email, phone=:phone, company=:company WHERE id=:id', array(
            ':first_name' => $result_data['billing_fname'], ':last_name' => $result_data['billing_lname'], ':address' => $result_data['billing_address'], ':address2' => $result_data['billing_address2'],
            ':city' => $result_data['billing_city'], ':state' => $result_data['billing_state'], ':zip' => $result_data['billing_zip'],
            ':country' => $result_data['billing_country'], ':email' => $result_data['billing_email'], ':phone' => $result_data['billing_phone'], ':company' => $result_data['billing_company'], ':id' => $where_id
        ));
        return 1;
    }

    /*
     * Add billing field for autofill
     * @return (int) billing id
     */

    public function addCardBilling() {
        $post = Request::initial()->post('data');
        //chack name
        $title = trim($post['title']);
        $user = DB::sql_row('SELECT * FROM user_jobs WHERE id=:id', array(':id' => $post['job_id']));
        $rez = DB::sql_row('SELECT COUNT(1) counts FROM credit_card_billing WHERE title=:title AND user_id=:uid', array(':title' => $title, ':uid' => $user['user_id']));
        if (empty($rez['counts']) && !empty($title)) {
            $r = DB::sql('INSERT INTO credit_card_billing (title,user_id,first_name, last_name,address,city,state,zip, country,email,phone,phone_ext,company,visible) 
            VALUES (:title,:user_id,:first_name,:last_name,:address,:city,:state,:zip,:country,:email,:phone,:phone_ext,:company,:visible)', array(
                        ':title' => $title, ':user_id' => $user['user_id'], ':first_name' => $post['fname'], ':last_name' => $post['lname'], ':address' => $post['address'], ':city' => $post['city'],
                        ':state' => $post['state'], ':zip' => $post['zip'], ':country' => $post['country'], ':email' => $post['email'], ':phone' => $post['phone'], ':phone_ext' => $post['phone_ext'], ':company' => $post['company'], ':visible' => 1
            ));
            return array('id' => $r[0]);
        } else {
            return array('err' => 1);
        }
    }

    /*
     * Update billing field for autofill
     * @return (int) billing id
     */

    public function updateCardBilling() {
        $posts = Request::initial()->post();
        $post = Request::initial()->post('data');

        if ($posts['val'] == 'default') {
            $user = DB::sql_row('SELECT user_id FROM user_jobs WHERE id=:id', array(':id' => $post['job_id']));
            $where = ' title="" AND visible=0 AND user_id="' . $user['user_id'] . '"';
        } else {
            $where = 'id=:id';
        }

        DB::sql('UPDATE credit_card_billing SET first_name=:first_name, last_name=:last_name, address=:address, city=:city, state=:state, zip=:zip, country=:country, email=:email, phone=:phone, phone_ext=:phone_ext, company=:company WHERE ' . $where, array(
            ':first_name' => $post['fname'], ':last_name' => $post['lname'], ':address' => $post['address'], ':city' => $post['city'],
            ':state' => $post['state'], ':zip' => $post['zip'], ':country' => $post['country'], ':email' => $post['email'], ':phone' => $post['phone'], ':phone_ext' => $post['phone_ext'], ':company' => $post['company'], ':id' => $posts['val']
        ));
    }

    /*
     * Get billing list for user
     * @param (int) $uid: user id
     * @return (array) billing list
     */

    public function getCardBilling($job_id) {
        $all = array();
        $rez = DB::sql('SELECT * FROM credit_card_billing WHERE 
            user_id=(SELECT user_id FROM user_jobs WHERE id=:id) ', array(':id' => $job_id));
        if (!empty($rez)) {
            foreach ($rez as $val) {
                $all[] = array('id' => $val['id'], 'title' => $val['company'] . '<br>' . $val['first_name'] . ' ' . $val['last_name'] . '<br>' . $val['address'] . '<br>' . $val['city'] . ' ' . $val['zip'] . ' ' . $val['state']);
            }
        }
        return $all;
    }

    /*
     * Get billing details by id
     * @param (string) $id: billing id (if id=default: get user default billing details)
     * @return (array) with billing details
     */

    public function getCardBillingDetails($id) {
        if ($id == 'default' || $id == 0) {
            $post = Request::initial()->post();
            $user_job = DB::sql_row('SELECT user_id FROM user_jobs WHERE id=:id', array(':id' => $post['job_id']));
            $uid = $user_job['user_id'];

            $user_data = DB::sql_row('SELECT users.*, users_company.company FROM users 
                LEFT JOIN users_company ON users_company.id=users.company_id
                WHERE users.id=:id', array(':id' => $uid));
            if (!empty($user_data)) {
                return array(
                    'first_name' => $user_data['first_name'],
                    'last_name' => $user_data['last_name'],
                    'address' => $user_data['street'],
                    'city' => $user_data['city'],
                    'state' => $user_data['state'],
                    'zip' => $user_data['zipcode'],
                    'country' => $user_data['country'],
                    'email' => $user_data['email'],
                    'phone' => $user_data['phone'],
                    'phone_ext' => $user_data['phone_ext'],
                    'company' => $user_data['company']
                );
            }
        } else {
            return DB::sql_row('SELECT * FROM credit_card_billing WHERE id=:id', array(':id' => $id));
        }
    }

    /*
     * Remove payment note from history by id
     * @param (int) $id: note id
     * @param (int) $group_id: admin group id
     * @return true
     */

    public function removePaymentNote($id, $group_id) {
        $pay_info = DB::sql_row('SELECT * FROM payment_history WHERE id=:id', array(':id' => $id));
        if ($group_id == 6) {
            DB::sql('DELETE FROM payment_history WHERE id=:id', array(':id' => $id));
        } else {
            DB::sql('UPDATE payment_history SET removed=1 WHERE id=:id', array(':id' => $id));
        }

        //add event
        Model::factory('Admin_Event')->add_event('payment_removed', $id, serialize($pay_info));
        return true;
    }

    /*
     * Change default card billing
     */

    public function change_card_billing($id, $card) {
        DB::sql('UPDATE credit_card SET billing_id=:id WHERE id=:card', array(':id' => $id, ':card' => $card));
        return 1;
    }

    /*
     * Company credit cards
     */

    public function getCompanyCreditCards($cid) {
        return DB::sql('SELECT credit_card.*, users.first_name, users.last_name FROM credit_card 
            LEFT JOIN users ON users.id=credit_card.user_id
            WHERE user_id IN(SELECT id FROM users WHERE company_id=:cid)', array(':cid' => $cid));
    }

    public function get_card_shipping($id, $cid) {
        $rez = DB::sql('SELECT * FROM credit_card_shipping WHERE (company_id=:cid AND public=1)
                OR (user_id=(SELECT user_id FROM credit_card WHERE id=:id) AND public=0)', array(':id' => $id, ':cid' => $cid));
        $all = array();
        if (!empty($rez)) {
            $card = DB::sql_row('SELECT * FROM credit_card WHERE id=:id', array(':id' => $id));

            foreach ($rez as $val) {
                $all[] = array('id' => $val['id'], 'uid' => $val['user_id'], 'curr_uid' => $card['user_id'], 'title' => $val['first_name'] . ' ' . $val['last_name'] . ' (' . $val['company'] . ')<br>' . $val['city'] . ' ' . $val['state'] . '<br>' . $val['address']);
            }
        }
        return $all;
    }

    public function shipp_add_contact() {
        $post = Request::initial()->post('data');
        $public = (empty($post['public'])) ? 0 : 1;
        $user = DB::sql_row('SELECT user_id, company_id FROM user_jobs WHERE id=:id', array(':id' => $post['job_id']));
        $r = DB::sql('INSERT INTO credit_card_shipping (user_id, company_id, title, first_name, last_name, company, address, address2, suite, city, state, zip, country, phone, email, public) 
                    VALUES (:user_id, :company_id, :title, :first_name, :last_name, :company, :address, :address2, :suite, :city, :state, :zip, :country, :phone, :email, :public)', array(
                    ':user_id' => $user['user_id'], ':company_id' => $user['company_id'], ':title' => $post['title'], ':first_name' => $post['fname'], ':last_name' => $post['lname'], ':company' => $post['company'], ':address' => $post['address'],
                    ':address2' => $post['address2'], ':suite' => $post['suite'], ':city' => $post['city'], ':state' => $post['state'], ':zip' => $post['zip'],
                    ':country' => $post['country'], ':public' => $public, ':phone' => $post['phone'], ':email' => $post['email']
        ));
        return $r[0];
    }

    public function shipp_update_contact() {
        $post = Request::initial()->post('data');
        DB::sql('UPDATE credit_card_shipping SET first_name=:first_name, last_name=:last_name, company=:company, address=:address, 
            address2=:address2, suite=:suite, city=:city, state=:state, zip=:zip, country=:country, phone=:phone, email=:email WHERE id=:id', array(
            ':first_name' => $post['fname'], ':last_name' => $post['lname'], ':company' => $post['company'], ':address' => $post['address'],
            ':address2' => $post['address2'], ':suite' => $post['suite'], ':city' => $post['city'], ':state' => $post['state'],
            ':zip' => $post['zip'], ':country' => $post['country'], ':id' => $post['id'], ':phone' => $post['phone'], ':email' => $post['email']
        ));
    }

    /*
     * Validate Credit Card by number
     */

    public function validCreditcard($number) {
        $card_array = array('default' => array(
                'length' => '13,14,15,16,17,18,19',
                'prefix' => '',
                'luhn' => TRUE,
            ),
            'american express' => array(
                'length' => '15',
                'prefix' => '3[47]',
                'luhn' => TRUE,
            ),
            'diners club' => array(
                'length' => '14,16',
                'prefix' => '36|55|30[0-5]',
                'luhn' => TRUE,
            ),
            'discover' => array(
                'length' => '16',
                'prefix' => '6(?:5|011)',
                'luhn' => TRUE,
            ),
            'jcb' => array(
                'length' => '15,16',
                'prefix' => '3|1800|2131',
                'luhn' => TRUE,
            ),
            'maestro' => array(
                'length' => '16,18',
                'prefix' => '50(?:20|38)|6(?:304|759)',
                'luhn' => TRUE,
            ),
            'mastercard' => array(
                'length' => '16',
                'prefix' => '5[1-5]',
                'luhn' => TRUE,
            ),
            'visa' => array(
                'length' => '13,16',
                'prefix' => '4',
                'luhn' => TRUE,
            ),
        );
        if (($number = preg_replace('/\D+/', '', $number)) === '')
            return array('rez' => false);
        // Use the default type
        $type = 'default';
        $cards = $card_array;
        // Check card type
        $type = strtolower($type);
        if (!isset($cards[$type]))
            return array('rez' => false);
        // Check card number length
        $length = strlen($number);
        // Validate the card length by the card type
        if (!in_array($length, preg_split('/\D+/', $cards[$type]['length'])))
            return array('rez' => false);
        // Check card number prefix
        if (!preg_match('/^' . $cards[$type]['prefix'] . '/', $number))
            return array('rez' => false);
        // No Luhn check required
        return array('rez' => $this->luhn_ValidateCC($number));
    }

    protected function luhn_ValidateCC($number) {
        // Force the value to be a string as this method uses string functions.
        // Converting to an integer may pass PHP_INT_MAX and result in an error!
        $number = (string) $number;
        if (!ctype_digit($number)) {
            // Luhn can only be used on numbers!
            return FALSE;
        }
        // Check number length
        $length = strlen($number);
        // Checksum of the card number
        $checksum = 0;
        for ($i = $length - 1; $i >= 0; $i -= 2) {
            // Add up every 2nd digit, starting from the right
            $checksum += substr($number, $i, 1);
        }
        for ($i = $length - 2; $i >= 0; $i -= 2) {
            // Add up every 2nd digit doubled, starting from the right
            $double = substr($number, $i, 1) * 2;
            // Subtract 9 from the double where value is greater than 10
            $checksum += ($double >= 10) ? ($double - 9) : $double;
        }
        // If the checksum is a multiple of 10, the number is valid
        return ($checksum % 10 === 0);
    }

    public function set_card_default($id) {
        $user = DB::sql_row('SELECT user_id FROM credit_card WHERE id=:id', array(':id' => $id));
        DB::sql('UPDATE credit_card SET `default`=0 WHERE user_id=:uid', array(':uid' => $user['user_id']));
        DB::sql('UPDATE credit_card SET `default`=1 WHERE id=:id', array(':id' => $id));
    }

    public function change_order_total($admin_uid) {
        $post = Request::initial()->post();
        $order = DB::sql_row('SELECT order_total,user_id FROM user_jobs WHERE id=:id', array(':id' => $post['job_id']));
        DB::sql('UPDATE user_jobs SET order_total=:total WHERE id=:id', array(':total' => $post['summ'], ':id' => $post['job_id']));
        $note = 'Change order total from $' . $order['order_total'] . ' to $' . $post['summ'] . '. Description: ' . $post['text'];
        //add to payment history ORDER CONFIRMED
        if (empty($order['order_total'])) {
            $r = DB::sql('INSERT INTO payment_history (job_id,`type`,user_type,date,clien_id,summ,description,card_id,total) VALUES (:job_id,:type,:user_type,NOW(),:clien_id,:summ,:description,:card_id,:total)', array(
                        ':job_id' => $post['job_id'], ':type' => 'order_confirmed', ':user_type' => 'sys', ':clien_id' => $order['user_id'],
                        ':summ' => $order['order_total'], ':description' => 'Order confirmed', ':card_id' => '', ':total' => $order['order_total']
            ));
            //add event
            $this->addSmallNotifications($post['req_id'], $post['job_id'], $post['comp_id'], $r[0], 'order_confirmed', $note);
        }

        $r = DB::sql('INSERT INTO payment_history (job_id,`type`,user_type,`date`,client_id,summ,description) VALUES (:job_id,"change_total","A",NOW(),:client_id,:summ,:description)', array(
                    ':job_id' => $post['job_id'], ':client_id' => $order['user_id'], ':description' => $note, ':summ' => $post['summ']));
        DB::sql('UPDATE user_jobs SET order_counts=order_counts+1 WHERE id=:id', array(':id' => $post['job_id']));
        //add event
        $this->addSmallNotifications($post['req_id'], $post['job_id'], $post['comp_id'], $r[0], 'order_modif', $note);
    }

    public function eye_company_change($id, $type, $uid, $req_id) {
        if (!empty($type)) {
            DB::sql('DELETE FROM eye_user_company WHERE company_id=:company_id', array(':company_id' => $id));
            DB::sql('INSERT INTO eye_user_company (uid, company_id) VALUES (:uid, :company_id)', array(':uid' => $uid, ':company_id' => $id));
        } else {
            //check required
            $all_req = DB::sql('SELECT * FROM request_note_required WHERE status=0 GROUP BY note_id');
            if (!empty($all_req)) {
                $notes_id = array();
                foreach ($all_req as $val) {
                    $notes_id[] = $val['note_id'];
                }
                $comp = DB::sql('SELECT request_id FROM request_notes WHERE id IN(' . implode(',', $notes_id) . ') AND removed=0 GROUP BY request_id');
                if (!empty($comp)) {
                    foreach ($comp as $v) {
                        if ($v['request_id'] == $req_id) {
                            return 'err_reqired';
                        }
                    }
                }
            }
            DB::sql('DELETE FROM eye_user_company WHERE company_id=:company_id', array(':company_id' => $id));
        }
        return 1;
    }

    public function pay_invoice($pay, $myorder, $result) {
        $history = DB::sql('SELECT payment_history.*, DATE_FORMAT(payment_history.date, "%m/%d/%Y") date, users.first_name, users.last_name, credit_card.card_number '
                        . ' FROM payment_history '
                        . ' LEFT JOIN users ON users.id=payment_history.client_id'
                        . ' LEFT JOIN credit_card ON credit_card.id=payment_history.card_id'
                        . ' WHERE payment_history.job_id=:job', array(':job' => $pay['payment_job_id']));

        $job = DB::sql_row('SELECT * FROM user_jobs WHERE id=:id', array(':id' => $pay['payment_job_id']));

        $html = View::factory('payment_invoice', array('pay' => $pay, 'myorder' => $myorder, 'history' => $history, 'trans_id' => $result['r_code'], 'job' => $job));
        require DOCROOT . 'kernel/vendor/mpdf/mpdf.php';
        $mpdf = new mPDF('utf-8', 'A4', '', '');
        $mpdf->useOnlyCoreFonts = true;
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->charset_in = 'utf-8';

        $stylesheet = file_get_contents(APPPATH . 'views/css/normalize.css');
        $mpdf->WriteHTML($stylesheet, 1);
        $stylesheet = file_get_contents(APPPATH . 'views/css/admin/payment_invoice.css');
        $mpdf->WriteHTML($stylesheet, 1);

        $mpdf->list_indent_first_level = 1;
        $mpdf->WriteHTML($html, 2);
        $mpdf->Output(APPPATH . 'files/invoice/' . $pay['oid'] . ' [TRAN: ' . $pay['transaction_num'] . ']' . '.pdf', 'F');
    }

    public function addCardTransaction($admin_id) {
        $post = Request::initial()->post();

        if ($post['card'] == 'add') {
            $card_id = '';
            $add_card = $post['add_card_name'] . ' ' . $post['add_card_type'] . ' - X' . $post['last_digits'];
            $card_num = $post['last_digits'];
        } else {
            $card_id = $post['card'];
            $exist_card = $this->getOneCreditCard($card_id);
            $card_num = substr($exist_card['card_number'], -4);
            $add_card = '';
        }

        $date = explode('/', $post['date']);
        $date = $date[2] . '-' . $date[0] . '-' . $date[1] . ' 13:00:00';
        $user = DB::sql_row('SELECT user_id,company_id, job_id FROM user_jobs WHERE id=:id', array(':id' => $post['job_id']));


        //set order total from form?

        $job_order = DB::sql_row('SELECT order_total FROM user_jobs WHERE id=:id', array(':id' => $post['job_id']));
        if (empty($job_order['order_total'])) {
            $amount = (empty($post['order_total'])) ? $post['amount'] : $post['order_total'];

            DB::sql('UPDATE user_jobs SET order_total=:total, edg=:edg WHERE id=:id', array(':total' => $amount, ':id' => $post['job_id'], ':edg' => 0));
            //add to payment history ORDER CONFIRMED
            $note = 'ORDER TOTAL $' . $amount;
            $r = DB::sql('INSERT INTO payment_history (job_id,`type`,user_type,`date`,client_id,`summ`,description,`total`) VALUES (:job_id,:type,:user_type,:date,:client_id,:summ,:description,:total)', array(
                        ':job_id' => $post['job_id'], ':type' => 'order_confirmed', ':user_type' => 'sys', ':client_id' => $user['user_id'],
                        ':summ' => $amount, ':description' => $note, ':total' => $amount, ':date' => $date,
            ));
            //add event
            $this->addSmallNotifications($post['req_id'], $post['job_id'], $post['comp_id'], $r[0], 'order_confirmed', $note);
        }

        $r = DB::sql('INSERT INTO payment_history (job_id, `type`, user_type, `date`, client_id, summ, description, card_id, edg, transaction_code) '
                        . 'VALUES (:job_id, :type, :user_type, :date, :client_id, :summ, :description, :card_id, :edg, :transaction_code)', array(
                    ':job_id' => $post['job_id'], ':type' => 'manual_cc', ':user_type' => 'A', ':date' => $date, ':client_id' => $user['user_id'], ':summ' => $post['amount'], ':description' => $post['trans_note'] . ' ' . $add_card, ':card_id' => $card_id, ':edg' => 0, ':transaction_code' => ''
        ));
        DB::sql('UPDATE user_jobs SET order_counts=order_counts+1, payments=payments+'.$post['amount'].' WHERE id=:id', array(':id' => $post['job_id']));
        $note = 'Payment success xxxxxxxxx' . substr($card_num, -4) . '; AMOUNT: $' . $post['amount'] . '; Job ID: ' . $user['job_id'] . ';';
        //add event
        $this->addSmallNotifications($post['req_id'], $post['job_id'], $post['comp_id'], $r[0], 'card_payment', $note);
    }

    public function addCashTransaction($admin_id) {
        $post = Request::initial()->post();

        $date = explode('/', $post['date']);
        $date = $date[2] . '-' . $date[0] . '-' . $date[1] . ' 13:00:00';
        $user = DB::sql_row('SELECT user_id,company_id FROM user_jobs WHERE id=:id', array(':id' => $post['job_id']));

        $job_order = DB::sql_row('SELECT order_total FROM user_jobs WHERE id=:id', array(':id' => $post['job_id']));
        if (empty($job_order['order_total'])) {
            DB::sql('UPDATE user_jobs SET order_total=:total, edg=:edg WHERE id=:id', array(':total' => $post['amount'], ':id' => $post['job_id'], ':edg' => 0));
            //add to payment history ORDER CONFIRMED
            $note = 'ORDER TOTAL $' . $post['amount'];
            $r = DB::sql('INSERT INTO payment_history (job_id,`type`,user_type,`date`,client_id,`summ`,description,`total`) VALUES (:job_id,:type,:user_type,:date,:client_id,:summ,:description,:total)', array(
                        ':job_id' => $post['job_id'], ':type' => 'order_confirmed', ':user_type' => 'sys', ':client_id' => $user['user_id'],
                        ':summ' => $post['amount'], ':description' => $note, ':total' => $post['amount'], ':date' => $date,
            ));
            //add event
            $this->addSmallNotifications($post['req_id'], $post['job_id'], $post['comp_id'], $r[0], 'order_confirmed', $note);
        }

        $r = DB::sql('INSERT INTO payment_history (job_id, `type`, user_type, `date`, client_id, summ, description, card_id, edg, transaction_code) '
                        . 'VALUES (:job_id, :type, :user_type, :date, :client_id, :summ, :description, :card_id, :edg, :transaction_code)', array(
                    ':job_id' => $post['job_id'], ':type' => 'manual_cash', ':user_type' => 'A', ':date' => $date, ':client_id' => $user['user_id'], ':summ' => $post['amount'], ':description' => $post['note'], ':card_id' => '', ':edg' => 0, ':transaction_code' => ''
        ));
        DB::sql('UPDATE user_jobs SET order_counts=order_counts+1,payments=payments+'.$post['amount'].' WHERE id=:id', array(':id' => $post['job_id']));
        //add event
        $note = 'Payment success by Cash';
        $this->addSmallNotifications($post['req_id'], $post['job_id'], $post['comp_id'], $r[0], 'cash_payment', $note);
    }

    public function addCheckTransaction($admin_id) {
        $post = Request::initial()->post();

        $date = explode('/', $post['date']);
        $date = $date[2] . '-' . $date[0] . '-' . $date[1] . ' 13:00:00';
        $user = DB::sql_row('SELECT user_id,company_id FROM user_jobs WHERE id=:id', array(':id' => $post['job_id']));

        $job_order = DB::sql_row('SELECT order_total FROM user_jobs WHERE id=:id', array(':id' => $post['job_id']));
        if (empty($job_order['order_total'])) {
            DB::sql('UPDATE user_jobs SET order_total=:total, edg=:edg WHERE id=:id', array(':total' => $post['amount'], ':id' => $post['job_id'], ':edg' => 0));
            //add to payment history ORDER CONFIRMED
            $note = 'ORDER TOTAL $' . $post['amount'];
            $r = DB::sql('INSERT INTO payment_history (job_id,`type`,user_type,`date`,client_id,`summ`,description,`total`) VALUES (:job_id,:type,:user_type,:date,:client_id,:summ,:description,:total)', array(
                        ':job_id' => $post['job_id'], ':type' => 'order_confirmed', ':user_type' => 'sys', ':client_id' => $user['user_id'],
                        ':summ' => $post['amount'], ':description' => $note, ':total' => $post['amount'], ':date' => $date,
            ));
            //add event
            $this->addSmallNotifications($post['req_id'], $post['job_id'], $post['comp_id'], $r[0], 'order_confirmed', $note);
        }

        $r = DB::sql('INSERT INTO payment_history (job_id, `type`, user_type, `date`, client_id, summ, description, card_id, edg, transaction_code) '
                        . 'VALUES (:job_id, :type, :user_type, :date, :client_id, :summ, :description, :card_id, :edg, :transaction_code)', array(
                    ':job_id' => $post['job_id'], ':type' => 'manual_check', ':user_type' => 'A', ':date' => $date, ':client_id' => $user['user_id'], ':summ' => $post['amount'], ':description' => $post['note'] . ' Check# ' . $post['number'], ':card_id' => '', ':edg' => 0, ':transaction_code' => ''
        ));
        DB::sql('UPDATE user_jobs SET order_counts=order_counts+1, payments=payments+'.$post['amount'].' WHERE id=:id', array(':id' => $post['job_id']));
        $note = 'Payment success by Check# ' . $post['number'];
        $this->addSmallNotifications($post['req_id'], $post['job_id'], $post['comp_id'], $r[0], 'check_payment', $note);
    }

    public function addMiscTransaction($admin_id) {
        $post = Request::initial()->post();

        $date = explode('/', $post['date']);
        $date = $date[2] . '-' . $date[0] . '-' . $date[1] . ' 13:00:00';
        $user = DB::sql_row('SELECT user_id,company_id FROM user_jobs WHERE id=:id', array(':id' => $post['job_id']));

        $job_order = DB::sql_row('SELECT order_total FROM user_jobs WHERE id=:id', array(':id' => $post['job_id']));
        if (empty($job_order['order_total'])) {
            DB::sql('UPDATE user_jobs SET order_total=:total, edg=:edg WHERE id=:id', array(':total' => $post['amount'], ':id' => $post['job_id'], ':edg' => 0));
            //add to payment history ORDER CONFIRMED
            $note = 'ORDER TOTAL $' . $post['amount'];
            $r = DB::sql('INSERT INTO payment_history (job_id,`type`,user_type,`date`,client_id,`summ`,description,`total`) VALUES (:job_id,:type,:user_type,:date,:client_id,:summ,:description,:total)', array(
                        ':job_id' => $post['job_id'], ':type' => 'order_confirmed', ':user_type' => 'sys', ':client_id' => $user['user_id'],
                        ':summ' => $post['amount'], ':description' => $note, ':total' => $post['amount'], ':date' => $date,
            ));
            //add event
            $this->addSmallNotifications($post['req_id'], $post['job_id'], $post['comp_id'], $r[0], 'order_confirmed', $note);
        }

        $r = DB::sql('INSERT INTO payment_history (job_id, `type`, user_type, `date`, client_id, summ, description, card_id, edg, transaction_code) '
                        . 'VALUES (:job_id, :type, :user_type, :date, :client_id, :summ, :description, :card_id, :edg, :transaction_code)', array(
                    ':job_id' => $post['job_id'], ':type' => 'manual_misc', ':user_type' => 'A', ':date' => $date, ':client_id' => $user['user_id'], ':summ' => $post['amount'], ':description' => $post['note'], ':card_id' => '', ':edg' => 0, ':transaction_code' => ''
        ));
        DB::sql('UPDATE user_jobs SET order_counts=order_counts+1, payments=payments+'.$post['amount'].' WHERE id=:id', array(':id' => $post['job_id']));
        $note = 'Misc payment success';
        $this->addSmallNotifications($post['req_id'], $post['job_id'], $post['comp_id'], $r[0], 'misc_payment', $note);
    }

    public function addConfirmTransaction($admin_id) {
        $post = Request::initial()->post();

        $date = explode('/', $post['date']);
        $date = $date[2] . '-' . $date[0] . '-' . $date[1] . ' 01:00:00';

        $user = DB::sql_row('SELECT user_id,company_id FROM user_jobs WHERE id=:id', array(':id' => $post['job_id']));
        DB::sql('UPDATE user_jobs SET order_total=:total, edg=:edg WHERE id=:id', array(':total' => $post['amount'], ':id' => $post['job_id'], ':edg' => 0));
        $note = 'ORDER TOTAL $' . $post['amount'];
        $r = DB::sql('INSERT INTO payment_history (job_id,`type`,user_type,`date`,client_id,`summ`,description,`total`) VALUES (:job_id,:type,:user_type,:date,:client_id,:summ,:description,:total)', array(
                    ':job_id' => $post['job_id'], ':type' => 'order_confirmed', ':user_type' => 'sys', ':client_id' => $user['user_id'],
                    ':summ' => $post['amount'], ':description' => $note, ':total' => $post['amount'], ':date' => $date,
        ));

        //add event
        $this->addSmallNotifications($post['req_id'], $post['job_id'], $post['comp_id'], $r[0], 'order_confirmed', $note);
    }

    public function addFailedTransaction($admin_id) {
        $post = Request::initial()->post();

        $date = explode('/', $post['date']);
        $date = $date[2] . '-' . $date[0] . '-' . $date[1] . ' 01:00:00';

        $user = DB::sql_row('SELECT user_id,company_id, job_id FROM user_jobs WHERE id=:id', array(':id' => $post['job_id']));
        DB::sql('UPDATE user_jobs SET order_total=:total, edg=:edg WHERE id=:id', array(':total' => $post['amount'], ':id' => $post['job_id'], ':edg' => 0));
        $note = 'Payment FAILED; AMOUNT: $' . $post['amount'] . '; JOB ID: ' . $user['job_id'] . '; Payment Description: ' . $post['note'];
        $r = DB::sql('INSERT INTO payment_history (job_id,`type`,user_type,`date`,client_id,`summ`,description,`total`) VALUES (:job_id,:type,:user_type,:date,:client_id,:summ,:description,:total)', array(
                    ':job_id' => $post['job_id'], ':type' => 'failed', ':user_type' => 'sys', ':client_id' => $user['user_id'],
                    ':summ' => $post['amount'], ':description' => $note, ':total' => $post['amount'], ':date' => $date,
        ));

        //add event
        $this->addSmallNotifications($post['req_id'], $post['job_id'], $post['comp_id'], $r[0], 'failed', $note);
    }

    public function addCreditTransaction($admin_id) {
        $post = Request::initial()->post();

        $date = explode('/', $post['date']);
        $date = $date[2] . '-' . $date[0] . '-' . $date[1] . ' 01:00:00';

        $user = DB::sql_row('SELECT user_id,company_id, job_id FROM user_jobs WHERE id=:id', array(':id' => $post['job_id']));
        DB::sql('UPDATE user_jobs SET order_total=:total, edg=:edg, payments=payments-'.$post['amount'].' WHERE id=:id', array(':total' => $post['amount'], ':id' => $post['job_id'], ':edg' => 0));
        $note = 'Credit Return; AMOUNT: $' . $post['amount'] . '; JOB ID: ' . $user['job_id'] . '; Payment Description: ' . $post['note'];
        $r = DB::sql('INSERT INTO payment_history (job_id,`type`,user_type,`date`,client_id,`summ`,description,`total`) VALUES (:job_id,:type,:user_type,:date,:client_id,:summ,:description,:total)', array(
                    ':job_id' => $post['job_id'], ':type' => 'credit', ':user_type' => 'sys', ':client_id' => $user['user_id'],
                    ':summ' => $post['amount'], ':description' => $note, ':total' => $post['amount'], ':date' => $date,
        ));

        //add event
        $this->addSmallNotifications($post['req_id'], $post['job_id'], $post['comp_id'], $r[0], 'credit', $note);
    }

    public function updateViewAs($id, $val) {
        return DB::sql('UPDATE credit_card SET view_as=:as WHERE id=:id', array(':as' => $val, ':id' => $id));
    }

    public function getTransaction($id) {
        return DB::sql_row('SELECT * FROM payment_history WHERE id=:id', array(':id' => $id));
    }

    public function updateCardTransaction() {
        $post = Request::initial()->post();
        $date_tmp = explode('/', $post['date']);
        $date = $date_tmp[2] . '-' . $date_tmp[0] . '-' . $date_tmp[1];
        DB::sql('UPDATE payment_history SET card_id=0 WHERE id=:id', array(':id' => $post['id']));

        if ($post['type'] == 'manual_cc') {
            if (!empty($post['card'])) {
                if ($post['card'] == 'add') {
                    $card_id = '0';
                    $add_card = $post['add_card_name'] . ' ' . $post['add_card_type'] . ' - X' . $post['last_digits'];
                    $card_num = $post['last_digits'];
                    $post['trans_note'] .= ' Credit Card: X' . $add_card;
                } else {
                    DB::sql('UPDATE payment_history SET card_id=:card_id WHERE id=:id', array(':id' => $post['id'], ':card_id' => $post['card']));
                }
            }
        } elseif ($post['type'] == 'manual_check') {
            $post['trans_note'] .= ' Check#: ' . $post['check'];
        }

        $data = DB::sql_row('SELECT summ FROM payment_history WHERE id=:id', array(':id' => $post['id']));
        $change_summ = $post['amount'] - $data['summ'];
        if($change_summ>0){
            DB::sql('UPDATE user_jobs SET payments=payments-'.$change_summ.' WHERE id=:id', array(':id'=>$post['job']));
        }elseif($change_summ<0){
            DB::sql('UPDATE user_jobs SET payments=payments+'.$change_summ.' WHERE id=:id', array(':id'=>$post['job']));
        }
        DB::sql('UPDATE payment_history SET job_id=:job, date=:date, summ=:summ, description=:description, type=:type WHERE id=:id', array(':id' => $post['id'], ':job' => $post['job'], ':date' => $date, ':summ' => $post['amount'], ':description' => $post['trans_note'], ':type' => $post['type']));

        //add event
        $note = 'Update Transaction. Note: ' . $post['trans_note'];
        $this->addSmallNotifications($post['req_id'], $post['job'], $post['comp_id'], $post['id'], 'edit_transaction', $note);
    }

// 
    public function removeShippAddress($id) {
        DB::sql('DELETE FROM credit_card_shipping WHERE id=:id', array(':id' => $id));
    }

    public function removeBillAddress($id) {
        DB::sql('DELETE FROM credit_card_billing WHERE id=:id', array(':id' => $id));
    }

    public function redestributePayment() {
        $post = Request::initial()->post();

        $payment = DB::sql_row('SELECT * FROM payment_history WHERE id=:id', array(':id' => $post['base_id']));
        //get jobs
        $print_model = Model::factory('Admin_Print');
        $job1 = $print_model->getJob($post['job1']);
        $base_job = $print_model->getJob($payment['job_id']);
        $total = $post['summ1'] + $post['summ2'];

        $descr = $base_job['job_id'] . ' [' . $post['small_descr'] . '] - $' . number_format($total, 2) . ' => ' . $job1['job_id'] . ' [$' . number_format($post['summ1'], 2) . ']';
        if ($post['count_redistr'] == 2) {
            $job2 = $print_model->getJob($post['job2']);
            $descr.=' + ' . $job2['job_id'] . ' [$' . number_format($post['summ2'], 2) . ']';
        }

        //To JOB1
        //check if first payment, add order total
        $job_order = DB::sql_row('SELECT order_total FROM user_jobs WHERE id=:id', array(':id' => $post['job1']));
        if (empty($job_order['order_total'])) {
            DB::sql('UPDATE user_jobs SET order_total=:total, edg=:edg WHERE id=:id', array(':total' => 0, ':id' => $post['job1'], ':edg' => 0));
            //add to payment history ORDER CONFIRMED
            $note = 'ORDER TOTAL $0';
            $r = DB::sql('INSERT INTO payment_history (job_id,`type`,user_type,`date`,client_id,`summ`,description,`total`) VALUES (:job_id,:type,:user_type,NOW(),:client_id,:summ,:description,:total)', array(
                        ':job_id' => $post['job1'], ':type' => 'order_confirmed', ':user_type' => 'sys', ':client_id' => $payment['client_id'],
                        ':summ' => 0, ':description' => $note, ':total' => 0
            ));
            //add event
            $this->addSmallNotifications($post['req_id'], $post['job1'], $post['comp_id'], $r[0], 'order_confirmed', $note);
        }

        $r = DB::sql('INSERT INTO payment_history (job_id, `type`, user_type, `date`, client_id, summ, description, card_id, edg, transaction_code, small_descr) '
                        . 'VALUES (:job_id, :type, :user_type, NOW(), :client_id, :summ, :description, :card_id, :edg, :transaction_code, :small_descr)', array(
                    ':job_id' => $post['job1'], ':type' => 'redistr_add', ':user_type' => 'A', ':client_id' => $payment['client_id'],
                    ':summ' => $post['summ1'], ':description' => $descr, ':card_id' => '', ':edg' => 0, ':transaction_code' => '', ':small_descr' => $base_job['job_id']
        ));
        //add event
        $this->addSmallNotifications($post['req_id'], $post['job1'], $post['comp_id'], $r[0], 'redistr_payment', $descr);
        DB::sql('UPDATE user_jobs SET order_counts=order_counts+1, payments=payments+'.$post['summ1'].' WHERE id=:id', array(':id' => $post['job1']));

        if ($post['count_redistr'] == 2) {

            //check if first payment, add order total
            $job_order = DB::sql_row('SELECT order_total FROM user_jobs WHERE id=:id', array(':id' => $post['job2']));
            if (empty($job_order['order_total'])) {
                DB::sql('UPDATE user_jobs SET order_total=:total, edg=:edg WHERE id=:id', array(':total' => 0, ':id' => $post['job2'], ':edg' => 0));
                //add to payment history ORDER CONFIRMED
                $note = 'ORDER TOTAL $0';
                $r = DB::sql('INSERT INTO payment_history (job_id,`type`,user_type,`date`,client_id,`summ`,description,`total`) VALUES (:job_id,:type,:user_type,NOW(),:client_id,:summ,:description,:total)', array(
                            ':job_id' => $post['job2'], ':type' => 'order_confirmed', ':user_type' => 'sys', ':client_id' => $payment['client_id'],
                            ':summ' => 0, ':description' => $note, ':total' => 0
                ));
                //add event
                $this->addSmallNotifications($post['req_id'], $post['job2'], $post['comp_id'], $r[0], 'order_confirmed', $note);
            }

//To JOB2
            $r = DB::sql('INSERT INTO payment_history (job_id, `type`, user_type, `date`, client_id, summ, description, card_id, edg, transaction_code, small_descr) '
                            . 'VALUES (:job_id, :type, :user_type, NOW(), :client_id, :summ, :description, :card_id, :edg, :transaction_code, :small_descr)', array(
                        ':job_id' => $post['job2'], ':type' => 'redistr_add', ':user_type' => 'A', ':client_id' => $payment['client_id'],
                        ':summ' => $post['summ2'], ':description' => $descr, ':card_id' => '', ':edg' => 0, ':transaction_code' => '', ':small_descr' => $base_job['job_id']
            ));
            //add event
            $this->addSmallNotifications($post['req_id'], $post['job2'], $post['comp_id'], $r[0], 'redistr_payment', $descr);
            DB::sql('UPDATE user_jobs SET order_counts=order_counts+1, payments=payments+'.$post['summ2'].' WHERE id=:id', array(':id' => $post['job2']));
        }

        //Add redistribute to payment history
        $r = DB::sql('INSERT INTO payment_history (job_id, `type`, user_type, `date`, client_id, summ, description, card_id, edg, transaction_code, small_descr) '
                        . 'VALUES (:job_id, :type, :user_type, NOW(), :client_id, :summ, :description, :card_id, :edg, :transaction_code, :small_descr)', array(
                    ':job_id' => $payment['job_id'], ':type' => 'redistribute', ':user_type' => 'A', ':client_id' => $payment['client_id'],
                    ':summ' => $post['summ1'] + $post['summ2'], ':description' => $descr, ':card_id' => '', ':edg' => 0, ':transaction_code' => '', ':small_descr' => trim($post['small_descr'])
        ));
        //add event
        $this->addSmallNotifications($post['req_id'], $post['job2'], $post['comp_id'], $r[0], 'redistr_payment', $descr);
        DB::sql('UPDATE user_jobs SET order_counts=order_counts+1, payments=payments-'.($post['summ1'] + $post['summ2']).' WHERE id=:id', array(':id' => $payment['job_id']));
    }

    public function check_exist_job($job) {
        $rez = DB::sql_row('SELECT COUNT(id) counts FROM user_jobs WHERE job_id=:job_id', array(':job_id' => $job));
        return $rez;
    }

    public function get_company_name($id) {
        return DB::sql_row('SELECT company FROM users_company WHERE id=:id', array(':id' => $id));
    }

}
