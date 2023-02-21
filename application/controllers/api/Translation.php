<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class Translation extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function search_get()
    {
        $search1 = $this->input->get("sura");
        $search2 = $this->input->get("aya");
        // print_r($search1);die();
        if ($search1 != '' && $search2 == '') {
            $sql = $this->db->select("*")->from("quran")->like('name',$search1)->get()->result();//sura = '$search1' OR 
            if ($sql) {
                $this->response([
                    "status" => true,
                    "data" => $sql
                ]);
            } else {
                $this->response([
                    "status" => FALSE,
                    "message" => "Data Not Found"
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        } else if ($search2 != "" && $search1 != '') {
            $sql = $this->db->select("*")->from("quran")->like(array('aya'=>$search2,'name'=>$search1))->get()->result();
            if ($sql) {
                $this->response([
                    "status" => true,
                    "data" => $sql
                ]);
            } else {
                $this->response([
                    "status" => False,
                    "message" => "invalid Data"
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response([
                "status" => FALSE,
                "message" => "Data Not Found"
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function translateUrdu_get()
    {
        $search1 = $this->input->get("sura");
        $search2 = $this->input->get("aya");
        if ($search1 != '' && $search2 == '') {
            $sql = $this->db->select("translation_Urdu")->from("quran")->where("sura = '$search1' || name = '$search1'")->get()->result();
            if ($sql) {
                $this->response([
                    "status" => true,
                    "data" => $sql
                ]);
            } else {
                $this->response([
                    "status" => FALSE,
                    "message" => "Data Not Found"
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        } else if ($search2 != "" && $search1 != '') {
            $sql = $this->db->select("translation_Urdu")->from("quran")->where("aya = '$search2' && sura = '$search1' || name = '$search1'")->get()->result();
            if ($sql) {
                $this->response([
                    "status" => true,
                    "data" => $sql
                ]);
            } else {
                $this->response([
                    "status" => False,
                    "message" => "invalid Data"
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response([
                "status" => FALSE,
                "message" => "Data Not Found"
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function translateEnglish_get()
    {
        $search1 = $this->input->get("sura");
        $search2 = $this->input->get("aya");
        if ($search1 != '' && $search2 == '') {
            $sql = is_numeric($search1) ? $this->db->select("translation_english")->from("quran")->where("sura = '$search1'")->get()->result() : $this->db->select("translation_english")->from("quran")->like('name',$search1)->get()->result();
            // $sql = $this->db->select("sura,name,aya,translation_english")->from("quran")->where("sura = '$search1' || name = '$search1'")->get()->result();
            if ($sql) {
                $this->response([
                    "status" => true,
                    "data" => $sql
                ]);
            } else {
                $this->response([
                    "status" => FALSE,
                    "message" => "Data Not Found"
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        } else if ($search2 != "" && $search1 != '') {
            
            $sql = is_numeric($search1) ? $this->db->select("translation_english")->from("quran")->where("aya = '$search2' AND sura = '$search1'")->get()->result() : $this->db->select("translation_english")->from("quran")->where("aya = '$search2'")->like('name',$search1)->get()->result();
            // print_r($sql);die();
            // $sql = $this->db->select("translation_english")->from("quran")->where("aya = '$search2' && (sura = '$search1' || name = '$search1')")->get()->result(); sura,name,aya,
            if ($sql) {
                $this->response([
                    "status" => true,
                    "data" => $sql
                ]);
            } else {
                $this->response([
                    "status" => False,
                    "message" => "invalid Data"
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response([
                "status" => FALSE,
                "message" => "Data Not Found"
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function translateHindi_get()
    {
        $search1 = $this->input->get("sura");
        $search2 = $this->input->get("aya");
        if ($search1 != '' && $search2 == '') {
            $sql = $this->db->select("translation_Hindi")->from("quran")->where("sura = '$search1' || name = '$search1'")->get()->result();
            if ($sql) {
                $this->response([
                    "status" => true,
                    "data" => $sql
                ]);
            } else {
                $this->response([
                    "status" => FALSE,
                    "message" => "Data Not Found"
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        } else if ($search2 != "" && $search1 != '') {
            $sql = $this->db->select("translation_Hindi")->from("quran")->where("aya = '$search2' && sura = '$search1' || name = '$search1'")->get()->result();
            if ($sql) {
                $this->response([
                    "status" => true,
                    "data" => $sql
                ]);
            } else {
                $this->response([
                    "status" => False,
                    "message" => "invalid Data"
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response([
                "status" => FALSE,
                "message" => "Data Not Found"
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
    public function surah_get()
    {
        $this->db->select('surah.*,CONCAT(quran_pdf.surah_pdf,quran_pdf.format) AS surah_pdf');
        $this->db->from('surah');
        $this->db->join('quran_pdf','surah.number = quran_pdf.id');
        $data = $this->db->get()->result();
        $url = 'https://softdigit.in/moq/assets/uploads/quran_pdf/';

        if ($data) {
            $this->response([
                "status" => TRUE,
                "url" => $url,
                "data" => $data
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => FALSE,
                "message" => "Data Not Found"
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}
