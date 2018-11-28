<?php

namespace App\EventListener;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Doctrine\ORM\EntityManager;


/**
 * Class AuthenticationFailureListener
 * @package DrIQ\UserBundle\Listener
 */
class AuthenticationFailureListener
{

    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function onFailure(AuthenticationFailureEvent $event)
    {}


}




