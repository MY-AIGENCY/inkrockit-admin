<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Mail extends Main {

    public function action_index() {
        $this->template->scripts[] = 'home.init';
        $this->template->content = View::factory('pages/mail_it', $this->data);
        $this->template->active_menu = $this->request->param('param');
    }

    public function action_ajaxGetInbox() {
        set_time_limit(3600);
        ignore_user_abort(true);

        $server = 's37838.gridserver.com';
        $username = 'dtraub@inkrockit.com';
        $password = 'Letmeinnow1234';
        $conn = imap_open('{' . $server . ':993/ssl}INBOX', $username, $password, OP_READONLY);
        $r = imap_search($conn, 'ALL');
        $r = array_reverse($r);

        $emails_db = array();
        $exists = DB::sql('SELECT CONCAT(`date`, `from`) `in` FROM message_inbox');
        if (!empty($exists)) {
            foreach ($exists as $val) {
                $emails_db[] = $val['in'];
            }
        }

        if (is_array($r)) {
            foreach ($r as $key => $val) {
                if ($key < 100) {

                    $head_info = imap_headerinfo($conn, $val);
                    if (!in_array(@$head_info->date . $head_info->fromaddress, $emails_db)) {
                        $matches = array();
                        
                        if (strpos($head_info->subject, 'INKROCKIT ORDER - ') !== FALSE) {
                            preg_match('/INKROCKIT ORDER - (.*)?- .*/i', $head_info->subject, $matches);
                        } elseif (strpos($head_info->subject, 'INKROCKIT PROOF - ') !== FALSE) {
                            preg_match('/INKROCKIT PROOF - (.*)?- .*/i', $head_info->subject, $matches);
                        } elseif (strpos($head_info->subject, 'INKROCKIT ESTIMATE - ') !== FALSE) {
                            preg_match('/INKROCKIT ESTIMATE - (.*)?- .*/i', $head_info->subject, $matches);
                        }

                        $date = date('Y-m-d h:i:s', strtotime(@$head_info->date));

                        if (!empty($matches[1])) {
                            $body = imap_fetchbody($conn, $val, '1');
                            $body = str_replace('”', '"', $body);
                            $body = str_replace('”', '"', $body);
                            $body = str_replace('“', '"', $body);
                            
                            $text_plain = trim(strip_tags(quoted_printable_decode($body)));
                            $message_text = 'From: '.  htmlspecialchars($head_info->fromaddress).'<br> Subject: '.$head_info->subject.'<br><br>'.$text_plain;

                            $job = DB::sql_row('SELECT * FROM user_jobs WHERE job_id=:id OR estimate_id=:id', array(':id' => $matches[1]));
                            if (!empty($job)) {

                                $req = DB::sql_row('SELECT * FROM requests WHERE user_id=:id', array(':id' => $job['user_id']));
                                if (!empty($req)) {
                                    echo $head_info->subject;
                                    echo '<hr>';
                                    //add to notes
                                    DB::sql('INSERT INTO request_notes (request_id, company_id, text, date, job_id, author_id, type, type_user) '
                                            . 'VALUES (:request_id, :company_id, :text, :date, :job_id, :author_id, :type, :type_user)', array(':request_id' => $req['id'], ':company_id' => $job['company_id'], ':text' => $message_text, ':date' => $date,
                                        ':job_id' => $job['id'], ':author_id' => 1, ':type' => 'email_in', ':type_user' => 'A'));
                                }
                            }
                            
                            //add to message table                    
                            DB::sql('INSERT INTO message_inbox (`date`, `subject`, `text`, `from`, `to`) VALUES (:date, :subject, :text, :from, :to)', array(':date' => @$head_info->date, ':subject' => $head_info->subject, ':text' => $text_plain, ':from' => $head_info->fromaddress, ':to' => $head_info->toaddress));
                        }
                    }
                }
            }
        }

        exit;
    }

}
