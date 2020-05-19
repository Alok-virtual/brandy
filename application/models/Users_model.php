
<?php 
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Users_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    //USER REGISTRATION 
    public function user_registration($data) 
    {
        $userreg = array(
            'email'  => trim($data['email']),
            'password' => trim(md5($data['password'])),
            'name' => trim($data['name']),
            'created_at'  => date('Y-m-d H:i:s')
        );

       

        $this->db->where('email', $data['email']);
        $query = $this->db->get(USERS);


        if ($query->num_rows()>0) {
            return false;
        }
        else {
            $this->db->insert(USERS, $userreg);
            $insert_id = $this->db->insert_id();
            $this->db->where('id', $insert_id);
        $q = $this->db->get(USERS);
         return $q->row();

            
            
        }
    }

    public function user_login($log_id, $pass) 
    {
        $this->db->select('*');
        $this->db->where('email', trim($log_id));
        $this->db->where('password', trim($pass));
        $this->db->from(USERS);
        $q = $this->db->get();
        if ($q->num_rows() == 1) {
             return $q->row(); 
        }
        else {
            return false;
        }
    }

}
?>