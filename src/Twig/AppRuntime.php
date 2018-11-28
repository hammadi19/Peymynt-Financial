<?php

namespace App\Twig;

use Twig\Extension\RuntimeExtensionInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Base\Bridge\RESTBridge;

class AppRuntime implements RuntimeExtensionInterface
{


    private $request;

    private $tokenStorage;

    private $params;

    private $restBridge;

    private $user;

    public function __construct(RequestStack $requestStack,TokenStorageInterface $tokenStorage, ParameterBagInterface $params){
        $this->request = $requestStack->getCurrentRequest();
        $this->tokenStorage = $tokenStorage->getToken();
        $this->params       = $params;
        $this->baseUrl      = $this->params->get('rest_endpoints')['base_server_url'];
        $this->client       = new \GuzzleHttp\Client();
        $this->user         = $this->tokenStorage->getUser();

        //echo($this->tokenStorage->getUser()->getToken());
        //RESTBridge $restBridge
        //$this->restBridge = $restBridge;
    }

    public function userPersonalInfoFunc(){
        if("object" === gettype($this->tokenStorage) && $this->tokenStorage instanceof UsernamePasswordToken){
            $baseServer     = $this->params->get('rest_endpoints')['base_server_url'];
            $localBasePath  = $this->request->getScheme().'://'.$this->request->getHost().$this->request->getBasePath();
            $firstName      = $this->tokenStorage->getAttribute('_first_name');
            $email      = $this->tokenStorage->getAttribute('_email');
            $profileImage   = $this->tokenStorage->getAttribute('_profile_image');
            $info = [
                'email' => $email,
                'first_name' => $firstName,
                'business_id' => $this->user->getBusinessId(),
            ];
            return $info;
        }
    }

    public function userBusinessInfoFunc(){

        $resourceBusiness = $this->params->get('rest_endpoints')['settings_list_user_business_endpoint'];
        $businessResponse = $this->getRequest($resourceBusiness);
        return $businessResponse["data"];
    }

    public function userBusinessNameFunc(){
        return $this->user->getBusinessId();
    }

    /**
     * Get REST resource
     *
     * @param $resource
     * @return mixed
     */
    public function getRequest($resource){
        //$response = $this->client->request('GET', $this->baseUrl.$resource , ['headers'=> ['Accept' => 'application/json','Authorization' => "Bearer ".$this->user->getToken().""],]);
        $response = $this->client->request('GET', $this->baseUrl.$resource , ['headers'=> $this->getHeaderParameters(),]);
        if(200 === $response->getStatusCode()){
            $responseArray = json_decode($response->getBody(),true);
            return $responseArray;
        }
    }

    /**
     * Get common header parameters
     *
     * @return array
     */
    private function getHeaderParameters(){
        $headerParams = array(
            'Accept' => 'application/json',
        );
        if($this->user != null){
            $headerParams['Authorization'] = "Bearer ".$this->user->getToken()."";
            //$headerParams['Authorization'] = "Bearer ".$this->getSessionStoredToken()."";
        }
        return $headerParams;
    }

}