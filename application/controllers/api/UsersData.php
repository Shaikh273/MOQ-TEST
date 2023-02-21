<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class UsersData extends REST_Controller
{
    // To Save and See data of Surah,daily tilawat,etc from user profile

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function usersdata_post()
    {
        $userid = $this->post("user_id");
        $daily = $this->post("daily_tilawat_id");
        $daily_id = $this->db->select("id")->from("daily_tilawat")->get()->num_rows();

        if ($daily > $daily_id || $daily == 0) { //$surah > $surah_id
            $this->response([
                "status" => FALSE,
                "message" => "Data Not Found"
            ], REST_Controller::HTTP_BAD_REQUEST);
        } else {

            $data = array(
                "user_id" => $userid,
                "daily_tilawat_id" => $daily,
            );

            $insertData = $this->db->insert("users_data", $data);

            if ($insertData) {
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
    }

    public function usersdata_get($key)
    {
        $this->db->select('users_data.id, users_data.user_id, daily_tilawat.dailyTilawat');
        $this->db->from('users_data');
        $this->db->join('daily_tilawat', 'users_data.daily_tilawat_id = daily_tilawat.id');
        $this->db->where('user_id', $key);
        $getData = $this->db->get()->result();

        if ($getData) {
            $this->response([
                "status" => TRUE,
                "data" => $getData
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => FALSE,
                "message" => "Data Not Found"
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function search_get()
    {
        $search = $this->input->get("ayat");
        $this->db->select('*');
        $this->db->from('daily_tilawat');
        $this->db->like('dailyTilawat',);
        $searchData = $this->db->get()->result();


        if ($searchData) {
            $this->response([
                "status" => TRUE,
                "ayat" => $search,
                "data" => $searchData
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => FALSE,
                "message" => "Data Not Found"
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function usersdata_delete($id = '')
    {
        $deleteData = $this->db->delete("users_data", array('id' => $id));
        if ($deleteData) {
            $this->response([
                "status" => TRUE,
                "id" => $id
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => FALSE,
                "message" => "Unable to Delete"
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}
