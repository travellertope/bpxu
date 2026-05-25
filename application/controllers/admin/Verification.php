<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Verification extends Home_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!is_admin() && !is_user()) {
            redirect(base_url());
        }
    }

    public function index()
    {
        $data = array();
        $data['page_title'] = 'Verification';  
        $data['countries'] = $this->admin_model->select_asc('country');
        $data['kyc'] = $this->admin_model->get_user_kyc(user()->id);
        $data['main_content'] = $this->load->view('admin/kyc/mentor_info',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function submit()
    {   

        check_status();
        
        if($_POST)
        {   

        //echo('string'); exit();
        $id = $this->input->post('id', true);
        $kyc = $this->admin_model->get_by_id($id,'kyc_verifications');
        $doc_type = $this->input->post('document_type', true);

        $data=array(
            'user_id' => user()->id,
            'country_id' => $this->input->post('country_id', true),
            'document_type' => $this->input->post('document_type', true),
            'doc_id_number' => $this->input->post('doc_id_number', true),
            'first_name' => $this->input->post('first_name', true),
            'last_name' => $this->input->post('last_name', true),
            'address' => $this->input->post('address', true),
            'birth_date' => $this->input->post('birth_date', true),
            
        );
        $data = $this->security->xss_clean($data);

        if ($id != '') {
            $this->admin_model->edit_option($data, $id, 'kyc_verifications');

            $pdata=array(
                'status' => 0,
                'is_preview' => $kyc->is_preview + 1,
                'resub_date' => my_date_now(),
            );
            $pdata = $this->security->xss_clean($pdata);
            $this->admin_model->edit_option($pdata, $id, 'kyc_verifications');
            $this->session->set_flashdata('msg', trans('updated-successfully')); 
        } else {
            $id = $this->admin_model->insert($data, 'kyc_verifications');
            $cdata=array(
                'created_at' => my_date_now(),
            );
            $cdata = $this->security->xss_clean($cdata);
            $this->admin_model->edit_option($data, $id, 'kyc_verifications');
            $this->session->set_flashdata('msg', trans('inserted-successfully')); 
        }



        // File upload start

        if($doc_type == 'nid' || $doc_type == 'dlicense'){

            $new_name = "file_".strtolower(time().'1.'.pathinfo($_FILES['front_side_doc']['name'], PATHINFO_EXTENSION));

            $config['upload_path']          = './uploads/files'; //file save path
            $config['allowed_types']        = 'pdf|jpg|png|jpeg';
            $config['max_size']             = 10000;
            $config['encrypt_name']         = TRUE;
            // $config['file_name'] = $new_name;

            $this->load->library('upload', $config);
            if ( ! $this->upload->do_upload('front_side_doc'))
            {
                $error = array('error' => $this->upload->display_errors());
                $this->session->set_flashdata('error', $error);
            }else{
                $filedata1 = $this->upload->data();
                $file_name1 = $filedata1['file_name'];

                $filedata1=array(
                    'front_side_doc' => 'uploads/files/'.$file_name1
                );
                $filedata1 = $this->security->xss_clean($filedata1);
                $this->admin_model->edit_option($filedata1, $id,'kyc_verifications');
                $this->session->set_flashdata('msg', 'File Uploaded Successfully'); 

            }

            $new_name2 = "file_".strtolower(time().'2.'.pathinfo($_FILES['back_side_doc']['name'], PATHINFO_EXTENSION));
            $config2['upload_path']          = './uploads/files'; //file save path
            $config2['allowed_types']        = 'pdf|jpg|png|jpeg';
            $config2['max_size']             = 10000;
            $config['encrypt_name']          = TRUE;

            $this->load->library('upload', $config2);
            if ( ! $this->upload->do_upload('back_side_doc'))
            {   
                $error = array('error' => $this->upload->display_errors());
                $this->session->set_flashdata('error', $error);
            }else{
                $filedata2 = $this->upload->data();
                $file_name2 = $filedata2['file_name'];
         
                $filedata2=array(
                    'back_side_doc' => 'uploads/files/'.$file_name2
                );
                
                $filedata2 = $this->security->xss_clean($filedata2);
                $this->admin_model->edit_option($filedata2, $id,'kyc_verifications');
                $this->session->set_flashdata('msg', 'File Uploaded Successfully'); 

            }
        }


        if($doc_type == 'passport'){

            $new_name3 = "file_".strtolower(time().'3.'.pathinfo($_FILES['passport']['name'], PATHINFO_EXTENSION));

            $config3['upload_path']          = './uploads/files'; //file save path
            $config3['allowed_types']        = 'pdf|jpg|png|jpeg';
            $config3['max_size']             = 10000;
            $config3['file_name'] = $new_name3;

            $this->load->library('upload', $config3);
            if ( ! $this->upload->do_upload('passport'))
            {
                $error = array('error' => $this->upload->display_errors());
                $this->session->set_flashdata('error', $error);
            }else
            {
                $filedata3 = $this->upload->data();
                $file_name3 = $filedata3['file_name'];
         
                $filedata3=array(
                    'passport' => 'uploads/files/'.$file_name3
                );
                $filedata3 = $this->security->xss_clean($filedata3);
                $this->admin_model->edit_option($filedata3, $id,'kyc_verifications');
                $this->session->set_flashdata('msg', 'File Uploaded Successfully'); 

            }

        }
        

        $new_name4 = "file_".strtolower(time().'4.'.pathinfo($_FILES['selfiee_with_doc']['name'], PATHINFO_EXTENSION));

        $config4['upload_path']          = './uploads/files'; //file save path
        $config4['allowed_types']        = 'pdf|jpg|png|jpeg';
        $config4['max_size']             = 10000;
        $config4['file_name'] = $new_name4;

        $this->load->library('upload', $config4);
        if ( ! $this->upload->do_upload('selfiee_with_doc'))
        {
            $error = array('error' => $this->upload->display_errors());
            $this->session->set_flashdata('error', $error);
        }else{
            $filedata4 = $this->upload->data();
            $file_name4 = $filedata4['file_name'];
     
            $filedata4=array(
                'selfiee_with_doc' => 'uploads/files/'.$file_name4
            );
            $filedata4 = $this->security->xss_clean($filedata4);
            $this->admin_model->edit_option($filedata4, $id,'kyc_verifications');
            $this->session->set_flashdata('msg', 'File Uploaded Successfully'); 

        }

        // File upload End
        redirect(base_url('admin/verification'));

        }      
        
    }

    public function kyc()
    {
        $data = array();
        $data['page_title'] ='KYC';  
        $data['countries'] = $this->admin_model->select_asc('country');
        $data['kycs'] = $this->admin_model->get_all_kycs();
        $data['main_content'] = $this->load->view('admin/kyc/kyc',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function status_action($type, $id) 
    {
        $data = array(
            'status' => $type
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'kyc_verifications');

        $kdata = array(
            'kyc_verified' => 1
        );
        $kyc = get_by_id($id, 'kyc_verifications');
        $this->admin_model->edit_option($kdata, $kyc->user_id, 'users');
        $this->session->set_flashdata('msg', trans('updated-successfully'));
        redirect(base_url('admin/verification/kyc?search=approve'));
    }

    public function reject_reason()
    {
        check_status();

        $kyc_id = $this->input->post('kyc');
        $data = array(
            'status' => 2,
            'reject_reason' => $this->input->post('reject_reason'),
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $kyc_id,'kyc_verifications');
        $this->session->set_flashdata('msg', trans('updated-successfully'));
        redirect(base_url('admin/verification/kyc?search=reject'));
    }


    public function delete($id)
    {
        $this->admin_model->delete($id,'kyc_verifications'); 
        echo json_encode(array('st' => 1));
    }

}
    

