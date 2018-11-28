<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;

class UserDefaultController extends AbstractController
{

    /**
     * @Method({"POST"})
     * @Route("/secure/login_check", name="app_secure_login_check", options={"expose"=true})
     */
    public function check()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }

    /**
     * @Method({"GET"})
     * @Route("/secure/logout", name="app_user_logout")
     */
    public function logout()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }

    /**
     * @Route("/login", name="app_user_login")
     * @Template()
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ){
               return $this->redirectToRoute('peymynt.welcome');
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return array(
            'last_username' => $lastUsername,
            'error'         => $error,
        );
    }


    /**
     * @Route("/ajax-login", name="app_ajax_user_login")
     * @Template()
     */
    public function ajaxLogin(AuthenticationUtils $authenticationUtils)
    {


        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return array(
            'last_username' => $lastUsername,
            'error'         => $error,
        );
    }


    /**
     * @Route("/signup", name="app_user_signup")
     */
    public function Signup(Request $request)
    {

        $errors = array();
        $success=null;
        $userDataArray = array();
        if ($request->request->get('formSignup')) {

            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $rptPassword = $request->request->get('rptPassword');

            $userDataArray['email'] = $email;
            $userDataArray['password'] = $password;


            if (empty($email)) {
                array_push($errors, 'Email field cannot be empty');
            }else if (empty($password)) {
                array_push($errors, 'Password field cannot be empty');
            }else if (empty($rptPassword)) {
                array_push($errors, 'Repeat Password field cannot be empty');
            }else if(!preg_match("(^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$)",$password)){
                array_push($errors, 'Password must contain atleast one capital letter and a number and should be of length of 8 characters' );
            }elseif($password != $rptPassword){
                array_push($errors, 'Passwords do not match' );
            }elseif(!empty($password) AND strlen($password) < 8){
                array_push($errors, 'Password must contain atleast one capital letter and a number and should be of length of 8 characters' );
            }




            if (0 === count($errors)) {

                $data = [
                    'form_params' => $userDataArray
                ];

                $client = new Client(['http_errors' => false]);
                $baseserverResource = $this->getParameter('rest_endpoints')['base_server_url'];
                $signupEP = $this->getParameter('rest_endpoints')['signup_endpoint'];
                $resource = $baseserverResource.$signupEP;
                $response = $client->request('POST', $resource , $data);
                $responseArray =  json_decode($response->getBody(), true);

                //----------For AJAX Response------------
                $apiRes = $response->getBody();
                return new Response($apiRes);
                //---------------------------------------
            }
            $res = array("message" => $errors[0], "code" => 404);
            return new Response(json_encode($res));
        }

    }



    /**
     * @Route("/set-password", name="app_user_set_password")
     * @Template()
     */
    public function setPassword(Request $request)
    {

        $success=null;
        $errors = array();

        if ($request->request->get('formSetPassword')) {

            $formValues = $request->request->get('formSetPassword');
            $firstPassword = $formValues['first_password'];
            $secondPassword = $formValues['second_password'];

            if(empty($firstPassword) OR empty($secondPassword)){
                array_push($errors, 'Password fields cannot be empty' );
            }

            elseif(!preg_match("(^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$)",$firstPassword)){
                array_push($errors, 'Password must contain atleast one capital letter and a number and should be of length of 8 characters' );
            }

            elseif($firstPassword != $secondPassword){
                array_push($errors, 'Passwords do not match' );
            }
            elseif(!empty($firstPassword) AND strlen($firstPassword) < 8){
                array_push($errors, 'Password must contain atleast one capital letter and a number and should be of length of 8 characters' );
            }

            if(0 === count($errors)){


                $hashString =  $request->query->get('hq');
                $data = [
                    'form_params' => [
                        'hq' => $hashString,
                        'password_first' => $firstPassword,
                        'password_second' => $secondPassword
                    ]
                ];

                $client = new Client(['http_errors' => false]);
                $baseserverResource = $this->getParameter('rest_endpoints')['base_server_url'];
                $resetPassEP = $this->getParameter('rest_endpoints')['reset_password_endpoint'];
                $resource = $baseserverResource.$resetPassEP;
                $response = $client->request('POST', $resource , $data);

                $responseArray =  json_decode($response->getBody(), true);
                    if(200 == $responseArray['code']){
                        $success=true;
                    }else{
                        array_push($errors,$responseArray['message']);
                        $success=false;
                    }

            }

        }

        return array(
            'success'=>$success,
            'errors' => $errors,
            'hq' => $request->query->get('hq')
        );
    }

    /**
     * @Route("/forgot-password", name="app_user_forgot_password")
     * @Template()
     */
    public function forgotPassword(Request $request)
    {

        $success=null;
        $errors = array();

        if ($request->request->get('formForgotPassword')) {

            $formValues = $request->request->get('formForgotPassword');
            $email = $formValues['email'];

            if(empty($email)){
                array_push($errors, 'Please provide email address' );
            }

            if(0 === count($errors)){

                $data = [
                    'form_params' => [
                        'email' => $email
                    ]
                ];

                $client = new Client(['http_errors' => false]);
                $baseserverResource = $this->getParameter('rest_endpoints')['base_server_url'];
                $forgotPassEP = $this->getParameter('rest_endpoints')['forgot_password_endpoint'];
                $resource = $baseserverResource.$forgotPassEP;
                $response = $client->request('POST', $resource , $data);
                $responseArray =  json_decode($response->getBody(), true);
                if(200 == $responseArray['code']){
                    $success=true;
                }else{
                    array_push($errors,$responseArray["data"]["errors"][0]);
                    $success=false;
                }

            }

        }

        return array(
            'success'=>$success,
            'errors' => $errors,
        );
    }

    /**
     * @Route(path="/register", name="register")
     * @Template();
     */
    public function registerAction(){

        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ){
            return $this->redirectToRoute('peymynt.welcome');
        }
        return [];
    }

}
