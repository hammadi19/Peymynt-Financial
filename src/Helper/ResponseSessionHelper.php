<?php
declare(strict_types=1);

namespace App\Helper;

class ResponseSessionHelper
{
    public static function addResponseMessageToSession($session, $response)
    {
        $message = $response['message'];
        if (200 == $response['code']) {
            $session
                ->getFlashBag()
                ->add('success', $message);

        } else {
            if (is_array($message)) {
                foreach ($message as $item) {
                    $session
                        ->getFlashBag()
                        ->add('error', $item);
                }
            } else {
                $session
                    ->getFlashBag()
                    ->add('error', $message);
            }

        }
    }

}