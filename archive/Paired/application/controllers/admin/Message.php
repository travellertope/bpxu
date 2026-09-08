<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Message extends Home_Controller {

    public function index()
    {
        $data = array();
        $data['mgs_with'] = $this->admin_model->mgs_with();
        $data['total_mgs_with']  = count($data['mgs_with']);
        if (count($data['mgs_with']) == 0) {
            $mgs_with_id = 0;
        } else {
            $mgs_with_id = $data['mgs_with']->id;
        }

        $data['page_title'] = 'Message';
        $data['mgs_with_id'] = md5($mgs_with_id);
        $data['messages'] = $this->admin_model->mgs_with_details(md5($mgs_with_id));
        $data['contacts'] = $this->admin_model->mgs_with();
        $data['contacts_old'] = $this->admin_model->get_all_cotacts($this->session->userdata('id'));
        $data['mgs_part'] = $this->load->view('admin/user/include/message_content', $data, TRUE);
        $data['main_content'] = $this->load->view('admin/user/message',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    public function details($id)
    {

        $this->admin_model->my_messages_make_seen($id);
        $data = array();
        $data['page_title'] = "My Message";
        $data['mgs_with_id'] = $id;
        $data['messages'] = $this->admin_model->mgs_with_details($id);
        $data['user'] = $this->admin_model->get_by_md5_id($id, 'users');
        $data_load = $this->load->view('admin/user/include/message_content', $data, TRUE);
        echo json_encode(array('st' => 1, 'data_load' => $data_load));


    }
    
    
    public function send_message() 
    {
        
        if ($_POST) {

            $mgs_to = get_user_id_md5(strip_tags($this->input->post('mgs_to', true)));

            $data = array(
                'mgs_to' => $mgs_to,
                'mgs_time' => date('Y-m-d H:i:s'),
                'message' => nl2br(strip_tags($this->input->post('message', true))),
                'mgs_from' => $this->session->userdata('id'),
            );

            $data = $this->security->xss_clean($data);
            $this->common_model->insert($data, 'messages');

            $sender = $this->common_model->get_by_id($this->session->userdata('id'), 'users');
            $reciever = $this->common_model->get_by_id($mgs_to, 'users');

            // send email with updated code dynamic value
            $subject = get_email_by_slug('sent-message-notification')->subject;
            $body = get_email_by_slug('sent-message-notification')->body;
            $variables_data = [
                'sender_name'  =>$sender->name,
                'sender_email' => $sender->email
            ]; 

            $msg = preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($variables_data) {
                $key = trim($matches[1]);
                return isset($variables_data[$key]) ? $variables_data[$key] : $matches[0]; 
            }, $body);
            $edata = array();
            $edata['subject'] = $subject;
            $edata['msg'] = $msg;

            $msg = $this->load->view('email_template/common', $edata, true);
            //echo "<pre>"; print_r($reciever->email); exit();
            $response = $this->email_model->send_email($reciever->email, $subject, $msg);




           
            $mgs_time = my_date_show_time(my_date_now());
            $message = nl2br(strip_tags($this->input->post('message', true)));
            $name = $this->session->userdata('name');
            $append = " 
            <div class='direct-chat-messages'>
                <div class='direct-chat-msg right'>
                   <div class='direct-chat-text text-right'>
                      $message
                   </div>
                   <div class='direct-chat-info clearfix'>
                      <span class='direct-chat-timestamp pull-left'>$mgs_time </span>
                   </div>
                </div>
            </div>
            ";
            echo json_encode(array('st' => 1, 'append' => $append));
        }
    }


     public function search_contact($query)
    {

        $data = array();
        $data['page_title'] = 'Message';
        $data['contacts'] = $this->admin_model->get_all_cotacts($this->session->userdata('id'),$query);

        $loaded=$this->load->view('admin/user/include/contact',$data,true);
        $sdata = array();
        $sdata['loaded'] = $loaded ;
        echo json_encode($sdata);
    }

}
