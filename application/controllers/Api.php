<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'third_party/REST_Controller.php';
require APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
class Api extends REST_Controller {
    public function __construct() {
        parent::__construct();
        
        
        $this->load->model('Users_model');
        // Load these helper to create JWT tokens
        $this->load->helper(['jwt', 'authorization']); 
    }

public function hello_get()
    {
        $tokenData = 'Hello World!';
        
        // Create a token
        $token = AUTHORIZATION::generateToken($tokenData);
        // Set HTTP status code
        $status = parent::HTTP_OK;
        // Prepare the response
        $response = ['status' => $status, 'token' => $token];
        // REST_Controller provide this method to send responses
        $this->response($response, $status);
    }


public function login_post()
 {
       
         $data = file_get_contents('php://input');
       
        $user_login = json_decode($data);
        $errormessage = '';
        if (empty($user_login->email)) {
            $errormessage = 'Please enter your email.';
        }

        elseif(empty($user_login->password)) {
            $errormessage = 'Please enter your password.';
       }
        if (!empty($errormessage)) {
            $message = array('status' => 0, 'message' => $errormessage);
            $this->response($message, 400);
        }
      
    else
      {
        $login_id   = trim($user_login->email);
        $password   = trim(md5($user_login->password));

     
      $login = $this->Users_model->user_login($login_id, $password);
     

      if($login)
            {
            // Create a token from the user data and send it as reponse
            $token = AUTHORIZATION::generateToken(['email' => $login_id]);

            // Prepare the response
            $status = parent::HTTP_OK;

            $response = ['status' => $status,'message' => 'User successfully login','data' => $login, 'token' => $token];

            $this->response($response, $status);
        }
        else {
            $this->response(['msg' => 'Invalid username or password!'], parent::HTTP_NOT_FOUND);
        }
      }
 }


public function register_post()
{

 $data = file_get_contents('php://input');
        $user_reg = json_decode($data);

        $errormessage = '';
        if (empty($user_reg->email)) {
            $errormessage = 'Please enter your email.';
        }
        else if (empty($user_reg->name)) {
            $errormessage = 'Please enter your name.';
        }
        else if (empty($user_reg->password)) {
            $errormessage = 'Please enter your password.';
        }
       
        if (!empty($errormessage)) {
            $message = array('status' => 0, 'message' => $errormessage);
            $this->response($message, 400);
        }
        else {
            $signupdata = array(
                'email'  => trim($user_reg->email),
                'password' => trim($user_reg->password),
                'name' => trim($user_reg->name),
               
            );
            $appUser = $this->Users_model->user_registration($signupdata);
            
           

            if ($appUser) {
                
                 $user_email  =  trim($user_reg->email);

                $token = AUTHORIZATION::generateToken(['email' =>  $user_email]);

                $message = array('status' => 1, 'message' => 'User successfully registered', 'data' => $appUser,'token'=>$token);
                $this->response($message, 200);
            }
            else {
                $message = array('status' => 0, 'message' => 'Email already exits!');
                $this->response($message, 200);
            }
        }
    }



}







