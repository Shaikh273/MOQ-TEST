<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class QnA extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function save_post($u_id = '')
    {
        $key =  $this->db->query("SELECT * FROM `moq_users` WHERE u_id = '$u_id' AND roles = 'admin'")->row();
        if ($key->roles == 'admin') {
            $date = $this->input->post("date");
            $question = $this->input->post("question");

            $this->form_validation->set_rules(
                "question",
                "Question",
                "required|min_length[1]",
                array(
                    'min_length' => 'Question should be minimum 1 digits',
                    'required' => 'Question should not be Empty',
                )
            );
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

            if ($this->form_validation->run() == False) {

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
                    // "Date" => $date,
                    "img" => $img,
                    "Question" => $question,
                    "status" => "draft"
                );
                if ($date != null) {
                    $data['date'] = $date;
                } else {
                    $data['date'] = '';
                }

                $insertData = $this->db->insert("qna", $data);

                if ($insertData) {
                    $this->response([
                        'status' => TRUE,
                        'message' => "Question Saved ",
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

    public function QnA_post($u_id = '')
    {
        $key =  $this->db->query("SELECT * FROM `moq_users`WHERE u_id = '$u_id' AND roles = 'admin'")->row();
        $id = $this->post('id');
        $q_id = $this->input->post("q_id");
        $date = $this->input->post("date");
        $question = $this->input->post("question");

        $this->form_validation->set_rules(
            "question",
            "Question",
            "required|min_length[1]",
            array(
                'min_length' => 'Question should be minimum 1 digits',
                'required' => 'Question should not be Empty',
            )
        );
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

        if ($this->form_validation->run() == False) {
            $error = strip_tags(validation_errors());

            $this->response([
                "status" => False,
                "error" => "Invalid Details",
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
                    $message = 'but ' . strip_tags($this->upload->display_errors());
                    #redirect("employee/view?I=" .base64_encode($eid));
                } else {
                    $path = $this->upload->data();
                    $img = $path['file_name'];
                    $message = '';
                }
            } else {
                $img = '';
                $message = '';
            }
            if ($key->roles == 'admin' && $id == null) {
                $q_id = $this->db->select("q_id")->from("qna")->order_by('q_id', 'DESC')->get()->row();
                if (!empty($q_id)) {
                    $q_id = $q_id->q_id;
                    $q_id = $q_id + 1;
                } else {
                    $q_id = 1;
                }

                $data = array(
                    "q_id" => $q_id,
                    "question" => $question,
                    "img" => $img,
                    "status" => 'Posted',
                );
                if ($date != null) {
                    $data['date'] = $date == date('d-m-Y',strtotime($date)) ? $date : $this->response(["message"=>"Date format should be day, month and year d-m-y"],REST_Controller::HTTP_BAD_REQUEST);
                } else {
                    $data['date'] = '';
                }

                $insertData = $this->db->insert('qna', $data);

                if ($insertData) {
                    $this->response([
                        'status' => TRUE,
                        'message' => "Question Added Successfully{$message}",
                        'data' => $data
                    ], REST_Controller::HTTP_OK);
                } else {
                    $this->response([
                        "status" => False,
                        "Message" => "Failed"
                    ], REST_Controller::HTTP_BAD_REQUEST);
                }
            } elseif ($key->roles == 'admin' && !empty($id)) {
                $q_id = $this->db->select("q_id")->from("qna")->order_by('q_id', 'DESC')->get()->row();
                if (!empty($q_id->q_id)) {
                    $q_id = $q_id->q_id;
                    $q_id = $q_id + 1;
                } else {
                    $q_id = 1;
                }
                $data = array();
                if (!empty($id)) {
                    $data['id'] = $id;
                }
                if (!empty($q_id)) {
                    $data['q_id'] = $q_id;
                }
                if (!empty($question)) {
                    $data['question'] = $question;
                }
                if (!empty($img)) {
                    $data['img'] = $img;
                }
                if ($date != null) {
                    $data['date'] = $date == date('d-m-Y',strtotime($date)) ? $date : $this->response(["message"=>"Date format should be day, month and year d-m-y"],REST_Controller::HTTP_BAD_REQUEST);
                } else {
                    $data['date'] = '';
                }
                $data["status"] = 'Posted';

                $insertData = $this->db->update('qna', $data, array('id' => $id));

                if ($insertData) {
                    $this->response([
                        'status' => TRUE,
                        'message' => "Question Added Successfully{$message}",
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

    public function QnA_get()
    {
        $this->db->select('*');
        $this->db->from('qna');
        $this->db->order_by('date','DESC');
        $this->db->order_by('id','DESC');
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

    public function QnA_delete($u_id = '')
    {
        $key =  $this->db->query("SELECT * FROM `moq_users`WHERE u_id = '$u_id' AND roles = 'admin'")->result();
        if ($key) {
            $id = $this->input->get('id');
            $data = $this->db->select('*')->from("qna")->where("id = '$id'")->get()->row();
            $deleteData = $this->db->delete("qna", array('id' => $id));
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
