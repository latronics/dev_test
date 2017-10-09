<?php

class Csv extends CI_Controller {

    function __construct() {
        parent::__construct();

        $this->db = $this->load->database('default', TRUE);
    }

    public function csv_import() {
        
        $this->load->view('csv_to_services');
        
    }

}

/*END OF FILE*/