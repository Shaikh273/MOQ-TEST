<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class UnderstandQuran extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    public function search_get()
    {
        $search1 = $this->input->get("sura");
        $this->form_validation->set_rules(
			"sura",
			"sura",
			"required",
			array(
				'required' => 'Surah Cannot be Empty',
			)
		);
        $search2 = $this->input->get("aya");
        $search3 = $this->input->get("text");
        // $text = $this->db->select("text")->from("quran")->get();
        if ($search1 != '' && $search2 == '' && $search3 == '') {
            $sql = $this->db->select("*")->from("quran")->where("sura = '$search1'")->get()->result();
            if ($sql) {
                $this->response([
                    "status" => true,
                    "data" => $sql
                ]);
            } else {
                $this->response([
                    "status" => FALSE,
                    "message" => "Invalid surah"
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        } else if ($search2 != "" && $search1 != '') {
            $sql = $this->db->select("*")->from("quran")->where("aya = '$search2' && sura = '$search1'")->get()->result();
            if ($sql) {
                $this->response([
                    "status" => true,
                    "data" => $sql
                ]);
            } else {
                $this->response([
                    "status" => False,
                    "message" => "Surah doesn't contain the ayat"
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        } else if ($search3 != '' && $search1 == '' && $search2 == '') {
            $sql = $this->db->select("*")->from("quran")->where("text = '$search3'")->get()->result();
            if ($sql) {
                $this->response([
                    "status" => true,
                    "data" => $sql
                ]);
            } else {
                $this->response([
                    "status" => False,
                    "message" => "Verse not found"
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        } else if ($search3 != '' && $search1 != '') {
            $sql = $this->db->select("*")->from("quran")->where("text = '$search3' && sura = '$search1'")->get()->result();
            if ($sql) {
                $this->response([
                    "status" => true,
                    "data" => $sql
                ]);
            } else {
                $this->response([
                    "status" => False,
                    "message" => "Surah & Verse did not match"
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response([
                "status" => FALSE,
                "message" => "Fields Cannot be Empty"
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }


    public function save_post($id = '')
    {
        $key =  $this->db->query("SELECT * FROM `moq_users` WHERE u_id = '$id' AND roles = 'admin'")->result();
        $id = $this->post('id');
        $verse = $this->input->post("verse");
        $date = $this->input->post("date");
        $tarjama = $this->input->post("tarjama");
        $tafseer = $this->input->post("tafseer");
        
        $this->form_validation->set_rules(
            "date",
            "Date",
            "required|max_length[11]",
            array(
                // 'max_length' => 'Verse should be maximum 10 digits',
                'min_length' => 'Date should be maximum 11 digits',
                'required' => 'Date must be filled'
            )
        );
        $this->form_validation->set_rules(
            "verse",
            "Verse",
            "required|min_length[1]",
            array(
                // 'max_length' => 'Verse should be maximum 10 digits',
                'min_length' => 'Verse should be minimum 1 digits',
                'required' => 'verse must be filled'
            )
        );
        $this->form_validation->set_rules(
            "tafseer",
            "tafseer",
            "required|min_length[1]",
            array(
                // 'max_length' => 'tafseer should be maximum 10 digits',
                'min_length' => 'tafseer should be minimum 1 digits',
                'required' => 'tafseer must be filled'
            )
        );
        $this->form_validation->set_rules(
            "tarjama",
            "tarjama",
            "required|min_length[1]",
            array(
                // 'max_length' => 'tarjama should be maximum 10 digits',
                'min_length' => 'tarjama should be minimum 1 digits',
                'required' => 'tarjama must be filled'
            )
        );

        // $sabaq = $this->db->select("sabaq_no")->from("understandquran")->order_by('sabaq_no', 'DESC')->get()->row();
        // $sabaq = $sabaq->sabaq_no;
        // // print_r($sabaq);die();
        // $sabaq = $sabaq + 1;

        if ($this->form_validation->run() == False) {

            // very important query "LIFES SAVER"
            $error = strip_tags(validation_errors());

            $this->response([
                "status" => False,
                "message" => $error
            ], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            if ($key && ($id == null || empty($id))) {
                $data = array(
                    "sabaq_no" => "0",
                    "verse" => $verse,
                    "tarjama" => $tarjama,
                    "tafseer" => $tafseer,
                    "status" => "draft"
                );
                 if ($date != null) {
                    $data['date'] = $date;
                } else {
                    $data['date'] = '';
                }
                

                $insertData = $this->db->insert('understandquran', $data);

                if ($insertData) {
                    $this->response([
                        'status' => TRUE,
                        'message' => "Sabaq Added Successfully",
                        'data' => $data
                    ], REST_Controller::HTTP_OK);
                } else {
                    $this->response([
                        "status" => False,
                        "Message" => "Failed"
                    ], REST_Controller::HTTP_BAD_REQUEST);
                }
            } else if ($key && $id) {
                $data = array(
                    "sabaq_no" => "0",
                    "verse" => $verse,
                    "tarjama" => $tarjama,
                    "tafseer" => $tafseer,
                    // "date" => $date,
                    "status" => "draft"
                );
                
                if ($date != null) {
                    $data['date'] = $date;
                } else {
                    $data['date'] = '';
                }
                
                // print_r($data);die();
                $insertData = $this->db->update('understandquran', $data, array('id' => $id));

                if ($insertData) {
                    $this->response([
                        'status' => TRUE,
                        'message' => "Sabaq Added Successfully",
                        'data' => $data
                    ], REST_Controller::HTTP_OK);
                } else {
                    $this->response([
                        "status" => False,
                        "Message" => "Failed"
                    ], REST_Controller::HTTP_BAD_REQUEST);
                }
            } else {
                $this->response([
                    "status" => False,
                    "message" => "Do not have Access"
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        }
    }

    public function post_post($id = '')
    {
        $key =  $this->db->query("SELECT * FROM `moq_users`WHERE u_id = '$id' AND roles = 'admin'")->result();
        $id = $this->post('id');
        $date = $this->input->post("date");
        $verse = $this->input->post("verse");
        $tarjama = $this->input->post("tarjama");
        $tafseer = $this->input->post("tafseer");
        // $date ='';
         $this->form_validation->set_rules(
            "date",
            "Date",
            "required|max_length[11]",
            array(
                // 'max_length' => 'Verse should be maximum 10 digits',
                'min_length' => 'Date should be maximum 11 digits',
                'required' => 'Date must be filled'
            )
        );
        $this->form_validation->set_rules(
            "verse",
            "Verse",
            "required|min_length[1]",
            array(
                // 'max_length' => 'Verse should be maximum 10 digits',
                'min_length' => 'Verse should be minimum 1 digits',
                'required' => 'verse must be filled'
            )
        );
        $this->form_validation->set_rules(
            "tafseer",
            "tafseer",
            "required|min_length[1]",
            array(
                // 'max_length' => 'tafseer should be maximum 10 digits',
                'min_length' => 'tafseer should be minimum 1 digits',
                'required' => 'tafseer must be filled'
            )
        );
        $this->form_validation->set_rules(
            "tarjama",
            "tarjama",
            "required|min_length[1]",
            array(
                // 'max_length' => 'tarjama should be maximum 10 digits',
                'min_length' => 'tarjama should be minimum 1 digits',
                'required' => 'tarjama must be filled'
            )
        );

        $sabaq = $this->db->select("sabaq_no")->from("understandquran")->order_by('sabaq_no', 'DESC')->get()->row();
        if(!empty($sabaq)){
            // print_r($sabaq);die();
            $sabaq = $sabaq->sabaq_no;
            $sabaq = $sabaq + 1;
        }else{
            $sabaq = 1;
        }

        if ($this->form_validation->run() == false) {

            // very important query "LIFES SAVER"
            $error = strip_tags(validation_errors());

            $this->response([
                "status" => False,
                "message" => $error
            ], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            if ($key && $id == '') {
                $data = array(
                    "sabaq_no" => $sabaq,
                    "verse" => $verse,
                    "tarjama" => $tarjama,
                    "tafseer" => $tafseer,
                    // "date" => $date,
                    "status" => "posted"
                );
                
                if ($date != null) {
                    $data['date'] = $date;
                } else {
                    $data['date'] = '';
                }

                $insertData = $this->db->insert('understandquran', $data);

                if ($insertData) {
                    $this->response([
                        'status' => TRUE,
                        'message' => "Sabaq Added Successfully",
                        'data' => $data
                    ], REST_Controller::HTTP_OK);
                } else {
                    $this->response([
                        "status" => False,
                        "Message" => "Failed"
                    ], REST_Controller::HTTP_BAD_REQUEST);
                }
            } else if ($key && $id != null) {
                $data = array(
                    "sabaq_no" => $sabaq,
                    "verse" => $verse,
                    "tarjama" => $tarjama,
                    "tafseer" => $tafseer,
                    // "date" => $date,
                    "status" => "posted"
                );
                
                if ($date != null) {
                    $data['date'] = $date;
                } else {
                    $data['date'] = '';
                }
                
                $insertData = $this->db->update('understandquran', $data, array('id' => $id));

                if ($insertData) {
                    $this->response([
                        'status' => TRUE,
                        'message' => "Sabaq Added Successfully",
                        'data' => $data
                    ], REST_Controller::HTTP_OK);
                } else {
                    $this->response([
                        "status" => False,
                        "Message" => "Failed"
                    ], REST_Controller::HTTP_BAD_REQUEST);
                }
            } else {
                $this->response([
                    "status" => False,
                    "message" => "Do not have Access"
                ],REST_Controller::HTTP_BAD_REQUEST);
            }
        }
    }

    public function understandquran_get()
    {
        $this->db->select('*');
        $this->db->from('understandquran');
        $data = $this->db->get()->result();

        if ($data) {
            $this->response([
                "status" => TRUE,
                "data" => $data
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => FALSE,
                "message" => "Data Not Found"
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
    
     public function understandquran_delete($u_id = '')
    {
        $id = $this->delete('id');
        $key =  $this->db->query("SELECT * FROM `moq_users`WHERE u_id = '$u_id' AND roles = 'admin'")->result();
        if ($key) {
            $data = $this->db->select('*')->from("understandquran")->where("id = '$id'")->get()->row();
            $deleteData = $this->db->delete("understandquran", array('id' => $id));
            if ($data == null) {
                $this->response([
                    "status" => FALSE,
                    "message" => "Data not found"
                ], REST_Controller::HTTP_BAD_REQUEST);
            } elseif (!empty($data)) {
                if ($deleteData) {
                    $this->response([
                        "status" => TRUE,
                        "id" => $id,
                        "message" => "Data Deleted "
                    ], REST_Controller::HTTP_OK);
                } else {
                    $this->response([
                        "status" => FALSE,
                        "message" => "Unable to Delete"
                    ], REST_Controller::HTTP_BAD_REQUEST);
                }
            } else {
                $this->response([
                    "status" => FALSE,
                    "message" => "Data not found"
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response([
                "status" => False,
                "message" => "Do not have Access"
            ]);
        }
    }
}