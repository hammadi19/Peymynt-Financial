<?php

namespace App\EventListener;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManager;


/**
 * Class LoginListener
 * @package App\EventListener
 */
class LoginListener {

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Container
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
     *
     * @var $session
     */
    protected $session;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param EntityManager $entityManager
     * @param ContainerInterface $container
     * @param RequestStack $requestStack
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker,EntityManager $entityManager,ContainerInterface $container,RequestStack $requestStack )
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->entityManager        = $entityManager;
        $this->container            = $container;
        $this->requestStack         = $requestStack;
    }


    /**
     * @param InteractiveLoginEvent $event
     * @return null
     */
    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        /*
        $this->user = $event->getAuthenticationToken()->getUser();
        if("object" === gettype($this->user)){
            if(NULL == $this->user->getIsApproved() || false == $this->user->getIsApproved()){
                $this->container->get('event_dispatcher')->addListener(KernelEvents::RESPONSE, function (FilterResponseEvent $event) {
                    $router = $this->container->get('router');
                    $event->setResponse(new RedirectResponse($router->generate('dr_iq.content.un_approved')));
                });
            }
        }
        */
    }

}//@