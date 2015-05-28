<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load-> model('users');
        $this->load-> model('uploadmodel');
    }

    function index($data)
    {
        if(!$this->session->userdata('is_logged_in')){
            redirect('login');
        }else {
            $this->load->view('header');
            $this->load->view('back/template/header');
            $this->load->view('back/upload/upload',$data);
            $this->load->view('footer');
        }
    }

    function do_upload()
    {
        $config['upload_path'] = getcwd() . '/uploads/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size']	= '1000000';
        $config['max_width']  = '1024';
        $config['max_width']  = '1024';
        $config['max_height']  = '768';

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload()){
            $data = array(
                "error" => "Une erreur est survenue durant le téléchargement de votre image de profil. Veuillez rééssayer. Erreur : ".$this->upload->display_errors()
            );
        }
        else{
            $data = array(
                "error" => "Image uploadée"
            );
            $upload_data = $this->upload->data();
            $this->uploadmodel->changeAvatar($upload_data,$this->session->userdata('username'));
        }
        $this->index($data);
    }

    public function remove(){
        $data = array(
            "error" => "Image supprimée"
        );
        $this->uploadmodel->delAvatar($this->session->userdata('username'));
        $this->index($data);
    }
}
?>