<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class SpiritualCount extends REST_Controller
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
            $today_date = date('d M Y');
            $topicName = $this->input->post("topicName");
            $desc = $this->input->post("description");
            $question = $this->input->post("question");
            $choice1 = $this->input->post("choice1");
            $choice2 = $this->input->post("choice2");
            $choice3 = $this->input->post("choice3");
            $choice4 = $this->input->post("choice4");
            $answer = $this->input->post("answer");

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
                "choice1",
                "Choice 1",
                "required|min_length[1]",
                array(
                    'min_length' => 'Choice should be minimum 1 digits',
                    'required' => 'Choice must be Specified',
                )
            );
            $this->form_validation->set_rules(
                "choice2",
                "Choice 2",
                "required|min_length[1]",
                array(
                    'min_length' => 'Choice should be minimum 1 digits',
                    'required' => 'Choice must be Specified',
                )
            );
            $this->form_validation->set_rules(
                "choice3",
                "Choice 3",
                "required|min_length[1]",
                array(
                    'min_length' => 'Choice should be minimum 1 digits',
                    'required' => 'Choice must be Specified',
                )
            );
            $this->form_validation->set_rules(
                "choice4",
                "Choice 4",
                "required|min_length[1]",
                array(
                    'min_length' => 'Choice should be minimum 1 digits',
                    'required' => 'Choice must be Specified',
                )
            );
            $this->form_validation->set_rules(
                "answer",
                "Answer",
                "required|min_length[1]",
                array(
                    'min_length' => 'Answer should be minimum 1 digits',
                    'required' => 'Answer must be Specified',
                )
            );

            $this->form_validation->set_rules(
                "topicName",
                "Topic Name",
                "required|min_length[1]",
                array(
                    'min_length' => 'Topic Name should be minimum 1 digits',
                    'required' => 'Topic Name must be specified',
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
                if (!empty($desc)) {
                    $data['description'] = $desc;
                } else {
                    $desc = '';
                }
                $data = array(
                    "Date" => $today_date,
                    "TopicName" => $topicName,
                    "Description" => $desc,
                    "img" => $img,
                    "Question" => $question,
                    "choice1" => $choice1,
                    "choice2" => $choice2,
                    "choice3" => $choice3,
                    "choice4" => $choice4,
                    "answer" => $answer,
                    "status" => "draft"
                );

                $insertData = $this->db->insert("spiritual_count", $data);

                if ($insertData) {
                    $this->response([
                        'status' => TRUE,
                        'message' => "Topic Saved ",
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

    public function spiritual_Count_post($u_id = '')
    {
        $key =  $this->db->query("SELECT * FROM `moq_users`WHERE u_id = '$u_id' AND roles = 'admin'")->row();
        $id = $this->post('id');
        $topicName = $this->input->post("topicName");
        $desc = $this->input->post("description");
        $q_id = $this->input->post("q_id");
        $question = $this->input->post("question");
        $choice1 = $this->input->post("choice1");
        $choice2 = $this->input->post("choice2");
        $choice3 = $this->input->post("choice3");
        $choice4 = $this->input->post("choice4");
        $answer = $this->input->post("answer");

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
            "choice1",
            "Choice 1",
            "required|min_length[1]",
            array(
                'min_length' => 'Choice should be minimum 1 digits',
                'required' => 'Choice must be Specified',
            )
        );
        $this->form_validation->set_rules(
            "choice2",
            "Choice 2",
            "required|min_length[1]",
            array(
                'min_length' => 'Choice should be minimum 1 digits',
                'required' => 'Choice must be Specified',
            )
        );
        $this->form_validation->set_rules(
            "choice3",
            "Choice 3",
            "required|min_length[1]",
            array(
                'min_length' => 'Choice should be minimum 1 digits',
                'required' => 'Choice must be Specified',
            )
        );
        $this->form_validation->set_rules(
            "choice4",
            "Choice 4",
            "required|min_length[1]",
            array(
                'min_length' => 'Choice should be minimum 1 digits',
                'required' => 'Choice must be Specified',
            )
        );
        $this->form_validation->set_rules(
            "answer",
            "Answer",
            "required|min_length[1]",
            array(
                'min_length' => 'Answer should be minimum 1 digits',
                'required' => 'Answer must be Specified',
            )
        );

        $this->form_validation->set_rules(
            "topicName",
            "Topic Name",
            "required|min_length[1]",
            array(
                'min_length' => 'Topic Name should be minimum 1 digits',
                'required' => 'Topic Name must be specified',
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
                $q_id = $this->db->select("q_id")->from("spiritual_count")->order_by('q_id', 'DESC')->get()->row();
                if (!empty($q_id)) {
                    // print_r($q_id);die();
                    $q_id = $q_id->q_id;
                    $q_id = $q_id + 1;
                } else {
                    $q_id = 1;
                }
                if (!empty($desc)) {
                    $data['description'] = $desc;
                } else {
                    $desc = '';
                }

                $data = array(
                    "q_id" => $q_id,
                    "topicName" => $topicName,
                    "description" => $desc,
                    "question" => $question,
                    "choice1" => $choice1,
                    "choice2" => $choice2,
                    "choice3" => $choice3,
                    "choice4" => $choice4,
                    "answer" => $answer,
                    "img" => $img,
                    "status" => 'Posted',
                );

                $insertData = $this->db->insert('spiritual_count', $data);

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
                $q_id = $this->db->select("q_id")->from("spiritual_count")->order_by('q_id', 'DESC')->get()->row();
                if (!empty($q_id)) {
                    // print_r($q_id);die();
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
                if (!empty($topicName)) {
                    $data['topicName'] = $topicName;
                }
                if (!empty($desc)) {
                    $data['description'] = $desc;
                }
                if (!empty($question)) {
                    $data['question'] = $question;
                }
                if (!empty($choice1)) {
                    $data['choice1'] = $choice1;
                }
                if (!empty($choice2)) {
                    $data['choice2'] = $choice2;
                }
                if (!empty($choice3)) {
                    $data['choice3'] = $choice3;
                }
                if (!empty($choice4)) {
                    $data['choice4'] = $choice4;
                }
                if (!empty($answer)) {
                    $data['answer'] = $answer;
                }
                if (!empty($img)) {
                    $data['img'] = $img;
                }
                $data["status"] = 'Posted';

                $insertData = $this->db->update('spiritual_count', $data, array('id' => $id));

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

    public function spiritual_Count_get()
    {
        $this->db->select('*');
        $this->db->from('spiritual_count');
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
    
    public function spiritualcount_delete($u_id = '', $id = '')
    {
        $key =  $this->db->query("SELECT * FROM `moq_users`WHERE u_id = '$u_id' AND roles = 'admin'")->result();
        if ($key) {
            $data = $this->db->select('*')->from("spiritual_count")->where("id = '$id'")->get()->row();
            $deleteData = $this->db->delete("spiritual_count", array('id' => $id));
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