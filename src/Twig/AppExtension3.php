<?php

namespace App\Twig;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension3 extends AbstractExtension
{

    /**
     * @var $container
     */
    protected $container;

    /**
     * @var $requestStack
     */
    protected $requestStack;

    /**
     * @var $user
     */
    protected $user;

    /**
     * @param Container $container
     * @param EntityManager $entityManager
     */
    public function __construct(Container $container = null,  RequestStack $requestStack){

        $this->container        =   $container;
        $this->requestStack     =   $requestStack;
    }

    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('iq_path', array($this, 'iqPath')),
            new \Twig_SimpleFunction('user_profile_image', array($this, 'userProfileImage')),
        );
    }

    public function getEmailAssetsPath(){
        $pathHelper = $this->container->get('attech.path');
        return $pathHelper->basePath.'/';
    }

    public function iqPath($option)
    {
        return $this->getEmailAssetsPath();
    }

    public function userProfileImage()
    {
        $serverURL = $this->container->getParameter('rest_endpoints')['base_server_url'];
        $pathHelper = $this->container->get('attech.path');
        $session = $this->container->get('session');
        $userprofileImage = $session->get('_profile_image');
        $hostURL = $pathHelper->basePath.'/';

        if($userprofileImage == "" || $userprofileImage == null){
            $imageURL = $hostURL."assets/images/dr-iq-web-app/profile.png";
        }else{
            $imageURL = $serverURL."/uploads/dr-iq/profiles/".$userprofileImage;
        }
        return $imageURL;
    }


}//@