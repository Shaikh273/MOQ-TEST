<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class Quiz extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    
    public function quiz_get()
    {
        $todays_date = date('Y-m-d');
        $query = $this->db->select('*')->from('spiritual_count')->where("Date = '$todays_date' && status = 'Posted'")->get()->result();
        if ($query) {
            $this->response([
                "status" => TRUE,
                "data" => $query
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => FALSE,
                "message" => "Data Not Found"
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
    public function result_get($id)
    {
        $query = $this->db->select('*')->from('quiz')->where("u_id = '$id'")->get()->result();
        if ($query) {
            $this->response([
                "status" => TRUE,
                "data" => $query
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => FALSE,
                "message" => "Data Not Found"
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function quiz_post($id = '')
    {
        $key =  $this->db->query("SELECT * FROM `moq_users`WHERE u_id = '$id'")->row();
        if ($key) {
            $q_id = $this->input->post('q_id');
            $todays_date = date('Y-m-d');
            $answer = $this->input->post('answer');

            // $this->form_validation->set_rules('u_id', 'User ID', 'required|max_length[20]', array(
            //     "required" => "Please Provide User Id",
            // ));

            // $this->form_validation->set_rules('q_id', 'Question ID', 'required|max_length[20]', array(
            //     "required" => "Please Provide Question Id",
            //     "max_length" => "It should contain not more than 20 charachters"
            // ));

            $this->form_validation->set_rules(
                "answer",
                "Answer",
                "required",
                array(
                    'required' => 'Please Select an Asnwer',
                )
            );

            if ($this->form_validation->run() == FALSE) {

                $error = strip_tags(validation_errors());

                $this->response([
                    "status" => False,
                    "error" => "Invalid Details",
                    "message" => $error
                ], REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $query = $this->db->query("SELECT * FROM `spiritual_count` WHERE q_id= '$q_id' AND status = 'Posted'")->row();
                if ($query) {
                    $correct_ans = $this->db->select('answer')->from('spiritual_count')->where("q_id='$q_id'")->get()->row();
                    $correct_ans = $correct_ans->answer;

                    if ($correct_ans == $answer) {
                        $data = array(
                            "Date" => $todays_date,
                            "u_id" => $id,
                            "q_id" => $q_id,
                            "answer" => $answer,
                            "Correct_Answer" => $correct_ans,
                            "status" => "Right Ans",
                        );

                        $insertData = $this->db->insert("quiz", $data);

                        if ($insertData) {
                            $this->response([
                                'status' => TRUE,
                                'message' => "Your Response has been Successfully Submitted",
                                'data' => $data
                            ], REST_Controller::HTTP_OK);
                        } else {
                            $this->response([
                                "status" => False,
                                "Message" => "Some Error Occurred"
                            ], REST_Controller::HTTP_BAD_REQUEST);
                        }
                    } else {
                        $data = array(
                            "Date" => $todays_date,
                            "u_id" => $id,
                            "q_id" => $q_id,
                            "answer" => $answer,
                            "Correct_Answer" => $correct_ans,
                            "status" => "Wrong Ans",
                        );

                        $insertData = $this->db->insert("quiz", $data);

                        if ($insertData) {
                            $this->response([
                                'status' => TRUE,
                                'message' => "Your Response has been Successfully Submitted",
                                'data' => $data
                            ], REST_Controller::HTTP_OK);
                        } else {
                            $this->response([
                                "status" => False,
                                "Message" => "Some Error Occurred"
                            ], REST_Controller::HTTP_BAD_REQUEST);
                        }
                    }
                } else {
                    $this->response([
                        "status" => False,
                        "Message" => "No Data Found"
                    ], REST_Controller::HTTP_BAD_REQUEST);
                }
            }
        } else {
            $this->response([
                "status" => False,
                "Message" => "User Not Found"
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}