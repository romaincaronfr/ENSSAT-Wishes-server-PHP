<?php
/**
 * Created by PhpStorm.
 * User: zahead
 * Date: 27/05/15
 * Time: 15:20
 */

class modules extends CI_Controller{

    function __construct() {
        parent::__construct();
        $this->load-> model('modulesmodels');
        $this->load-> model('users');
        $this->load-> model('contenu');
    }

    public function index(){
        if(!$this->session->userdata('is_logged_in')){
            redirect('login');
        }else{
            $data = array(
                "modules" => $this->modulesmodels->getAllModules(),
                "enseignants" => $this->users->getAllEnseignants()
            );
            $this->load->model('users');
            $this->load->view('header');
            $this->load->view('back/template/header');
            $this->load->view('back/modules/showmodules',$data);
            $this->load->view('footer');
        }
    }

    public function displayModule(){
        if(!$this->session->userdata('is_logged_in')){
            redirect('login');
        }else{
            $data=array(
                "module"=>$this->input->post('module'),
                "teacher"=>$this->input->post('teacher')
            );
            if($data['module']!="" && $data['teacher']!="")
                $this->contenu->getModuleTeacher($data);
            elseif($data['module']!="") {
                $result = $this->contenu->getModuleByModule($data);
                var_dump($result);
            }else{
                $result = $this->contenu->getModuleByTeacher($data);
                var_dump($result);
            }

        }
    }
}