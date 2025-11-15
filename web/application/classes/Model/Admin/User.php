<?php

class Model_Admin_User extends Model_User {
    /*
     * Add/Update User
     * @param (int) $id: user id for update
     */

    public function save_user($id = NULL) {
        $post = Request::initial()->post();
        $login = $post['login'];
        $email = $post['email'];
        $first_name = $post['first_name'];
        $last_name = $post['last_name'];
        $group_id = $post['group'];
        $password_ins = '';
        if (!empty($post['password'])) {
            $password_ins = ', password=MD5("' . $post['password'] . '")';
        }
        if (!empty($id)) {
            DB::sql('UPDATE users SET login=:login, email=:email, first_name=:first_name, last_name=:last_name, group_id=:group_id ' . $password_ins . ' WHERE id=:id', array(':login' => $login, ':email' => $email, ':first_name' => $first_name, ':last_name' => $last_name, ':group_id' => $group_id, ':id' => $id));

            //add event
            Model::factory('Admin_Event')->add_event('user_updated', $id, "");
        } else {
            $r = DB::sql('INSERT INTO users (login, password, email, first_name, last_name, group_id) 
                VALUES (:login, :password, :email, :first_name, :last_name, :group_id)', array(':login' => $login, ':password' => md5($post['password']), ':email' => $email, ':first_name' => $first_name, ':last_name' => $last_name, ':group_id' => $group_id));
            $id = $r[0];
            //add event
            Model::factory('Admin_Event')->add_event('new_user', $id, "");
        }

        if (is_file($_FILES['photo']['tmp_name'])) {
            $img = Image::factory($_FILES['photo']['tmp_name']);
            $img->resize(79, 79, Image::INVERSE)->crop(79, 79)->save(APPPATH . 'files/users/' . $id . '.jpg', 95);
        }
    }

    /*
     * Get user information
     * @param (int) $id: user id
     * @return (array) user data
     */

    public function get_user_info($id) {
        $user = DB::sql_row('SELECT users.*, users_company.company FROM users 
            LEFT JOIN users_company ON users_company.id=users.company_id
        WHERE users.id=:id', array(':id' => intval($id)));
        //additional
        $addit = DB::sql('SELECT * FROM user_additional_info WHERE uid=:uid', array(':uid' => $id));
        if (!empty($addit)) {
            foreach ($addit as $val) {
                if ($val['type'] == 'phone') {
                    $user['phone_alt_ext'] = $val['ext'];
                    $user['phone_alt'] = $val['value'];
                } else {
                    $user['email_alt'] = $val['value'];
                }
            }
        }
        return $user;
    }

    /*
     * user additional info
     */

    public function user_additional_fields($uid, $type) {
        $user = array();
        $us_data = DB::sql_row('SELECT * FROM users WHERE id=:uid', array(':uid' => $uid));
        //main
        if ($type == 'phone') {
            if (!empty($us_data['phone'])) {
                $user[] = array('phone' => $us_data['phone'], 'ext' => $us_data['phone_ext'], 'type' => $us_data['phone_type']);
            }
        } else {
            if (!empty($us_data['email'])) {
                $user[] = array('email' => $us_data['email']);
            }
        }

        //additional
        $addit = DB::sql('SELECT * FROM user_additional_info WHERE uid=:uid AND type=:type', array(':uid' => $uid, ':type' => $type));
        if (!empty($addit)) {
            foreach ($addit as $val) {
                if ($val['type'] == 'phone') {
                    $user[] = array('phone' => $val['value'], 'ext' => $val['ext'], 'type' => $val['content_type']);
                } else {
                    $user[] = array('email' => $val['value']);
                }
            }
        }
        return $user;
    }

    /*
     * Get user groups
     * @return (array)
     */

    public function get_user_groups() {
        $groups = DB::get('user_group');
        $arr = array();
        foreach ($groups as $val) {
            $arr[$val['id']] = $val['group_name'];
        }
        return $arr;
    }

    /*
     * Delete user
     * @param (int) $id: user id
     */

    public function remove_user($id) {
        $user_data = $this->get_user_info($id);
        DB::sql('DELETE FROM users WHERE id=:id', array(':id' => $id));

        //add event
        Model::factory('Admin_Event')->add_event('user_removed', $id, serialize($user_data));
    }

    /*
     * Get all users
     * @return (array)
     */

    public function get_users($search_name = '', $search_group = '', $page = 0) {
        $for_page = 100;
        $page = (empty($page)) ? 0 : intval($page) - 1;
        $begin = $page * $for_page;
        $_GET['page'] = $page + 1;
        $search = array();
        if (!empty($search_name)) {
            $search[] = ' AND (CONCAT(users.first_name," ",users.last_name) LIKE "%' . trim($search_name) . '%") OR (users_company.company LIKE "%' . trim($search_name) . '%") ';
        }
        if (!empty($search_group)) {
            $search[] = ' AND group_id="' . $search_group . '" ';
        }
        return DB::sql('SELECT SQL_CALC_FOUND_ROWS users.*, users_company.company
            FROM users 
            LEFT JOIN users_company ON users_company.id=users.company_id
            WHERE 1=1 ' . implode(' ', $search) . ' LIMIT ' . $begin . ', ' . $for_page);
    }

    /*
     * Get user jobs
     * @param (int) $uid: user id
     * @param (string) $user_type: if set we will search with filter: "S" - just estimate, "O" - Orders, "A" - all
     * @return (array)
     */

    public function getCompanyJobs($cid, $user_type = NULL) {
        $rez = array();
        if (!empty($user_type)) {
            $where = '';
            switch ($user_type) {
                case 'S':
                    $where = 'AND (estimate_id!="" AND job_id="") ';
                    break;
                case 'O':
                    $where = ' AND job_id!="" ';
                    break;
            }
            $sql = 'SELECT * FROM user_jobs WHERE company_id=:cid ' . $where . ' ORDER BY id DESC';
        } else {
            $sql = 'SELECT * FROM user_jobs WHERE company_id=:cid ORDER BY id DESC';
        }
        $all = DB::sql($sql, array(':cid' => $cid));
        if (!empty($all)) {
            foreach ($all as $val) {
                $rez[$val['id']] = $val;
            }
        }
        return $rez;
    }

    /*
     * Contact list. Set Review or Processed status for user. 
     * @param (int) $uid: user id
     * @param $type: status type
     * @param $val: status value
     */

    public function change_comment_status($uid, $type, $val) {
        $user = $this->get_user_info($uid);
        $admin_comment = (empty($user['admin_comment'])) ? array() : unserialize($user['admin_comment']);
        $admin_comment[$type] = $val;
        DB::sql('UPDATE users SET admin_comment=:admin_comment WHERE id=:id', array(':id' => $uid, ':admin_comment' => serialize($admin_comment)));
    }

    /*
     * Get users in groups
     * @param (array) $groups: group id array
     * @return (array) users
     */

    public function getUsersInGroups($groups) {
        return DB::sql('SELECT * FROM users WHERE group_id IN (' . implode(',', $groups) . ')');
    }

    public function generate_user_abbr($name, $global_str) {
        $name = strtoupper($name);
        $name = preg_replace('/[^A-Z0-9 ]/', '', $name);
        $comp = str_replace(array(' OF ', ' THE ', ' A ', ' AND '), ' ', $name);

        if (strpos($comp, 'A ') == 0) {
            $comp = str_replace('A ', '', $comp);
        }
        if (strpos($comp, 'THE ') == 0) {
            $comp = str_replace('THE ', '', $comp);
        }
        $company = explode(' ', trim(strtoupper($comp)));
        $company_ok = array();
        foreach ($company as $k => $v) {
            if (!empty($v)) {
                $company_ok[] = $v;
            }
        }
        if (empty($company_ok))
            return FALSE;

        //one word
        if (count($company_ok) == 1) {

            //if one letter - repeat 3 times
            if (strlen($company_ok[0]) == 1) {
                $code = $company_ok[0] . $company_ok[0] . $company_ok[0];
                $rez = $this->check_job_exist($code, $global_str, 1);
                if (empty($rez)) {
                    return $code;
                } else {
                    //repeat 3 times + number
                    return $code . ($rez + 1);
                }

                //2 letters
            } elseif (strlen($company_ok[0]) == 2) {
                //2 letters + repeat last
                $code = $company_ok[0] . substr($company_ok[0], -1, 1);
                $rez = $this->check_job_exist($code, $global_str, 1);
                if (empty($rez)) {
                    return $code;
                } else {
                    //2 letters + repeat last + number
                    return $code . ($rez + 1);
                }

                //3 letters
            } elseif (strlen($company_ok[0]) == 3) {
                $code = substr($company_ok[0], 0, 3);
                $rez = $this->check_job_exist($code, $global_str, 1);
                if (empty($rez)) {
                    return $code;
                } else {
                    //3 letters + number
                    return $code . ($rez + 1);
                }

                //4 letters
            } elseif (strlen($company_ok[0]) == 4) {
                //3 first letters
                $code = substr($company_ok[0], 0, 3);
                $rez = $this->check_job_exist($code, $global_str);
                if (empty($rez)) {
                    return $code;
                } else {
                    //2 first + last letter
                    $code = substr($company_ok[0], 0, 2) . substr($company_ok[0], -1, 1);
                    $rez = $this->check_job_exist($code, $global_str);
                    if (empty($rez)) {
                        return $code;
                    } else {
                        //1 first + 2 last
                        $code = substr($company_ok[0], 0, 1) . substr($company_ok[0], -2, 1);
                        $rez = $this->check_job_exist($code, $global_str);
                        if (empty($rez)) {
                            return $code;
                        } else {
                            //3 first + number
                            $code = substr($company_ok[0], 0, 3);
                            $rez = $this->check_job_exist($code, $global_str, 1);
                            if (empty($rez)) {
                                return $code;
                            } else {
                                return $code . ($rez + 1);
                            }
                        }
                    }
                }
            } else {
                //more than 4 letters
                $code = substr($company_ok[0], 0, 3);
                $rez = $this->check_job_exist($code, $global_str);
                if (empty($rez)) {
                    return $code;
                } else {
                    //2 first + last letter
                    $code = substr($company_ok[0], 0, 2) . substr($company_ok[0], -1, 1);
                    $rez = $this->check_job_exist($code, $global_str);
                    if (empty($rez)) {
                        return $code;
                    } else {
                        //1 first + 2 last
                        $code = substr($company_ok[0], 0, 1) . substr($company_ok[0], -2, 1);
                        $rez = $this->check_job_exist($code, $global_str);
                        if (empty($rez)) {
                            return $code;
                        } else {
                            //2 first + some other letter, scan...
                            $steps = strlen($company_ok[0]) - 2;
                            for ($x = 1; $x <= $steps; $x++) {
                                $code = substr($company_ok[0], $x * (-1), 1);
                                $rez = $this->check_job_exist($code, $global_str);
                                if (empty($rez)) {
                                    return $code;
                                }
                            }
                            //3 letters
                            $code = substr($company_ok[0], 0, 3);
                            $rez = $this->check_job_exist($code, $global_str, 1);
                            if (empty($rez)) {
                                return $code;
                            } else {
                                return $code . ($rez + 1);
                            }
                        }
                    }
                }
            }

            //2 words 
        } elseif (count($company_ok) == 2) {

            //2 FN + 1 LN
            $code = substr($company_ok[0], 0, 2) . substr($company_ok[1], 0, 1);
            $rez = $this->check_job_exist($code, $global_str);
            if (empty($rez)) {
                return $code;
            } else {

                //1 FN + 2 LN
                $code2 = substr($company_ok[0], 0, 1) . substr($company_ok[1], 0, 2);
                $rez2 = $this->check_job_exist($code2, $global_str);
                if (empty($rez2)) {
                    return $code2;
                } else {

                    //3FN
                    $code3 = substr($company_ok[0], 0, 3);
                    $rez3 = $this->check_job_exist($code3, $global_str);
                    if (empty($rez3)) {
                        return $code3;
                    } else {

                        //2 FN + second letter second word
                        $code = substr($company_ok[0], 0, 2) . substr($company_ok[1], 1, 1);
                        $rez3 = $this->check_job_exist($code, $global_str);
                        if (empty($rez3)) {
                            return $code;
                        } else {

                            //1 first + second and third letters second word
                            $code = substr($company_ok[0], 0, 1) . substr($company_ok[1], 1, 2);
                            $rez3 = $this->check_job_exist($code, $global_str);
                            if (empty($rez3)) {
                                return $code;
                            } else {

                                //2 first + some other letter, scan...
                                $steps = strlen($company_ok[1]) - 2;
                                if ($steps > 1) {
                                    for ($x = 1; $x <= $steps; $x++) {
                                        $code = substr($company_ok[0], 0, 2) . substr($company_ok[1], $x * (-1), 1);
                                        $rez = $this->check_job_exist($code, $global_str);
                                        if (empty($rez)) {
                                            return $code;
                                        }
                                    }
                                }

                                //2 FN + 1LN + number
                                $code = substr($company_ok[0], 0, 2) . substr($company_ok[1], 0, 1);
                                $rez3 = $this->check_job_exist($code, $global_str, 1);
                                if (empty($rez3)) {
                                    return $code;
                                } else {
                                    return $code . ($rez3 + 1);
                                }
                            }
                        }
                    }
                }
            }

            //3 words
        } else {
            //1FN + 1SN + 1LN
            $code = @substr(@$company_ok[0], 0, 1) . substr($company_ok[1], 0, 1) . substr($company_ok[2], 0, 1);
            $rez = $this->check_job_exist($code, $global_str);
            if (empty($rez)) {
                return $code;
            } else {
                //2 FN + 1 LN
                $code = substr($company_ok[0], 0, 2) . substr($company_ok[1], 0, 1);
                $rez = $this->check_job_exist($code, $global_str);
                if (empty($rez)) {
                    return $code;
                } else {
                    //1 FN + 2 LN
                    $code2 = substr($company_ok[0], 0, 1) . substr($company_ok[1], 0, 2);
                    $rez2 = $this->check_job_exist($code2, $global_str);
                    if (empty($rez2)) {
                        return $code2;
                    } else {
                        //3FN
                        $code3 = substr($company_ok[0], 0, 3);
                        $rez3 = $this->check_job_exist($code3, $global_str);
                        if (empty($rez3)) {
                            return $code3;
                        } else {
                            //2FN + 1TN
                            $code = substr($company_ok[0], 0, 2) . substr($company_ok[2], 0, 1);
                            $rez = $this->check_job_exist($code, $global_str);
                            if (empty($rez)) {
                                return $code;
                            } else {
                                //1FN + 2TH
                                $code = substr($company_ok[0], 0, 1) . substr($company_ok[2], 0, 2);
                                $rez = $this->check_job_exist($code, $global_str);
                                if (empty($rez)) {
                                    return $code;
                                } else {
                                    //1 first + second letter second word + 1 third
                                    $code = substr($company_ok[0], 0, 1) . substr($company_ok[1], 1, 1) . substr($company_ok[2], 0, 1);
                                    $rez = $this->check_job_exist($code, $global_str);
                                    if (empty($rez)) {
                                        return $code;
                                    } else {
                                        //1 first + 1 second + second letter third word
                                        $code = substr($company_ok[0], 0, 1) . substr($company_ok[1], 0, 1) . substr($company_ok[2], 1, 1);
                                        $rez = $this->check_job_exist($code, $global_str);
                                        if (empty($rez)) {
                                            return $code;
                                        } else {
                                            //2 first + second letter second word
                                            $code = substr($company_ok[0], 0, 2) . substr($company_ok[1], 1, 1);
                                            $rez = $this->check_job_exist($code, $global_str);
                                            if (empty($rez)) {
                                                return $code;
                                            } else {
                                                //2 first + second letter third word
                                                $code = substr($company_ok[0], 0, 2) . substr($company_ok[2], 1, 1);
                                                $rez = $this->check_job_exist($code, $global_str);
                                                if (empty($rez)) {
                                                    return $code;
                                                } else {

                                                    //1 first + 1 second + some other letter from third, scan...
                                                    $steps = strlen($company_ok[2]) - 2;
                                                    if ($steps > 1) {
                                                        for ($x = 1; $x <= $steps; $x++) {
                                                            $code = substr($company_ok[0], 0, 1) . substr($company_ok[1], 0, 1) . substr($company_ok[2], $x * (-1), 1);
                                                            $rez = $this->check_job_exist($code, $global_str);
                                                            if (empty($rez)) {
                                                                return $code;
                                                            }
                                                        }
                                                    }

                                                    //2 first + 1 second + number
                                                    $code = substr($company_ok[0], 0, 2) . substr($company_ok[2], 0, 1);
                                                    $rez = $this->check_job_exist($code, $global_str, 1);
                                                    if (empty($rez)) {
                                                        return $code;
                                                    } else {
                                                        return $code . ($rez + 1);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function check_job_exist($code, $global_str, $last = false) {
        if (strlen($code) == 2 && $last == 1) {
            $code .= '0';
        }
        if (strlen($code) == 3) {
            if (empty($global_str[$code])) {
                $global_str[$code] = 1;
                return 0;
            } else {
                if (!empty($last)) {
                    $global_str[$code] += 1;
                }
                $rez = $global_str[$code];
                return $rez;
            }
        }
        return true;
    }

    public function get_company_users($company) {
        return DB::sql('SELECT * FROM users WHERE company_id=:id', array(':id' => $company));
    }

    public function request_main_user($req_id, $user_id) {
        DB::sql('UPDATE requests SET user_id=:uid WHERE id=:id', array(':id' => $req_id, ':uid' => $user_id));
    }

    public function add_company_user() {
        $post = Request::initial()->post();
        if (!empty($post['req_id'])) {
            $print_model = Model::factory('Print');
            $req = $print_model->item_info($post['req_id']);
            $comp_id = $req['company_id'];

            $comp = DB::sql_row('SELECT * FROM users_company WHERE id=:id', array(':id' => $comp_id));

            $r = DB::sql('INSERT INTO users (login,password,email,first_name,last_name,group_id,company_id,street,street2,city,state,zipcode,phone,position,fax,phone_ext) 
            VALUES (:login,"",:email,:first_name,:last_name, 1, :company_id,:street,:street2,:city,:state,:zipcode,:phone,:position,:fax,:phone_ext)', array(
                        ':login' => $post['email'], ':email' => $post['email'], ':first_name' => $post['first_name'], ':last_name' => $post['last_name'], ':company_id' => $comp_id,
                        ':street' => $post['street'], ':street2' => $post['street2'], ':city' => $post['city'], ':state' => $post['state'], ':zipcode' => $post['zipcode'], ':phone' => $post['phone'], ':position' => $post['position'], ':fax' => $post['fax'], ':phone_ext' => $post['phone_ext']
            ));

            //add billing & shipping
            DB::sql('INSERT credit_card_billing (user_id, first_name, last_name, address, city, state, zip, country, email, phone, phone_ext, company, visible, `default`, address2) '
                    . 'VALUES (:user_id, :first_name, :last_name, :address, :city, :state, :zip, :country, :email, :phone, :phone_ext, :company, 1, 1, :address2)', array(
                ':user_id' => $r[0], ':first_name' => $post['first_name'], ':last_name' => $post['last_name'], ':address' => $post['street'], ':city' => $post['city'], ':state' => $post['state'], ':zip' => $post['zipcode'],
                ':country' => '', ':email' => $post['email'], ':phone' => $post['phone'], ':phone_ext' => $post['phone_ext'], ':company' => $comp['company'], ':address2' => $post['street2']
            ));
            DB::sql('INSERT credit_card_shipping (user_id, company_id, first_name, last_name, address, city, state, zip, country, email, phone, phone_ext, company, address2, title) '
                    . 'VALUES (:user_id, :company_id, :first_name, :last_name, :address, :city, :state, :zip, :country, :email, :phone, :phone_ext, :company, :address2, "default")', array(
                ':user_id' => $r[0], ':first_name' => $post['first_name'], ':last_name' => $post['last_name'], ':address' => $post['street'], ':city' => $post['city'], ':state' => $post['state'], ':zip' => $post['zipcode'],
                ':country' => '', ':company_id'=>$comp_id, ':email' => $post['email'], ':phone' => $post['phone'], ':phone_ext' => $post['phone_ext'], ':company' => $comp['company'], ':address2' => $post['street2']
            ));
            return $r[0];
        }
    }

    public function job_main_user($job_id, $user_id) {
        DB::sql('UPDATE user_jobs SET user_id=:uid WHERE id=:id', array(':id' => $job_id, ':uid' => $user_id));
    }

    public function user_fast_update() {
        $post = Request::initial()->post();
        DB::sql('UPDATE users SET first_name=:first_name, last_name=:last_name, email=:email, street=:street, street2=:street2, city=:city, state=:state, zipcode=:zipcode, phone=:phone, phone_ext=:phone_ext, fax=:fax, position=:position WHERE id=:id', array(':id' => $post['id'], ':first_name' => $post['first_name'], ':last_name' => $post['last_name'], ':email' => $post['email'], ':street' => $post['street'], ':street2' => $post['street2'], ':city' => $post['city'], ':state' => $post['state'],
            ':zipcode' => $post['zipcode'], ':phone' => $post['phone'], ':phone_ext' => $post['phone_ext'], ':fax' => $post['fax'], ':position' => $post['position']));
        //Add to billing+shipping address
        $users = DB::sql_row('SELECT users.*, users_company.company FROM users '
                        . ' LEFT JOIN users_company ON users_company.id=users.company_id'
                        . ' WHERE users.id=:id', array(':id' => $post['id']));
        DB::sql('INSERT INTO credit_card_billing (user_id,title,first_name,last_name,company,address,address2,suite,city,state,zip,country,phone,email) '
                . 'VALUES (:user_id,"",:first_name,:last_name,:company,:address,:address2,:suite,:city,:state,:zip,:country,:phone,:email)', array(
            ':user_id' => $post['id'], ':first_name' => $post['first_name'], ':last_name' => $post['last_name'], ':company' => $users['company'], ':address' => $post['street'], ':address2' => $post['street2'], ':suite' => '', ':city' => $post['city'], ':state' => $post['state'], ':zip' => $post['zipcode'], ':country' => $users['country'], ':phone' => $post['phone'], ':email' => $post['email']
        ));
        DB::sql('INSERT INTO credit_card_shipping (user_id,company_id,title,first_name,last_name,company,address,address2,suite,city,state,zip,country,phone,email) '
                . 'VALUES (:user_id,:company_id,"",:first_name,:last_name,:company,:address,:address2,:suite,:city,:state,:zip,:country,:phone,:email)', array(
            ':user_id' => $post['id'], ':company_id' => $users['company_id'], ':first_name' => $post['first_name'], ':last_name' => $post['last_name'], ':company' => $users['company'], ':address' => $post['street'], ':address2' => $post['street2'], ':suite' => '', ':city' => $post['city'], ':state' => $post['state'], ':zip' => $post['zipcode'], ':country' => $users['country'], ':phone' => $post['phone'], ':email' => $post['email']
        ));
    }

    public function user_fastship_update($uid) {
        $post = Request::initial()->post();
        $name = explode(' ', trim($post['name']));
        DB::sql('UPDATE users SET first_name=:first_name, last_name=:last_name, street=:street, city=:city, state=:state, zipcode=:zipcode, country=:country WHERE id=:id', array(':id' => $uid, ':first_name' => $name[0], ':last_name' => $name[1], ':street' => $post['address'], ':city' => $post['city'], ':state' => $post['state'], ':country' => $post['country'], ':zipcode' => $post['zip']));
    }

    public function get_user_job($job_id) {
        $user = DB::sql_row('SELECT user_id FROM user_jobs WHERE id=:id', array(':id' => $job_id));
        return $user['user_id'];
    }

    public function remove_user_billing($id) {
        DB::sql('DELETE FROM credit_card_billing WHERE id=:id', array(':id' => $id));
        return array('ok' => 1);
    }

}
