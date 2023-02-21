<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class ReadingQuran extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->q_pdf = 'quran_pdf';
    }
    
    public function quran_get(){
        $data = $this->db->select('*')->from($this->q_pdf)->get()->result();
        if ($data) {
            $this->response([
                "status" => TRUE,
                "data" => $data,
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => FALSE,
                "message" => "Data Not Found"
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function save_post()
    {
        $surahName = $this->input->post("surahName");
        $ayat = $this->input->post("ayat");
        $date = $this->input->post("date");
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
            "surahName",
            "Surah Name",
            "required|min_length[1]",
            array(
                'min_length' => 'Surah Name should be minimum 1 digits',
                'required' => 'Surah Name must be specified',
            )
        );
        $this->form_validation->set_rules(
            "ayat",
            "Ayat",
            "required|min_length[1]",
            array(
                'min_length' => 'Ayat should be bigger than 1 characters',
                'required' => 'Ayat must be Specified',
            )
        );
        if ($this->form_validation->run() == False) {

            // very important query "LIFES SAVER"
            $error = strip_tags(validation_errors());

            $this->response([
                "status" => False,
                "message" => $error
            ], REST_Controller::HTTP_BAD_REQUEST);
        } else {

            $img  = $this->security->xss_clean($this->post('img'));
            if (!empty($_FILES['img'])) {
                $fileName = $_FILES['img']['name'];

                $config['file_name'] = $fileName;
                $config['upload_path'] = './assets/uploads/images/quran/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg|zip|pdf';
                $config['max_size']     = '1024000';
                $config['max_width'] = '6000';
                $config['max_height'] = '6000';

                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('img')) {
                    $message = 'but '.strip_tags($this->upload->display_errors());
                    #redirect("employee/view?I=" .base64_encode($eid));
                } else {
                    $path = $this->upload->data();
                    $img = $path['file_name'];
                    $message = '';
                }
            }else{
                $message = '';
            }

            $data = array(
                "SurahName" => $surahName,
                "Ayat" => $ayat,
                "img" => $img,
                // "date" => $date,
                "status" => "draft"
            );
            if ($date != null) {
                $data['date'] = $date;
            } else {
                $data['date'] = '';
            }

            $insertData = $this->db->insert("readingquran", $data);

            if ($insertData) {
                $this->response([
                    'status' => TRUE,
                    'message' => "Surah Added Successfully {$message}",
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

    public function readingquran_post($u_id = '')
    {
        $id = $this->post('id');
        $surahName = $this->input->post("surahName");
        $ayat = $this->input->post("ayat");
        $date = $this->input->post("date");
        $img  = $this->security->xss_clean($this->post('img'));
        $this->form_validation->set_rules(
            "date",
            "Date",
            "required|min_length[1]",
            array(
                'min_length' => 'Date should be minimum 1 digits',
                'required' => 'Date must be specified',
            )
        );
        $this->form_validation->set_rules(
            "surahName",
            "Surah Name",
            "required|min_length[1]",
            array(
                'min_length' => 'Surah Name should be minimum 1 digits',
                'required' => 'Surah Name must be specified',
            )
        );
        $this->form_validation->set_rules(
            "ayat",
            "Ayat",
            "required|min_length[1]",
            array(
                'min_length' => 'Ayat should be bigger than 1 characters',
                'required' => 'Ayat must be Specified',
            )
        );
        if ($this->form_validation->run() == False) {

            // very important query "LIFES SAVER"
            $error = strip_tags(validation_errors());

            $this->response([
                "status" => False,
                "message" => $error
            ], REST_Controller::HTTP_BAD_REQUEST);
        } else {

            
            if (!empty($_FILES['img'])) {
                $fileName = $_FILES['img']['name'];

                $config['file_name'] = $fileName;
                $config['upload_path'] = './assets/uploads/images/quran/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg|zip|pdf';
                $config['max_size']     = '1024000';
                $config['max_width'] = '6000';
                $config['max_height'] = '6000';

                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('img')) {
                    $message = 'but '.strip_tags($this->upload->display_errors());
                    #redirect("employee/view?I=" .base64_encode($eid));
                } else {
                    $path = $this->upload->data();
                    $img = $path['file_name'];
                    $message = '';
                }
            }else{
                $message = '';
            }


            $key =  $this->db->query("SELECT * FROM `moq_users` WHERE u_id = '$u_id' AND roles = 'admin'")->row();
            if ($key->roles == 'admin' && !empty($id)) {
                // if (!empty($_FILES['img']['name'])) {
                    if(strlen($_FILES['img']['name']) <= 50){
                    $data = array();
                    if (!empty($id)) {
                        $data['id'] = $id;
                    }
                    if (!empty($surahName)) {
                        $data['surahName'] = $surahName;
                    }
                    if (!empty($ayat)) {
                        $data['ayat'] = $ayat;
                    }
                    if (!empty($img)) {
                        $data['img'] = $img;
                    }
                    if ($date != null) {
                        $data['date'] = $date;
                    } else {
                        $data['date'] = '';
                    }
                    $data["status"] = 'Posted';
                    

                    $insertData = $this->db->update('readingquran', $data, array('id' => $id));

                    if ($insertData) {
                        $this->response([
                            'status' => TRUE,
                            'message' => "Surah Added Successfully {$message}",
                            'data' => $data
                        ], REST_Controller::HTTP_OK);
                    } else {
                        $this->response([
                            "status" => False,
                            "Message" => "Failed"
                        ], REST_Controller::HTTP_BAD_REQUEST);
                    }
                
                    }else{
                        $this->response([
                            "status" => False,
                            "Message" => "image name should not be more than 50 charachters"
                        ], REST_Controller::HTTP_BAD_REQUEST);
                    }
                        // print_r(strlen($_FILES['img']['name']));die();
                // }else{
                //     $this->response([
                //         "status" => False,
                //         "Message" => "img field should not be Empty or Zero"
                //     ], REST_Controller::HTTP_BAD_REQUEST);
                // }
                    // print_r(strlen($_FILES['img']['name']));die();
            } else if ($key->roles == 'admin' && $id == null) {
                // if (!empty($_FILES['img']['name'])) {
                    // if(strlen($_FILES['img']['name']) <= 50){
                        $data = array();
                        if (!empty($surahName)) {
                            $data['surahName'] = $surahName;
                        }
                        if (!empty($ayat)) {
                            $data['ayat'] = $ayat;
                        }
                        if (!empty($img)) {
                            $data['img'] = $img;
                        }
                        if ($date != null) {
                            $data['date'] = $date;
                        } else {
                            $data['date'] = '';
                        }
                        $data["status"] = 'Posted';
    
                        $insertData = $this->db->insert('readingquran', $data);
    
                            if ($insertData) {
                                $this->response([
                                    'status' => TRUE,
                                    'message' => "Surah Added Successfully {$message}",
                                    'data' => $data
                                ], REST_Controller::HTTP_OK);
                            } else {
                                $this->response([
                                    "status" => False,
                                    "Message" => "Failed"
                                ], REST_Controller::HTTP_BAD_REQUEST);
                            }
                        // }else{
                        //     $this->response([
                        //         "status" => False,
                        //         "Message" => "image name should not be more than 50 charachters"
                        //     ], REST_Controller::HTTP_BAD_REQUEST);
                        // }
                        // print_r(strlen($_FILES['img']['name']));die();
                    // }else{
                    //     $this->response([
                    //         "status" => False,
                    //         "Message" => "img field should not be Empty or Zero"
                    //     ], REST_Controller::HTTP_BAD_REQUEST);
                    // }
                } else {
                    $this->response([
                        "status" => False,
                        "message" => "Do not have Access"
                    ],REST_Controller::HTTP_BAD_REQUEST);
            }
        }
    }

    public function readingquran_get()
    {
        $this->db->select('*');
        // $this->db->select('ayat');
        // $this->db->select('img');
        $this->db->from('readingquran');
        $this->db->order_by('date','DESC');
        $this->db->order_by('id','DESC');
        $surah = $this->db->get()->result();

        if ($surah) {
            $this->response([
                "status" => TRUE,
                "url" => 'https://softdigit.in/moq/assets/uploads/images/quran/',
                "surahName" => $surah,
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => FALSE,
                "message" => "Data Not Found"
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
    
    public function readingquran_delete($id = '')
    {
        $u_id = $this->input->get('u_id');
        $key =  $this->db->query("SELECT * FROM `moq_users`WHERE u_id = '$u_id' AND roles = 'admin'")->result();
        if ($key) {
            $data = $this->db->select('*')->from("readingquran")->where("id = '$id'")->get()->row();
            $deleteData = $this->db->delete("readingquran", array('id' => $id));
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
