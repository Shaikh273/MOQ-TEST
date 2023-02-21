<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class Users extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function profile_post($u_id = '')
    {
        $u_id = $this->security->xss_clean($u_id);
        $bio = $this->security->xss_clean($this->input->post("bio"));
        $fullName = $this->security->xss_clean($this->input->post("fullName"));
        $first = explode(" ", $fullName);
        $emp_id = $first[0] . "_" . rand(1000, 5000);
        $mobile_no = $this->security->xss_clean($this->input->post("mobile_no"));
        $img = $this->security->xss_clean($this->post('img'));
        $roles = $this->security->xss_clean($this->input->post("roles"));

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
                $message = 'but ' . strip_tags($this->upload->display_errors());
                #redirect("employee/view?I=" .base64_encode($eid));
            } else {
                $path = $this->upload->data();
                $img = $path['file_name'];
                $message = '';
            }
        } else {
            $message = '';
        }

        // $key =  $this->db->query("SELECT * FROM `moq_users` WHERE u_id = '$u_id'")->row();
        // if ($key->roles == 'admin' && !empty($id)) {
        if (($bio && $fullName) || $img) {
            $data = array();
            if (!empty($fullName)) {
                $data['full_name'] = $fullName;
            }
            if (!empty($bio)) {
                $data['bio'] = $bio;
            }
            if (!empty($mobile_no)) {
                $data['mobile_no'] = $mobile_no;
            }
            if (!empty($img)) {
                $data['img'] = $img;
            }
            if (!empty($roles)) {
                $data['roles'] = $roles;
            }

            if(!empty($u_id)){
            // print_r($data);die();
                $updateData = $this->db->update('moq_users', $data, array('u_id' => $u_id));
                if($updateData) {
                    $this->response([
                        'status' => TRUE,
                        'message' => "Profile Upated {$message}",
                        'data' => $data
                    ], REST_Controller::HTTP_OK);
                }else{
                    $this->response([
                    "status" => False,
                    "Message" => "Failed"
                    ], REST_Controller::HTTP_BAD_REQUEST);
                }
            }else{
                $this->form_validation->set_rules('mobile_no','Mobile No','is_unique[moq_users.mobile_no]', array('is_unique'=>'This Mobile No already Taken'));
                if($this->form_validation->run() == false){
                    $error = strip_tags(validation_errors());
                    $this->response([
                        "status" => false,
                        "Message" => $error
                    ], REST_Controller::HTTP_BAD_REQUEST);
                }else{
                    $data['u_id'] = $emp_id;
                    $insertData = $this->db->insert('moq_users', $data);
                    if($insertData) {
                        $this->response([
                            'status' => TRUE,
                            'message' => "Profile Added {$message}",
                            'data' => $data
                        ], REST_Controller::HTTP_OK);
                    }else{
                        $this->response([
                        "status" => False,
                        "Message" => "Failed"
                        ], REST_Controller::HTTP_BAD_REQUEST);
                    }
                }
                
            }

            // if($insertData) {
                
            // }else if($updateData){
                
            // } else {
                
            // }
        }
    }

    public function profile_get($u_id = '')
    {
        $this->db->select('*');
        $this->db->from('moq_users');
        if(!empty($u_id)){
            $this->db->where("u_id = '$u_id'");
        }
        $data = $this->db->get()->result();
        // print_r($data);die();
        if ($data) { //$surah && 
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
    
}
