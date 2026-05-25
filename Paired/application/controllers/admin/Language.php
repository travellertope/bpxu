<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Language extends Home_Controller {

    public function __construct()
    {
        parent::__construct();
        //check auth
        if (!is_admin() && !is_user()) {
            redirect(base_url());
        }
        $this->load->dbforge();
    }


    public function index()
    {
        $data = array();
        $data['page_title'] = 'Language';   
        $data['language'] = FALSE;
        $data['languages'] = $this->admin_model->get_language();
        $data['main_content'] = $this->load->view('admin/language/language',$data,TRUE);
        $this->load->view('admin/index',$data);
    }


    // add language
    public function add()
    {   
        if($_POST)
        {   
            check_status();

            if (!is_writable('application/language')):
                $this->session->set_flashdata('error', 'script > application > "language" folder is not writable, please writable this folder.');
                redirect(base_url('admin/language')); exit();
            endif;

            $lang_name = $this->input->post('name', true);
            if (strlen($lang_name) != strlen(utf8_decode($lang_name)))
            {
                $this->session->set_flashdata('error', 'Language name must be english characters');
                redirect(base_url('admin/language')); exit();
            }


            $id = $this->input->post('id', true);
            if ($id == '') {
                $is_unique = '|is_unique[language.name]';
            }else{
                $is_unique = '';
            }

            $this->form_validation->set_rules('name', trans('name'), 'required'.$is_unique);

            if ($this->form_validation->run() === false) {
                $this->session->set_flashdata('error', validation_errors());
                redirect(base_url('admin/language'));
            } else {
               
                $data=array(
                    'name' => $this->input->post('name', true),
                    'slug' => str_slug($this->input->post('name', true)),
                    'short_name' => $this->input->post('short_name', true),
                    'text_direction' => $this->input->post('text_direction', true),
                    'status' => 1
                );
                $data = $this->security->xss_clean($data);

                if ($id != '') {
                    $this->admin_model->edit_option($data, $id, 'language');
                    $this->session->set_flashdata('msg', trans('updated-successfully')); 

                    $lang = str_slug($_POST['lang_name']);
                    
                  
                    if (is_dir(APPPATH.'language/'.$lang)) {
                        rename(APPPATH.'language/'.$lang, APPPATH.'language/'.str_slug($this->input->post('name', true)));
                    }

                    $fields = array(
                        $lang => array(
                            'name' => str_slug($this->input->post('name', true)),
                            'type' => 'TEXT',
                            'constraint' => '255'
                        ),
                    );
                    $this->dbforge->modify_column('lang_values', $fields);

                } else {
                    $this->admin_model->insert($data, 'language');
                    $this->session->set_flashdata('msg', trans('inserted-successfully')); 
                
                   
                    $fields = array(
                        str_slug($_POST['name']) => array('type' => 'TEXT', 'after' => 'english')
                    );
                    $this->dbforge->add_column('lang_values', $fields);

                    
                    $dir = str_slug($_POST['name']);
                    if (!is_dir(APPPATH.'language/'.$dir)) {
                        mkdir(APPPATH.'./language/' . $dir, 0777, TRUE);
                        copy(APPPATH.'language/english/website_lang.php', APPPATH.'language/'.$dir.'/website_lang.php');
                    }
                }

                redirect(base_url('admin/language'));

            }
            
        }     
    }


    
    public function add_value()
    {   
        check_status();

        if($_POST){

            $check = $this->admin_model->check_keyword(str_slug($this->input->post('keyword', true)));
            if ($check == 1) {
                $this->session->set_flashdata('error', 'keyword-exists');
                redirect($_SERVER['HTTP_REFERER']);
            } else {

                $data=array(
                    'label' => $this->input->post('label', true),
                    'keyword' => character_limiter(str_slug($this->input->post('keyword', true)), 2),
                    'english' => $this->input->post('label', true),
                    'type' => $this->input->post('type', true)
                );
                $data = $this->security->xss_clean($data);
                $this->admin_model->insert($data, 'lang_values');
                $this->session->set_flashdata('msg', trans('inserted-successfully')); 
                redirect(base_url('admin/language/values/'.$this->input->post('type').'/'.$this->input->post('lang')));
            }
        }
    }


    public function test()
    {

        //exit();
        // $values = $this->admin_model->select_asc('lang_values');
        // foreach ($values as $value) {
        //     echo  "'".html_escape($value->id)."' => '".html_escape($value->english)."',<br>";
        // }
        // exit();

        $variable = array(
           '1681' => 'Vos informations KYC sont en cours de vérification. Veuillez attendre les prochaines mises à jour',
'1682' => 'Vos informations KYC ont été vérifiées et approuvées avec succès. Vous pouvez maintenant procéder aux actions ou services prévus.',
'1683' => 'Nous avons le regret de vous informer que vos informations KYC ont été rejetées. Veuillez consulter les directives fournies et soumettre à nouveau vos informations en conséquence.',
'1684' => 'KYC Reject Reason',
'1685' => 'Resubmitted at',
'1686' => 'Enable to allow your Mentors to verify KYC documents',
'1687' => 'Nous vous rappelons qu il est obligatoire de soumettre vos documents KYC (Know Your Customer) pour la vérification de votre compte. Le fait de ne pas soumettre ces documents peut entraîner une restriction de l accès à votre compte ou à vos services.',
'1688' => 'Custom CSS',
'1689' => 'Ajoutez votre propre code CSS ici',
'1690' => 'Apprenez cette nouvelle compétence, lancez ce projet, décrochez la carrière de vos rêves',
'1691' => 'Parcourir les mentors par catégories',
'1692' => 'Paramètres PWA',
'1693' => 'Activer les PWA (Progressive Web Apps)',
'1694' => 'Activer pour permettre à vos utilisateurs d installer les PWA sur leur téléphone',
'1695' => 'Vous avez atteint la limite des sessions gratuites ! Pour continuer, veuillez ajouter un prix pour votre session',
'1696' => 'Fixer le prix 0 pour la session gratuite',
'1697' => 'Combien de personnes seront autorisées à réserver par tranche horaire',
'1698' => 'Réservation individuelle',
'1699' => 'Social Login',
'1700' => 'Redirect Url',
'1701' => 'Google Login',
'1702' => 'Integration Docs',
'1703' => 'Continuer avec Google',
'1704' => 'Quand devrions-nous nous rencontrer ?',
'1705' => 'Vous êtes déjà connecté. Veuillez confirmer votre réservation pour continuer',
'1706' => 'Utilisation de l API',
'1707' => 'Utilisez l API Admin Zoom pour gérer les réunions zoom de tous les utilisateurs',
'1708' => 'Allow users to manage their Zoom meetings via their individual zoom API',





        );
        
        //echo "<pre>"; print_r($variable); exit();
        foreach ($variable as $key => $value) {
            
            $vdata=array(
                'french' => $value
            );
            $this->admin_model->update($vdata, $key, 'lang_values');
        }
        echo "done";
        exit();

    }


    //show language values
    public function values($type, $slug)
    {   
        $data = array();  
        $data['page_title'] = 'language';  
        $data['value'] = $slug;  
        $data['type'] = $type;  
        $data['language'] = $this->admin_model->get_lang_values_by_type($type);
        $data['main_content'] = $this->load->view('admin/language/language_values',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    //update language values
    public function update_values($type)
    {   
        check_status();

        $data = array();
        $language = $this->input->post('lang_type', true);
        $languages = $this->admin_model->get_lang_values_by_type($type);
        

        ini_set('memory_limit', '-1');
        set_time_limit ( -1);

        foreach ($languages as $lang) {
            $value = 'value'.$lang->id;

            $data=array(
                $_POST['lang_type'] => $_POST[$value]
            );
            $this->admin_model->edit_option($data, $lang->id, 'lang_values');
        }
        $this->session->set_flashdata('msg', trans('updated-successfully'));
        redirect(base_url('admin/language/values/'.$type.'/'.$language));

    }


    //edit language values
    public function edit($id)
    {  
        $data = array();
        $data['page_title'] = 'Edit';   
        $data['language'] = $this->admin_model->select_option($id, 'language');
        $data['main_content'] = $this->load->view('admin/language/language',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    //active language    
    public function active($id) 
    {
        $data = array(
            'status' => 1
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'language');
        $this->session->set_flashdata('msg', trans('msg-activated')); 
        redirect(base_url('admin/language'));
    }


    //deactive language
    public function deactive($id) 
    {
        check_status();
        
        $language = $this->admin_model->get_by_id($id,'language');
        $data = array(
            'status' => 0
        );
        $data = $this->security->xss_clean($data);
        if ($language->name != 'english') {
            $this->admin_model->update($data, $id,'language');
            $this->session->set_flashdata('msg', trans('msg-deactivated')); 
        }
        redirect(base_url('admin/language'));
    }


    //delete language
    public function delete($id)
    {
        check_status();

        $language = $this->admin_model->get_by_id($id,'language');
     
        $lang = $language->slug;
        if ($lang != 'english') {

            //delete language folder & file
            if (is_dir(APPPATH.'language/'.$lang)) {
                unlink(APPPATH.'language/'.$lang.'/website_lang.php');
                rmdir(APPPATH.'language/'.$lang);
            }

            //delete database column 
            $this->dbforge->drop_column('lang_values', $lang);
            $this->admin_model->delete($id,'language'); 
        }
        echo json_encode(array('st' => 1));
    }

}
    

