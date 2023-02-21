<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class Register extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        // $this->load->helper(array('form', 'url'));
        // $this->load->library('form_validation');
    }
    
    public function register_get() {
        $data = $this->db->select('*')->from('moq_users')->get()->result();
        if($data){
            $this->response([
                'status' => TRUE,
                'data' => $data
            ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                "status" => False,
                "Message" => "Internal Server Error"
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function register_post()
    {
      $id = $this->security->xss_clean($this->input->post("id"));
      $fullName = $this->security->xss_clean($this->input->post("fullName"));
      $mobileNo = $this->security->xss_clean($this->input->post("mobileNo"));
      $bio = $this->security->xss_clean($this->input->post("bio"));
      $roles = $this->security->xss_clean($this->input->post("roles"));
    //   $img = $this->input->post("img");
      
      if(!empty($mobileNo) && empty($id) && empty($fullName) && empty($bio)){
        $login_data = $this->db->select('*')->from('moq_users')->where("mobile_no = '$mobileNo'")->get()->row();
        $login = $this->db->select('mobile_no')->from('moq_users')->where("mobile_no = '$mobileNo'")->get()->row()->mobile_no ?? '';
        if(!empty($login)){
            $this->response([
                'status' => true,
                'message' => "Login Successfully",
                'data' => $login_data
            ], REST_Controller::HTTP_OK);  
        }
      }
      
      if (!empty($_FILES['img'])) {
        $fileName = $_FILES['img']['name'];

        $config['file_name'] = $fileName;
        $config['upload_path'] = './assets/uploads/images/users/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size']     = '1024000';
        $config['max_width'] = '6000';
        $config['max_height'] = '6000';

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('img')) {
            echo $this->upload->display_errors();
            $img = '';
            #redirect("employee/view?I=" .base64_encode($eid));
        } else {
            $path = $this->upload->data();
            $img = $path['file_name'];
        }
     }else{
         $img = '';
     }
     
      if(!empty($id)){
          $data = array();
          if(!empty($fullName)) {
              $data["full_name"] = $fullName;
          }
          if(!empty($img)) {
              $data["img"] = $img;
          }
          if(!empty($bio)) {
              $data["bio"] = $bio;
          }
          if(!empty($roles)) {
              $data["roles"] = $roles;
          }
          
        $updateData = $this->db->update("moq_users", $data, array('id'=>$id));
        $given_data = $this->db->select("*")->from("moq_users")->where("id = '$id'")->get()->row();
                // print_r($given_data);die();
        if ($updateData) {
            $this->response([
                'status' => TRUE,
                'message' => "Profile Updated Successfully",
                'data' => $given_data
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => False,
                "Message" => "Registration Failed"
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
      }else{ // if($id == null || empty($id))
          $first = explode(" ", $fullName);
          $u_id = $first[0] . "_" . rand(1000, 5000);
    
          $this->form_validation->set_rules(
                "fullName",
                "Full Name",
                "required|alpha_numeric_spaces|min_length[4]|max_length[15]",
                array(
                    'min_length' => 'Name should be bigger than 4 characters',
                    'max_length' => 'Name should not be bigger than 15 characters',
                    'required' => 'This Field must be filled',
                    'alpha_numeric_spaces' => 'Please Enter Alphabets'
                )
            );
            $this->form_validation->set_rules(
                "mobileNo",
                "Mobile No",
                "required|numeric|is_unique[moq_users.mobile_no]|min_length[10]|max_length[15]",
                array(
                    'max_length' => 'Mobile no. should be maximum 15 digits',
                    'min_length' => 'Mobile no. should be minimum 10 digits',
                    'is_unique' => 'Mobile no. already used',
                    'required' => 'This Field must be filled',
                    'numeric' => 'Please Enter only Numbers'
                )
            );
    
            if ($this->form_validation->run() == false) {
    
                // very important query "LIFES SAVER"
                $error = strip_tags(validation_errors());
    
                $this->response([
                    "status" => False,
                    "message" => "Invalid Details",
                    "error" => $error
                ], REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $data = array(
                    "full_name" => $fullName,
                    "mobile_no" => $mobileNo,
                    "u_id" => $u_id,
                    "img" => $img,
                    "roles" => $roles,
                    "bio" => $bio
                );
    
                $insertData = $this->db->insert("moq_users", $data);
                if ($insertData) {
                    $this->response([
                        'status' => TRUE,
                        'message' => "You're Registered Successfully",
                        'data' => $data
                    ], REST_Controller::HTTP_OK);
                } else {
                    $this->response([
                        "status" => False,
                        "Message" => "Registration Failed"
                    ], REST_Controller::HTTP_BAD_REQUEST);
                }
            }
        } 
    }
}
