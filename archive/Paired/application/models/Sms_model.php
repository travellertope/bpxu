<?php

use Twilio\Rest\Client;

class Sms_model extends CI_Model {

    public function send_user($to_number, $message, $user_id)
    {

        require_once('application/libraries/twilio/src/Twilio/autoload.php');
        // Use the REST API Client to make requests to the Twilio REST API
        $user = $this->common_model->get_by_id($user_id, 'users');
  
        // Your Account SID and Auth Token from twilio.com/console
        $sid = $user->twillo_account_sid;
        $token = $user->twillo_auth_token;
        $client = new Client($sid, $token);

        try{
            // Use the client to do fun stuff like send text messages!
            $message = $client->messages->create(
                // the number you'd like to send the message to
                $to_number,
                [
                    // A Twilio phone number you purchased at twilio.com/console
                    'from' => $user->twillo_number,
                    // the body of the text message you'd like to send
                    'body' => $message
                ]
            );
            return 1;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function send_whatsapp_user($to_number, $message, $instance_id, $token)
    {

        $params=array(
            'token' => $token,
            'to' => $to_number,
            'body' => $message
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.ultramsg.com/".$instance_id."/messages/chat",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_SSL_VERIFYHOST => 0,
          CURLOPT_SSL_VERIFYPEER => 0,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => http_build_query($params),
          CURLOPT_HTTPHEADER => array(
            "content-type: application/x-www-form-urlencoded"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
           //echo "Error #:" . $err;
            return false;
        } else {
            return true;
        }
    }



    public function send($to_number, $message)
    {

        require_once('application/libraries/twilio/src/Twilio/autoload.php');
        // Use the REST API Client to make requests to the Twilio REST API
        
        // Your Account SID and Auth Token from twilio.com/console
        $sid = user()->twillo_account_sid;
        $token = user()->twillo_auth_token;
        $client = new Client($sid, $token);

        try{
            // Use the client to do fun stuff like send text messages!
            $message = $client->messages->create(
                // the number you'd like to send the message to
                $to_number,
                [
                    // A Twilio phone number you purchased at twilio.com/console
                    'from' => user()->twillo_number,
                    // the body of the text message you'd like to send
                    'body' => $message
                ]
            );
            return 1;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function send_admin($to_number, $message)
    {

        require_once('application/libraries/twilio/src/Twilio/autoload.php');
        // Use the REST API Client to make requests to the Twilio REST API
        
        // Your Account SID and Auth Token from twilio.com/console
        $sid = settings()->twillo_account_sid;
        $token = settings()->twillo_auth_token;
        $client = new Client($sid, $token);

        try{
            // Use the client to do fun stuff like send text messages!
            $message = $client->messages->create(
                // the number you'd like to send the message to
                $to_number,
                [
                    // A Twilio phone number you purchased at twilio.com/console
                    'from' => settings()->twillo_number,
                    // the body of the text message you'd like to send
                    'body' => $message
                ]
            );
            return 1;
        }
        catch(Exception $e){
            return $e->getMessage();
        }

    }

}