<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_model extends CI_Model{

    /**
     * 
     * Summary statistics for dashboard
     * 
     * @param userId: Specify userId to get stats depending on user type
     * 
    */
    function getSummaryStats($userId=''){

        # Admin views everything
        if($this->auth_model->is_admin()){
            $str = "
                (SELECT COUNT(id) 
                    FROM aauth_users) AS users";
        }
        else{

            // TODO: Depending on the user type, show different stats
        }

        $this->db->select($str);

        return $this->db->get()->row();
    }
}
