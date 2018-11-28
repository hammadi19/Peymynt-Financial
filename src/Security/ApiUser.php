<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;


/**
 * Class ApiUser
 * @package App\Security
 */
class ApiUser implements AdvancedUserInterface, \Serializable
{

    /**
     * @var $username
     */
    private $username;

    /**
     * @var $password
     */
    private $password;

    /**
     * @var $salt
     */
    private $salt;

    /**
     * @var array $roles
     */
    private $roles;

    /**
     * @var $token
     */
    private $token;

    /**
     * @var null|int
     */
    private $business_id;

    /**
     * @var null|string
     */
    private $currency;

    /**
     * @var null|string
     */
    private $email;

    /**
     * APIUser constructor.
     * @param $username
     * @param $password
     * @param $salt
     * @param array $roles
     * @param $token
     * @param string $email
     * @param int $business_id
     * @param string $currency
     */
    public function __construct($username, $password, $salt, array $roles, $token, string $email, int $business_id, string $currency)
    {
        $this->username = $username;
        $this->password = $password;
        $this->salt = $salt;
        $this->roles = $roles;
        $this->token = $token;
        $this->email = $email;
        $this->business_id = $business_id;
        $this->currency = $currency;
    }


    public function setRoles($roles)
    {
        $this->roles = $roles;
    }


    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return mixed
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    public function eraseCredentials()
    {}

    /**
     * @param UserInterface $user
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof self) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * @return int|null
     */
    public function getBusinessId(): ?int
    {
        return $this->business_id;
    }

    /**
     * @param int|null $business_id
     */
    public function setBusinessId(?int $business_id): void
    {
        $this->business_id = $business_id;
    }

    /**
     * @return null|string
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * @param null|string $currency
     */
    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param null|string $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize([
            $this->token,
            $this->username,
            $this->password,
            $this->business_id,
            $this->currency,
            $this->email,
        ]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->token,
            $this->username,
            $this->password,
            $this->business_id,
            $this->currency,
            $this->email
            ) = unserialize($serialized,array('allowed_classes' => false));
    }

}//@