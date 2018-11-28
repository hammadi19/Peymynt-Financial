<?php

namespace App\Base\Manager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ContentManager
 * @package App\Base\Manager
 */
class ContentManager
{

    /**
     * @var ContainerInterface $container
     */
    private $container;
    /**
     * Set startups
     *
     * @param ContainerInterface $container
     */
    public function __construct( ContainerInterface $container )
    {
        $this->container = $container;
    }

    public function setUserPassword($hashString, $firstPassword, $lastPassword ){

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $this->container->getParameter('base_server_url').$this->container->getParameter('reset_password_endpoint'), [
            'form_params' => [
                'hq' => $hashString,
                'password_first' => $firstPassword,
                'password_second' => $lastPassword
            ]
        ]);

        $code = $response->getStatusCode();
        if(200 == $code){
            return json_decode($response->getBody(), true);
        }
        return FALSE;
    }

    public function forgetPassword($email){
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $this->container->getParameter('base_server_url').$this->container->getParameter('forget_password_endpoint'), [
            'form_params' => [
                'email' => $email
            ]
        ]);

        $code = $response->getStatusCode();

        if(200 == $code){
            return TRUE;
        }
        return FALSE;
    }

    public function sendEmailContactUs($name, $email, $contact, $message, $userAgent,$ipAddress){
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $this->container->getParameter('base_server_url').$this->container->getParameter('contact_us_endpoint'), [
            'form_params' => [
                'name' => $name,
                'email' => $email,
                'contact_no' => $contact,
                'message' => $message,
                'user_agent' => $userAgent,
                'ip_address' => $ipAddress
            ]
        ]);

        $code = $response->getStatusCode();

        if(200 == $code){
            return json_decode($response->getBody(), true);
        }
        return FALSE;
    }



    public function getSurgeryList($resource){

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $resource);

        $code = $response->getStatusCode();
        if(200 == $code){
            return json_decode($response->getBody(), true);
        }


    }


    public function signupFromSubmit($resource, $userDataArray){
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $resource, [
            'form_params' => [
                'organization_id' => $userDataArray["organization_id"],
                'first_name' => $userDataArray["first_name"],
                'last_name' => $userDataArray["last_name"],
                'contact_no' => $userDataArray["contact_no"],
                'email' => $userDataArray["email"],
                'date_of_birth' => $userDataArray["date_of_birth"],
                'gender' => $userDataArray["gender"],
                'postcode' => $userDataArray["postcode"],
                'nhs' => $userDataArray["nhs"]
            ]
        ]);

        $code = $response->getStatusCode();

        if(200 == $code){
            return json_decode($response->getBody(), true);
        }
        return FALSE;
    }


    //public function
    public function isUserApproved($user){
        $client = new \GuzzleHttp\Client();
        $baseUrl = $this->container->getParameter('rest_endpoints')['base_server_url'];
        $resource = $this->container->getParameter('rest_endpoints')['view_profile_endpoint'];
        $response = $client->request('GET' , $baseUrl.$resource  , ['headers'=> ['Accept' => 'application/json','Authorization' => "Bearer ".$user->getToken().""],]);
        $output = json_decode($response->getBody(),true);
        if( 200 == $response->getStatusCode()){
            return $output['data']['is_approved'];
        }
        return false;
    }








    //Twig replacement Functions
    public function userProfileInfo(){
        $session = $this->container->get('session');
        $userprofileInfo["first_name"] = $session->get('_first_name');
        $userprofileInfo["email"] = $session->get('_email');
        $userprofileInfo["profile_image"] = $session->get('_profile_image');
        return $userprofileInfo;
    }



}