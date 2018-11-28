<?php

namespace App\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimpleFormAuthenticatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use App\Security\ApiUser;
use GuzzleHttp\Client;


/**
 * Token Authenticator.
 *
 * @DI\Service("project.token.authenticator")
 */
class TokenAuthenticator implements SimpleFormAuthenticatorInterface
{
    /**
     * @var UserPasswordEncoderInterface $encoder
     */
    private $encoder;

    /**
     * @var $container
     */
    private $container;

    /**
     * @param UserPasswordEncoderInterface $encoder
     * @param Container $container
     */
    public function __construct(UserPasswordEncoderInterface $encoder, Container $container)
    {
        $this->encoder = $encoder;
        $this->container = $container;

    }

    /**
     * @param TokenInterface $token
     * @param UserProviderInterface $userProvider
     * @param $providerKey
     * @return UsernamePasswordToken
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        try {

            $data = [
                'form_params' => [
                    '_username' => trim( $token->getUsername()),
                    '_password' => trim($token->getAttribute('password')),
                ],
            ];

            $client = new Client(['http_errors' => false]);
            $resource = $this->container->getParameter('rest_endpoints')['base_server_url'].$this->container->getParameter('rest_endpoints')['login_endpoint'];
            $response = $client->request('POST', $resource , $data);

            if(401 === $response->getStatusCode()){
                throw new AuthenticationException('Invalid client credentials');
            }
            if(200 === $response->getStatusCode()){
                $responseArray = json_decode($response->getBody(), true);
                $apiKey = $responseArray['payload']['token'];
                $tokenParts = explode('.', $apiKey);
                $payload = json_decode(base64_decode($tokenParts[1]), true);
                //$payload  = $this->getPayload($apiKey);
                $roles   = isset($payload['roles']) ? $payload['roles'] : [];
                $user = new ApiUser(
                    $token->getUsername(),
                    $token->getAttribute('password'),
                    '',
                    $roles,
                    $apiKey,
                    $payload['email'],
                    $payload['business_id'] ?? 0,
                    $payload['business_currency'] ?? 'USD'
                );
                $userPasswordToken = new UsernamePasswordToken(
                    $user,
                    $user->getPassword(),
                    $providerKey,
                    $user->getRoles()
                );
                $userPasswordToken->setAttribute('_email',$payload['email']);
                $userPasswordToken->setAttribute('_first_name',$payload['first_name']);
                $userPasswordToken->setAttribute('_profile_image',$payload['profile_image']);

                return $userPasswordToken;
            }
            throw new CustomUserMessageAuthenticationException('Invalid username or password');
        } catch (UsernameNotFoundException $exception) {
            // CAUTION: this message will be returned to the client
            // (so don't put any un-trusted messages / error strings here)
            throw new CustomUserMessageAuthenticationException('Invalid username or password');
        }

        throw new CustomUserMessageAuthenticationException('Invalid username or password');
    }

    /**
     * @param TokenInterface $token
     * @param $providerKey
     * @return bool
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof UsernamePasswordToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * @param Request $request
     * @param $username
     * @param $password
     * @param $providerKey
     * @return UsernamePasswordToken
     */
    public function createToken(Request $request, $username, $password, $providerKey)
    {

        $userToken = new UsernamePasswordToken($username, $password, $providerKey);
        $userToken->setAttribute('password',$password);
        return $userToken;
    }

    public function getPayload($token){
        $tokenParts = explode('.', $token);
        return json_decode(base64_decode($tokenParts[1]), true);
    }



}