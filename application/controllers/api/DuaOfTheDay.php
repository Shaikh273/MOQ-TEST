<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class DuaOfTheDay extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function save_post($id = '')
    {
        $key =  $this->db->query("SELECT * FROM `moq_users`WHERE u_id = '$id' AND roles = 'admin'")->result();
        if ($key) {
            $title = $this->input->post("title");
            $dua = $this->input->post("Dua");
            $meaning = $this->input->post("Meaning");
            $fazilat = $this->input->post("Fazilat");

            $this->form_validation->set_rules(
                "title",
                "Title",
                "required",
                array(
                    'required' => 'Name of Dua must be specified',
                )
            );
            $this->form_validation->set_rules(
                "Dua",
                "Dua",
                "required|min_length[1]",
                array(
                    'min_length' => 'Dua should be minimum 1 digits',
                    'required' => 'Dua must be specified',
                )
            );
            $this->form_validation->set_rules(
                "Meaning",
                "Meaning",
                "required|min_length[1]",
                array(
                    'min_length' => 'Meaning should be bigger than 1 characters',
                    'required' => 'Meaning must be Specified',
                )
            );

            if ($this->form_validation->run() == False) {

                // very important query "LIFES SAVER"
                $error = strip_tags(validation_errors());

                $this->response([
                    "status" => False,
                    "error" => "Invalid Details",
                    "message" => $error
                ], REST_Controller::HTTP_BAD_REQUEST);
            } else {

                $img  = $this->security->xss_clean($this->post('img'));
                if (!empty($_FILES['img'])) {
                    $fileName = $_FILES['img']['name'];

                    $config['file_name'] = $fileName;
                    $config['upload_path'] = './assets/uploads/images/quran/';
                    $config['allowed_types'] = 'gif|jpg|png';
                    $config['max_size']     = '1024000';
                    $config['max_width'] = '1920';
                    $config['max_height'] = '1080';

                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload('img')) {
                        echo $this->upload->display_errors();
                        #redirect("employee/view?I=" .base64_encode($eid));
                    } else {
                        $path = $this->upload->data();
                        $img = $path['file_name'];
                    }
                }

                $data = array(
                    "title" => $title,
                    "Dua" => $dua,
                    "Meaning" => $meaning,
                    "img" => $img,
                    "Fazilat" => $fazilat,
                    "status" => "draft"
                );

                $insertData = $this->db->insert("duaoftheday", $data);

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
        } else {
            $this->response([
                "status" => False,
                "message" => "Do not have Access"
            ]);
        }
    }

    public function post_post($u_id = '')
    {
        $key =  $this->db->query("SELECT * FROM `moq_users` WHERE u_id = '$u_id' AND roles = 'admin'")->row();
        // print_r($key);die();
        $id = $this->post("id");
        $title = $this->input->post("title");
        $dua = $this->input->post("Dua");
        $meaning = $this->input->post("Meaning");
        $fazilat = $this->input->post("Fazilat");
        $date = $this->input->post("posted_date");
        
        $this->form_validation->set_rules(
            "title",
            "Title",
            "required",
            array(
                'required' => 'Name of Dua must be specified',
            )
        );
        $this->form_validation->set_rules('Dua','Dua','required',
            array(
                'required' => 'Dua must be Specified',
            )
        );
        $this->form_validation->set_rules('Meaning','Meaning','required',
            array(
                'required' => 'Meaning must be Specified',
            )
        );
        $this->form_validation->set_rules('posted_date','Posted Date','required',
            array(
                'required' => 'Date must be Specified',
            )
        );

        if ($this->form_validation->run() == false) {
            $error = strip_tags(validation_errors());
            $this->response([
                "status" => False,
                "message" => $error
            ], REST_Controller::HTTP_BAD_REQUEST);
            
        } else {
        if ($key->roles == 'admin' && !empty($id)) {
                if(!empty($title)){
                    $data['title'] = $title;
                }
                if(!empty($dua)){
                    $data['Dua'] = $dua;
                }
                if(!empty($meaning)){
                    $data['Meaning'] = $meaning;
                }
                if(!empty($fazilat)){
                    $data['Fazilat'] = $fazilat;
                }else{
                    $data['Fazilat'] = '';
                }
                if(!empty($date)){
                    $data['posted_date'] = $date;
                }
                 $data["status"] = 'Posted';
                // $data = array(
                //   "dua"=>$dua,
                //   "Meaning"=>$meaning,
                //   "fazilat"=>$fazilat,
                //   "posted_date"=>$date,
                //   "status"=>"Posted"
                // );
                
                $insertData = $this->db->update('duaoftheday', $data, array('id'=>$id));

                if ($insertData) {
                    $this->response([
                        'status' => TRUE,
                        'message' => "Dua Posted Successfully",
                        'data' => $data
                    ], REST_Controller::HTTP_OK);
                } else {
                    $this->response([
                        "status" => False,
                        "message" => "Failed"
                    ], REST_Controller::HTTP_BAD_REQUEST);
                }
            
        } else if($key->roles == 'admin' && $id == null) {
                
                if(!empty($title)){
                    $data['title'] = $title;
                }
                if(!empty($dua)){
                    $data['Dua'] = $dua;
                }
                if(!empty($meaning)){
                    $data['Meaning'] = $meaning;
                }
                if(!empty($fazilat)){
                    $data['Fazilat'] = $fazilat;
                }else{
                    $data['Fazilat'] = '';
                }
                if(!empty($date)){
                    $data['posted_date'] = $date;
                }
                 $data["status"] = 'Posted';
                // $data = array(
                //   "dua"=>$dua,
                //   "Meaning"=>$meaning,
                //   "fazilat"=>$fazilat,
                //   "posted_date"=>$date,
                //   "status"=>"Posted"
                // );
                
                $insertData = $this->db->insert('duaoftheday', $data);

                if ($insertData) {
                    $this->response([
                        'status' => TRUE,
                        'message' => "Dua Posted Successfully",
                        'data' => $data
                    ], REST_Controller::HTTP_OK);
                } else {
                    $this->response([
                        "status" => False,
                        "message" => "Failed"
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

    public function posted_get()
    {
        $date = $this->security->xss_clean($this->input->get('date'));
        if(!empty($date)){
            $this->db->select('*');
            $this->db->from('duaoftheday');
            $this->db->where(array('status'=>'posted','posted_date'=>$date));
            $this->db->order_by('posted_date','DESC');
            $this->db->order_by('id','DESC');
            $data = $this->db->get()->result();
        }else{
            $this->db->select('*');
            $this->db->from('duaoftheday');
            $this->db->where(array('status'=>'posted'));
            $this->db->order_by('id','DESC');
            $data = $this->db->get()->result();
        }


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


    public function draft_get($id = '')
    {
        $key =  $this->db->query("SELECT * FROM `moq_users`WHERE u_id = '$id' AND roles = 'admin'")->result();
        if ($key) {
           $this->db->select('*');
            // $this->db->select('status');
            $this->db->from('duaoftheday');
            $this->db->where('status', 'draft');
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
        } else {
            $this->response([
                "status" => False,
                "message" => "Do not have Access"
            ]);
        }
    }
    
    public function duaoftheday($u_id = '', $id = '')
    {
        $key =  $this->db->query("SELECT * FROM `moq_users`WHERE u_id = '$u_id' AND roles = 'admin'")->result();
        if ($key) {
            $data = $this->db->select('*')->from("duaoftheday")->where("id = '$id'")->get()->row();
            $deleteData = $this->db->delete("duaoftheday", array('id' => $id));
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
    ///// Test
    
}