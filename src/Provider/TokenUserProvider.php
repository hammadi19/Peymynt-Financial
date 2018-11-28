<?php

namespace App\Provider;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use App\Security\ApiUser;


/**
 * Token User Provider.
 *
 * @DI\Service("project.token.user_provider")
 */
class TokenUserProvider implements UserProviderInterface
{

    private $token;


    /**
     * @param string $username
     * @return APIUser
     */
    public function loadUserByUsername($username)
    {
        return new ApiUser($username,  null, '' , ['ROLE_USER'], '','', 0, '');
    }

    /**
     * @param UserInterface $user
     * @return APIUser
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof ApiUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        $payload = $this->getPayload($user->getToken());
        $user->setRoles($payload['roles']);
        return $user;

        //return new ApiUser($payload['email'] , null, '' , $payload ,$user->getToken());
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return 'App\Security\ApiUser' === $class;
    }

    public function getPayload($token){
        $tokenParts = explode('.', $token);
        return json_decode(base64_decode($tokenParts[1]), true);
    }

}//@