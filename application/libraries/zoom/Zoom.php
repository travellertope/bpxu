<?php
defined('BASEPATH') OR exit('No direct script access allowed');

  class Zoom {
    private $endPoint = "https://api.zoom.us/v2/users/me/meetings";

    private function callApi($account_id, $client_id, $client_secret, $data){
      $headers = array(
        "Authorization: Bearer ".$this->generateAccessToken($account_id, $client_id, $client_secret),
        "Content-Type: application/json"
      );

      $url = $this->endPoint;
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_POST, 1);
      curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      $response = curl_exec($curl);

      if(!$response){
        die('Connection Failer.');
      }else{
        curl_close($curl);
        return $response;
      }

    }

public function get_meeting($account_id, $client_id, $client_secret, $fields=""){
   $response = $this->callApi($account_id, $client_id, $client_secret, $fields);
   
   return $response;
   
  }


//function to generate JWT
        private function generateAccessToken($account_id="", $client_id="", $client_secret="") {


          $ch = curl_init();
          $ab = base64_encode($client_id.":".$client_secret);

          curl_setopt($ch, CURLOPT_URL, 'https://zoom.us/oauth/token');
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_POST, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=account_credentials&account_id=".$account_id);

          $headers = array();
          $headers[] = 'Host: zoom.us';
          $headers[] = 'Authorization: Basic '.$ab;
          $headers[] = 'Content-Type: application/x-www-form-urlencoded';
          curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

          $result = curl_exec($ch);
          if (curl_errno($ch)) {
              echo 'Error:' . curl_error($ch);
          }

          $result = json_decode($result);
          $access_token = $result->access_token;
          curl_close($ch);
          return $access_token;

        }

public function send_invitation($account_id, $client_id, $client_secret,$meeting_id, $data){

$headers = array(
  "Authorization: Bearer ".$this->generateAccessToken($account_id, $client_id, $client_secret),
  "Content-Type: application/json"
);
$url2 = "https://api.zoom.us/v2/meetings/$meeting_id/invite_links";

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_POST, 1);
      curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
      curl_setopt($curl, CURLOPT_URL, $url2);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      $res = curl_exec($curl);

$result = json_decode($res);

return $result;
}


  }
?>