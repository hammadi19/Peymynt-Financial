<?php

namespace App\Base\IO;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Handle path related functions
 *
 * Class Path
 * @package ATMedics\SystemBundle\Base\IO
 */
class Path
{

    /**
     * @var ContainerInterface $container
     */
    private $container;

    /**
     * @var string $webDir1
     */
    public $webDir1;

    /**
     * @var string $webDir2
     */
    public $webDir2;

    /**
     * @var object $fileLoc
     */
    public $fileLoc;

    /**
     * @var $logDir
     */
    public $logDir;

    /**
     * @var $rootDir
     */
    public $rootDir;

    /**
     * @var string $sitePath
     */
    public $sitePath;

    public $schema;


    public $basePath;

    /**
     * Set startups
     *
     * @param ContainerInterface $container
     */
    public function __construct( ContainerInterface $container )
    {
        $this->container = $container;
        $this->fileLoc   = $this->container->get('file_locator');
        $this->webDir1   = realpath($this->container->get('kernel')->getRootDir() . '/../web');
        $this->webDir2   = $this->container->get('kernel')->getRootDir() . '/../web';
        $this->logDir    = $this->container->get('kernel')->getLogDir();
        $this->rootDir   = $this->container->get('kernel')->getRootDir();

        $request         = $this->container->get('request_stack')->getCurrentRequest();
        $this->sitePath  = 'http://'.$request->getHost().$request->getBasePath().'/uploads/';
        $this->basePath  = $request->getScheme().'://'.$request->getHost().$request->getBasePath();
        $this->schema = $request->getScheme();
    }


}