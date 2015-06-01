<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 27/05/15
 * Time: 17:14
 */

class Contenu extends CI_Model{

    public function getAllModuleTypes(){
        $this->db->select("type");
        $this->db->distinct();
        $this->db->from("contenu");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getTypeContenu(){
        $this->db->select('partie');
        $this->db->from('contenu');
        $this->db->where('module',$this->input->get('gData'));
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getModuleContenus(){
        $this->db->select('*');
        $this->db->from("contenu");
        $this->db->where('module',$this->input->get('gData'));
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getModuleContenusByPartieModule(){
        $array = array(
            "partie" => $this->input->get('gData'),
            "module" => $this->input->get('bData')
        );
        $this->db->select('*');
        $this->db->from("contenu");
        $this->db->where($array);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function modifyModuleContenu($data,$keys){
        $where = 'module = "'.$keys['module'].'" AND partie = "'.$keys['partie'].'"';
        $query = $this->db->query($this->db->update_string('contenu',$data,$where));
        if(!$query){
            $ret= array(
                "ErrorMessage" => $this->db->_error_message(),
                "ErrorNumber" => $this->db->_error_message()
            );
        }else
            $ret = "good";
        return $ret;

    }

    public function deleteContenuModule($array){
        foreach($array['partie'] as $partie){
            $this->db->where('partie',$partie);
            $query = $this->db->delete('contenu');
        }
    }

    public function getModuleTeacher($data,$promotion=null,$semester=null){
        if($data['module']!=null && $data['teacher']!=null && $semester!="noSemester")
            $query = $this->db->query("SELECT * FROM module inner join contenu on module.ident=contenu.module where ident='".$data['module']."' AND semestre='".$semester."' AND enseignant='".$data['teacher']."'");
        else if ($data['module']!=null && $data['teacher']==null)
            $query = $this->db->query("SELECT * FROM module inner join contenu on module.ident=contenu.module AND ident='".$data['module']."' AND enseignant IS NULL");
        else if ($data['module']!=null)
            $query = $this->db->query("SELECT * FROM module inner join contenu on module.ident=contenu.module AND ident='".$data['module']."'");
        return $query->result_array();
    }

    public function getModuleByModule($data,$promotion,$semester){
        if($semester!="noSemester" && $data['module']!=null){
            $query = $this->db->query("SELECT * FROM contenu join module WHERE contenu.module= '".$data['module']."' and module.ident=contenu.module and semestre='".$semester."'");
        }else{
            $this->db->select("*");
            $this->db->from("contenu");
            $this->db->where("module",$data['module']);
            $query = $this->db->get();
        }
        return $query->result_array();
    }

    public function getModuleByElse($data,$promotion,$semester){
        if($promotion!="noProm") {
            $query = $this->db->query("SELECT * FROM module inner join contenu on module.ident=contenu.module where public='".$promotion."'");
        }else if($data['teacher']==null){
            $query = $this->db->query("SELECT * FROM module inner join contenu on module.ident=contenu.module AND contenu.enseignant IS NULL");

            /*$this->db->select("*");
            $this->db->from("contenu,module");
            $this->db->where("enseignant",$data['teacher']);
            if($promotion!="noProm")
                $this->db->where("public",$promotion);
            if($semester!="noSemester")
                $this->db->where("semestre",$semester);
            $query = $this->db->get();*/
        }else if($data['teacher']!=null){
            $query = $this->db->query("SELECT * FROM module inner join contenu on module.ident=contenu.module AND contenu.enseignant='".$data['teacher']."'");
        }else {
            $query = $this->db->query("SELECT * FROM module inner join contenu on module.ident=contenu.module");
        }
        return $query->result_array();
    }

    public function getContenuByModule($array){
        $this->db->select('*');
        $this->db->from('module');
        $this->db->join('contenu','module.ident=contenu.module');
        if($array['module'])
            $this->db->where('module',$array['module']);
        if($array['semester']!='noSemester')
            $this->db->where('semestre',$array['semester']);
        if($array['teacher']!='no')
            $this->db->where('enseignant',$array['teacher']);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getContenuByPromo($array){
        $this->db->select('*');
        $this->db->from('module');
        $this->db->join('contenu','module.ident=contenu.module');
        if($array['promotion']!='noProm')
            $this->db->where('public',$array['promotion']);
        if($array['semester']!='noSemester')
            $this->db->where('semestre',$array['semester']);
        if($array['teacher']!='no')
            $this->db->where('enseignant',$array['teacher']);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * @return int nombre d'heure qu'un professeur a
     */
    public function getHeuresPrises($teacher){
        $this->db->SELECT ("hed");
        $this->db->from ("contenu");
        $this->db->where("enseignant",$teacher);
        $query =  $this->db->get();
        $heures = 0;
        if ($query->num_rows>0) {
            $result = $query->result_array();
            foreach ($result as $heure) {
                $heures = $heures + $heure['hed'];
            }
        }
        return $heures;
    }

    public function getHeurePourUnContenu($module,$partie){
        $this->db->select('hed');
        $this->db->from('contenu');
        $this->db->where('module',$module);
        $this->db->where('partie',$partie);
        $query = $this->db->get();
        return $query->row()->hed;
    }

    public function addEnseignanttoContenu($module,$partie){
        $data = array(
            'enseignant' => $this->session->userdata('username')
        );
        //$this->db->set('enseignant',$this->session->userdata('username'));
        $this->db->where('module',$module);
        $this->db->where('partie',$partie);
        $query = $this->db->update('contenu',$data);
        return $query;
    }

    public function removeALotEnseignanttoContenu($tableau_enseignants){
        foreach($tableau_enseignants as $enseignants) {
            $data = array(
                'enseignant' => null
            );
            $this->db->where('enseignant', $enseignants);
            $this->db->update('contenu',$data);
        }
    }

    /**
     * Permet de savoir un contenu existe
     * @param $module
     * @param $partie
     * @return bool vrai si le module existe, faux sinon
     */
    public function ifContenuExist($module,$partie){
        $this->db->select('*');
        $this->db->from('contenu');
        $this->db->where('module',$module);
        $this->db->where('partie',$partie);
        $query = $this->db->get();
        if ($query->num_rows<1){
            return false;
        }
        else{
            return true;
        }
    }

    /**
     * Permet de savoir si un contenu a déjà un prof d'attribué
     * @param $module
     * @param $partie
     * @return bool
     */
    public function ifThereIsTeacher($module,$partie){
        $this->db->select('enseignant');
        $this->db->from('contenu');
        $this->db->where('module',$module);
        $this->db->where('partie',$partie);
        $query = $this->db->get();
        if ($query->row()->enseignant==null){
            return false;
        }
        else{
            return true;
        }
    }

    public function desinscriptionModule($module,$partie){
        $data = array(
            'enseignant' => null
        );
        //$this->db->set('enseignant',$this->session->userdata('username'));
        $this->db->where('module',$module);
        $this->db->where('partie',$partie);
        $this->db->where('enseignant',$this->session->userdata('username'));
        $query = $this->db->update('contenu',$data);
        return $query;
    }
}