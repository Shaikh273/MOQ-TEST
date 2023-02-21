<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class UnderstandIslam extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    // public function understandislam_delete($u_id = '', $id = '')
    // {
    //     $key =  $this->db->query("SELECT * FROM `moq_users` WHERE u_id = '$u_id' AND roles = 'admin'")->result();
    //     if ($key) {
    //         $deleteData = $this->db->delete("understandislam", array('id' => $id));
    //         if ($deleteData) {
    //             $this->response([
    //                 "status" => TRUE,
    //                 "id" => $id,
    //                 "message" => "Data Deleted "
    //             ], REST_Controller::HTTP_OK);
    //         } else {
    //             $this->response([
    //                 "status" => FALSE,
    //                 "message" => "Unable to Delete"
    //             ], REST_Controller::HTTP_BAD_REQUEST);
    //         }
    //     }else{
    //         $this->response([
    //             "status" => False,
    //             "message" => "Do not have Access"
    //         ]);
    //     }
    // }
    
    public function save_post($id = '')
    {
        $key =  $this->db->query("SELECT * FROM `moq_users`WHERE u_id = '$id' AND roles = 'admin'")->result();
        $id = $this->input->post("id");
        $TopicName = $this->input->post("TopicName");
        $Description = $this->input->post("Description");
        $Date = $this->input->post("Date");
        // $Date = "";

        $this->form_validation->set_rules(
            "date",
            "Date",
            "required",
            array(
                'required' => 'Date must be Given'
            )
        );
        $this->form_validation->set_rules(
            "TopicName",
            "TopicName",
            "required",
            array(
                'required' => 'Topic Name must be Given'
            )
        );
        $this->form_validation->set_rules(
            "Description",
            "Description",
            "required",
            array(
                'required' => 'Description must be Given'
            )
        );
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

        if ($this->form_validation->run() == False) {

            // very important query "LIFES SAVER"
            $error = strip_tags(validation_errors());

            $this->response([
                "status" => False,
                "message" => $error
            ], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            if ($key && $id == null) {
                $data = array(
                    "img" => $img,
                    "TopicName" => $TopicName,
                    "Description" => $Description,
                    "status" => "draft"
                );

                if ($Date != null) {
                    $data['Date'] = $Date;
                } else {
                    $data['Date'] = '';
                }

                $insertData = $this->db->insert('understandislam', $data);

                if ($insertData) {
                    $this->response([
                        'status' => TRUE,
                        'message' => "Topic Added Successfully {$message}",
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
                    "img" => $img,
                    "TopicName" => $TopicName,
                    "Description" => $Description,
                    // "Date" => $Date,
                    "status" => "draft"
                );

                if ($Date != null) {
                    $data['Date'] = $Date;
                } else {
                    $data['Date'] = '';
                }
                
                $insertData = $this->db->update('understandislam', $data, array('id' => $id));

                if ($insertData) {
                    $this->response([
                        'status' => TRUE,
                        'message' => "Topic Updated Successfully {$message}",
                        'data' => $data
                    ], REST_Controller::HTTP_OK);
                } else {
                    $this->response([
                        "status" => False,
                        "Message" => "Failed"
                    ], REST_Controller::HTTP_BAD_REQUEST);
                } 
                }else {
                $this->response([
                    "status" => False,
                    "message" => "Do not have Access"
                ]);
            }
        }
    }

    public function post_post($id = '')
    {
        $key =  $this->db->query("SELECT * FROM `moq_users`WHERE u_id = '$id' AND roles = 'admin'")->result();
        $id = $this->post('id');
        $Description = $this->input->post("Description");
        $TopicName = $this->input->post("TopicName");
        $Date = $this->input->post("Date");
          $this->form_validation->set_rules(
            "Date",
            "Date",
            "required",
            array(
                'required' => 'Date must be Given'
            )
        );
        $this->form_validation->set_rules(
            "TopicName",
            "TopicName",
            "required",
            array(
                'required' => 'Topic Name must be Given'
            )
        );
        $this->form_validation->set_rules(
            "Description",
            "Description",
            "required",
            array(
                'required' => 'Description must be Given'
            )
        );

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
        if ($this->form_validation->run() == False) {

            // very important query "LIFES SAVER"
            $error = strip_tags(validation_errors());

            $this->response([
                "status" => False,
                "message" => $error
            ], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            if ($key && $id == null) {
                $data = array(
                    "img" => $img,
                    "TopicName" => $TopicName,
                    "Description" => $Description,
                    "status" => "posted"
                );

                if ($Date != null) {
                     $data['Date'] = $Date == date('d-m-Y',strtotime($Date)) ? $Date : $this->response(["message"=>"Date format should be day, month and year d-m-y"],REST_Controller::HTTP_BAD_REQUEST);
                } else {
                    $data['Date'] = '';
                }

                $insertData = $this->db->insert('understandislam', $data);

                if ($insertData) {
                    $this->response([
                        'status' => TRUE,
                        'message' => "Topic Added Successfully {$message}",
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
                    "img" => $img,
                    "TopicName" => $TopicName,
                    "Description" => $Description,
                    "status" => "posted"
                );

                if ($Date != null) {
                     $data['Date'] = $Date == date('d-m-Y',strtotime($Date)) ? $Date : $this->response(["message"=>"Date format should be day, month and year d-m-y"],REST_Controller::HTTP_BAD_REQUEST);
                } else {
                    $data['Date'] = '';
                }
                
                $insertData = $this->db->update('understandislam', $data, array('id' => $id));

                if ($insertData) {
                    $this->response([
                        'status' => TRUE,
                        'message' => "Topic Upated Successfully {$message}",
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
                ]);
            }
        }
    }

    public function understandislam_get()
    {
        $this->db->select('*');
        $this->db->from('understandislam');
        $this->db->order_by('date','DESC');
        $this->db->order_by('id','DESC');
        $data = $this->db->get()->result();

        if ($data) {
            $this->response([
                "status" => TRUE,
                "url" => 'https://softdigit.in/moq/assets/uploads/images/quran/',
                "data" => $data
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => FALSE,
                "message" => "Data Not Found"
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
    
    public function understandislam_delete($u_id = '')
    {
        $id = $this->input->get('id');
        $key =  $this->db->query("SELECT * FROM `moq_users`WHERE u_id = '$u_id' AND roles = 'admin'")->result();
        if ($key) {
            $data = $this->db->select('*')->from("understandislam")->where("id = '$id'")->get()->row();
            
            // print_r($data);die();
            $deleteData = $this->db->delete("understandislam", array('id' => $id));
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