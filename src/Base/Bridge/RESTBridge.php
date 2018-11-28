<?php

namespace App\Base\Bridge;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * REST Bridge b/w Taskbee & Guzzle
 *
 * Class RESTBridge
 * @package App\Base\Bridge
 */
class RESTBridge
{

    /**
     * @var $user
     */
    private $user;

    /**
     * @var $client
     */
    private $client;

    /**
     * @var $container
     */
    protected $container;

    /**
     * @var $baseUrl
     */
    protected $baseUrl;

    /**
     * RESTBridge constructor.
     * @param Container|null $container
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(Container $container = null , TokenStorageInterface $tokenStorage)
    {
        $this->container    = $container;
        $tokenStorage       = $tokenStorage->getToken();
        $this->user         = null;
        if("object" == gettype($tokenStorage)){
            $this->user         = $tokenStorage->getUser();
        }
        $this->baseUrl      = $this->container->getParameter('rest_endpoints')['base_server_url'];
        $this->client       = new \GuzzleHttp\Client();
    }

    /**
     * Get REST resource
     *
     * @param $resource
     * @param array $params
     * @return mixed
     * @throws
     */
    public function get($resource, $params = []){
        $response = $this->client->request(
            'GET',
            $this->baseUrl . $resource
            ,
            [
                'headers' => $this->getHeaderParameters(),
                'query' => $params,
            ]
            );
        if(200 === $response->getStatusCode()){
            $responseArray = json_decode($response->getBody(),true);
            return $responseArray;
        }
    }

    /**
     * DELETE REST resource
     *
     * @param $resource
     * @return mixed
     * @throws
     */
    public function delete($resource){
        $params['business_id'] = $this->user->getBusinessId();
        $response = $this->client->request(
            'DELETE',
            $this->baseUrl . $resource
            ,
            [
                'headers' => $this->getHeaderParameters(),
            ]
        );
        if(200 === $response->getStatusCode()){
            $responseArray = json_decode($response->getBody(),true);
            return $responseArray;
        }
    }


    /**
     * Post data on REST resource
     *
     * @param $resource
     * @param $bodyParameters
     * @return mixed
     */
    public function post($resource, $bodyParameters){
        //$response = $this->client->request('POST', $this->baseUrl.$resource  , ['headers'=> ['Accept' => 'application/json','Authorization' => "Bearer ".$this->user->getToken().""],'form_params'=>$bodyParameters,]);
        $response = $this->client->request('POST', $this->baseUrl.$resource  , ['headers'=> ['Accept' => 'application/json','Authorization' => "Bearer ".$this->user->getToken()."" ],'form_params' => $bodyParameters]);
        if(200 === $response->getStatusCode()){
            $responseArray = json_decode($response->getBody(),true);
            return $responseArray;
        }
    }


    /**
     * Update data on REST resource
     *
     * @param $resource
     * @param $bodyParameters
     * @return mixed
     */
    public function put($resource, $bodyParameters){
        $response = $this->client->request('PUT', $this->baseUrl.$resource , ['headers'=> ['Accept' => 'application/json','Authorization' => "Bearer ".$this->user->getToken().""],'form_params'=>$bodyParameters]);
        return json_decode($response->getBody(),true);
    }

    public function postRequest($resource, $bodyParameters){
        $response = $this->client->request('POST', $this->baseUrl.$resource , ['headers'=> ['Accept' => 'application/json','Authorization' => "Bearer ".$this->user->getToken().""],'form_params'=>$bodyParameters]);
        return json_decode($response->getBody(),true);
    }


    public function postMultipartRequest($resource, $bodyParameters, $profileImageArr){

        $multipartArr = array();
        foreach($bodyParameters as $k=>$v){
            array_push(
                $multipartArr,
                array(
                    'name' => $k,
                    'contents' => $v
                )
            );
        }
        if(count($profileImageArr) > 0){
            array_push($multipartArr, $profileImageArr);
        }
        $response = $this->client->request('POST', $this->baseUrl.$resource , ['headers'=> ['Accept' => 'application/json','Authorization' => "Bearer ".$this->user->getToken().""], 'multipart' => $multipartArr]);
        return json_decode($response->getBody(),true);
    }






    /**
     * Post Json Data
     *
     * @param $resource
     * @param $jsonArray
     * @return mixed
     */
    public function postJson($resource, $jsonArray){
        $response = $this->client->request('POST', $this->baseUrl.$resource , ['headers'=> $this->getHeaderParameters(), 'json' => $jsonArray ]);
        //$response = $this->client->post($this->baseUrl.$resource , $headers, json_encode($jsonArray))->send();
        //return $response->getStatusCode();
        return json_decode($response->getBody(),true);
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
        }
        return $headerParams;
    }

    /**
     * Get token from session
     *
     * @return string
     */
    private function getSessionStoredToken(){
        $session = $this->container->get('session');
        $token = $session->get('_token');
        return $token;
    }


}//@