<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class DailyTilawat extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function save_post($id = '')
    {
        $dua = $this->input->post("Dua");
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where("u_id = $id");

        $this->form_validation->set_rules(
            "Dua",
            "Dua",
            "required|min_length[1]",
            array(
                'min_length' => 'Dua should be minimum 1 digits',
                'required' => 'Dua must be specified',
            )
        );
       
        if ($this->form_validation->run() == False) {

            // very important query "LIFES SAVER"
            $error = validation_errors();

            $this->response([
                "status" => False,
                "error" => "Invalid Details",
                "message" => $error
            ], REST_Controller::HTTP_BAD_REQUEST);
        } else {

            $data = array(
                "Dua" => $dua,
                "u_id"=> $id
                //  "status" => "draft"
            );

            $insertData = $this->db->insert("daily_tilawat", $data);

            if ($insertData) {
                $this->response([
                    'status' => TRUE,
                    'message' => "Dua Saved",
                    'data' => $data
                ], REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    "status" => False,
                    "Message" => "Failed"
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        }
    }

    public function dailytilawat_get($id='')
    {
        $this->db->select('*');
        $this->db->from('daily_tilawat');
        $this->db->where("u_id = '$id'");
        $tilawat = $this->db->get()->result();

        if ($tilawat) { //$surah && 
            $this->response([
                "status" => TRUE,
                "tilawat" => $tilawat,
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => FALSE,
                "message" => "Data Not Found"
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}