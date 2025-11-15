<?php

class Model_Print extends Model {

    public function __construct() {
//        $this->db_fedex = Database::instance('ink_fedex');
        $this->tb_category = 'print_category';
        $this->tb_coat = 'print_coating';
        $this->tb_finish = 'print_finishes';
        $this->tb_coat_price = 'print_coating_price';
        $this->tb_item = 'print_item';
        $this->tb_item_price = 'print_item_price';
        $this->tb_shipp = 'print_shipping';
        $this->tb_shipp_price = 'print_shipping_price';
        $this->tb_setting = 'print_setting';
        $this->tb_stick = 'print_item_stick';
        $this->tb_pocket = 'print_pocket';
        $this->tb_folding = 'print_folding';
    }

    /*
     * Get item info
     * @param (int) $id: item id
     * @return (array) 
     */

    public function item_info($id, $uid = 'requests.user_id') {
        $rez = DB::sql_row('SELECT requests.*, DATE_FORMAT(requests.request_date, "%m-%d-%Y") request_date, users.first_name, users.last_name, company, 
            users.street, users.street2, users.email, users.position, users.phone_ext, users_company.abbr, users_company.duplicate,
            users.industry user_industry, users.city, users.state, users.zipcode, users.phone, users.fax, users.country FROM requests 
            LEFT JOIN users ON users.id='.$uid.'
            LEFT JOIN users_company ON users_company.id=requests.company_id
            WHERE requests.id=:id', array(':id' => intval($id)));
        return $rez;
    }

    /* --- Category  --- */

    /*
     * Get all Print category
     * @return (array) 
     */

    public function get_cats() {
        $all = array();
        $rez = DB::sql('SELECT * FROM ' . $this->tb_category);
        if (!empty($rez)) {
            foreach ($rez as $val) {
                $all[$val['id']] = $val;
            }
        }
        return $all;
    }

    /*
     * Get one Print category
     * @return (array) 
     */

    public function get_cat($id = NULL) {
        if(empty($id)){
            $id = Request::initial()->param('param2');
        }
        return DB::get_row($this->tb_category, 'id=' . $id);
    }

    /* --- Folding  --- */

    /*
     * Get all Print folding
     * @return (array) 
     */

    public function get_foldings() {
        $all = array();
        $rez = DB::sql('SELECT * FROM ' . $this->tb_folding);
        if (!empty($rez)) {
            foreach ($rez as $val) {
                $all[$val['id']] = $val;
            }
        }
        return $all;
    }

    /*
     * Get one Print folding
     * @return (array) 
     */

    public function get_folding() {
        $id = Request::initial()->param('param2');
        return DB::get_row($this->tb_folding, 'id=' . $id);
    }

    /* --- Pockets  --- */

    /*
     * Get all Print pockets
     * @return (array) 
     */

    public function get_pockets($type = NULL) {
        $all = array();
        $where = '';
        if(!empty($type)){
            $where = ' WHERE `type`="'.$type.'" ';
        }
        $rez = DB::sql('SELECT * FROM ' . $this->tb_pocket.' '.$where);
        if (!empty($rez)) {
            foreach ($rez as $val) {
                $all[$val['id']] = $val;
            }
        }
        return $all;
    }
    
    public function get_item_packs(){
        $id = Request::initial()->param('param2');
        $rez = array();
        $all = DB::sql('SELECT * FROM print_item_pockets WHERE item_id=:id', array(':id'=>$id));
        if(!empty($all)){
            foreach($all as $val){
                $rez[$val['page_num']] = $val;
            }
        }
        return $rez;
    }

    /*
     * Get one Print pocket
     * @return (array) 
     */

    public function get_pocket() {
        $id = Request::initial()->param('param2');
        return DB::get_row($this->tb_pocket, 'id=' . $id);
    }

    /* --- Coatings --- */

    /*
     * Get coats/finish prices
     * @param (int) $id: coating id
     * @param (string) $table: table name
     * @return (array)
     */

    public function get_coat_price($id, $table) {
        return DB::get($table . '_price', 'coat_id=' . $id);
    }

    /*
     * Get all coats/finish
     * @return (array)
     */

    public function get_coats($table) {
        $all = array();
        $rez = DB::sql('SELECT * FROM ' . $table);
        if (!empty($rez)) {
            foreach ($rez as $val) {
                $all[$val['id']] = $val;
            }
        }
        return $all;
    }

    /*
     * Get coat/finish details
     * @param (string) $table: table name
     * @return (array)
     */

    public function get_coat($table) {
        $id = Request::initial()->param('param2');
        return DB::get_row($table, 'id=' . $id);
    }

    /* --- Items  --- */

    /*
     * Get all print items
     * @return (array)
     */

    public function get_items($category = NULL) {
        $where = '';
        if(!empty($category)){
            $where = ' WHERE category_id='.$category;
        }
        return DB::sql('SELECT tabi.*, tabc.title category 
            FROM ' . $this->tb_item . ' tabi 
                LEFT JOIN ' . $this->tb_category . ' tabc ON tabc.id=tabi.category_id '.$where);
    }

    /*
     * Get one print item
     * @param (int) $id: item id
     * @return (array)
     */

    public function get_item($id = NULL) {
        if (empty($id)) {
            $id = Request::initial()->param('param2');
        }
        return DB::sql_row('SELECT item.*, tabc.title category 
            FROM ' . $this->tb_item . ' item
            LEFT JOIN ' . $this->tb_category . ' tabc ON tabc.id=item.category_id
            WHERE item.id=:id
        ', array(':id' => $id));
    }

    /*
     * Get sticked 
     * @param (int) $id: item id
     * @return (array)
     */

    public function get_sticked($id = NULL) {
        if (empty($id)) {
            $id = Request::initial()->param('param2');
        }
        return DB::get_row($this->tb_stick, 'item_id=' . $id);
    }

    /*
     * Get item prices 
     * @param (int) $id: item id
     * @return (array)
     */

    public function get_item_prices($id) {
        return DB::sql('SELECT * FROM print_item_price WHERE item_id=:item_id', array(':item_id' => $id));
    }

    /*
     * Get item dimentions 
     * @param (int) $id: item id
     * @return (array)
     */

    public function get_item_dimentions($id) {
        return DB::sql('SELECT * FROM print_item_dimention WHERE item_id=:item_id', array(':item_id' => $id));
    }

    /* --- Shipping --- */

    /*
     * Get shipp price
     * @param (int) $id: shipp id
     * @return (array)
     */

    public function get_shipp_price($id) {
        return DB::get($this->tb_shipp_price, 'shipp_id=' . $id);
    }

    /*
     * Get all shipp
     * @return (array)
     */

    public function get_shipps() {
        return DB::get($this->tb_shipp);
    }

    /*
     * Get one shipp data
     * @return (array)
     */

    public function get_shipp() {
        $id = Request::initial()->param('param2');
        return DB::get_row($this->tb_shipp, 'id=' . $id);
    }

    /* --- Finishes --- */

    /*
     * Get coating/finishes
     * @param (string) $table: DB table name
     * @return (array)
     */

    public function get_coating($table = 'print_coating') {
        $all = array();
        $rez = DB::sql('SELECT * FROM ' . $table);
        if (!empty($rez)) {
            foreach ($rez as $val) {
                $all[$val['id']] = $val;
            }
        }
        return $all;
    }

    /* --- INKS --- */

    /*
     * Get all inks
     * @return (array)
     */

    public function get_inks() {
        $all = array();
        $rez = DB::sql('SELECT * FROM print_inks');
        if (!empty($rez)) {
            foreach ($rez as $val) {
                $all[$val['id']] = $val;
            }
        }
        return $all;
    }

    /*
     * Get one ink
     * @param (int) $id
     * @return (array)
     */

    public function getInk($id) {
        return DB::sql_row('SELECT * FROM print_inks WHERE id=:id', array(':id' => $id));
    }

    /* --- Slits --- */

    /*
     * Get all slits
     * @return (array)
     */

    public function getSlits() {
        $all = array();
        $rez = DB::sql('SELECT * FROM print_slits');
        if (!empty($rez)) {
            foreach ($rez as $val) {
                $all[$val['id']] = $val;
            }
        }
        return $all;
    }

    /*
     * Get item slits
     */
    public function get_item_slits(){
        $id = Request::initial()->param('param2');
        return DB::get('print_item_slits', 'item_id=' . $id);
    }
    
    /*
     * Get one slit
     * @param (int) $id
     * @return (array)
     */

    public function getSlit($id) {
        return DB::sql_row('SELECT * FROM print_slits WHERE id=:id', array(':id' => $id));
    }

    /* --- Proof --- */

    /*
     * Get all proofs
     * @return (array)
     */

    public function getProofs() {
        $all = array();
        $rez = DB::sql('SELECT * FROM print_proof');
        if (!empty($rez)) {
            foreach ($rez as $val) {
                $all[$val['id']] = $val;
            }
        }
        return $all;
    }

    /*
     * Get one proof
     * @param (int) $id
     * @return (array)
     */

    public function getProof($id) {
        return DB::sql_row('SELECT * FROM print_proof WHERE id=:id', array(':id' => $id));
    }

    /* --- Paper --- */

    /*
     * Get paper prices
     * @param (int) $id: paper id
     * @return (array)
     */

    public function get_paper_price($id) {
        return DB::get('print_papers_price', 'paper_id=' . $id);
    }

    /*
     * Get paper
     * @param (int) $id
     * @return (array)
     */

    public function getPaper($id) {
        return DB::sql_row('SELECT * FROM print_papers WHERE id=:id', array(':id' => $id));
    }

    /*
     * Get papers
     * @param (array) $in_array: filter by id
     * @return (array)
     */

    public function get_papers($in_array = NULL) {
        $in_arr = (empty($in_array)) ? '' : 'IN (' . implode(',', $in_array) . ')';
        $all = array();
        $rez = DB::sql('SELECT * FROM print_papers ' . $in_arr);
        if (!empty($rez)) {
            foreach ($rez as $val) {
                $all[$val['id']] = $val;
            }
        }
        return $all;
    }

    /*
     * Get category
     * @param (array) $items: filter by id
     * @return (array)
     */

    public function getCategoryDetails($items) {
        $rez = DB::sql('SELECT * FROM ' . $this->tb_category . ' WHERE id IN ("' . implode('","', $items) . '")');
        if (!empty($rez)) {
            foreach ($rez as $val) {
                $all[$val['id']] = $val['title'];
            }
        }
        return $all;
    }

    /*
     * Get category items
     * @param (array) $id: category id
     * @return (array)
     */

    public function getCategoryItems($id) {
        return DB::sql('SELECT * FROM ' . $this->tb_item . ' WHERE category_id=:id', array(':id' => $id));
    }

}