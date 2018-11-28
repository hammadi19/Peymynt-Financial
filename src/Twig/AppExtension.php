<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Twig\AppRuntime;

class AppExtension extends AbstractExtension
{

    public function getFunctions()
    {
        return array(
            new TwigFunction('user_personal_info', array(AppRuntime::class, 'userPersonalInfoFunc')),
            new TwigFunction('user_business_info', array(AppRuntime::class, 'userBusinessInfoFunc')),
            new TwigFunction('user_business_name', array(AppRuntime::class, 'userBusinessNameFunc')),
        );
    }

}//@
