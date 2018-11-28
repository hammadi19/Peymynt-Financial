<?php
declare(strict_types=1);

namespace App\Controller\Sales;

use App\Helper\SelectData;
use App\Security\ApiUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CurrencyController
 * @package App\Controller\Sales
 * @Route(path="/currency")
 */
class CurrencyController extends Controller
{
    /**
     * @Route(path="/get-exchange-rate", name="currency.exchange.rate")
     */
    public function getExchangeRate(Request $request){
        $to = $request->query->get('to');
        /** @var ApiUser $user */
        $client       = new \GuzzleHttp\Client();
        try {
            $response = $client->request(
                'GET',
                $this->getParameter('currency_api_host'));
            $data = json_decode($response->getBody()->getContents(), true);
            $user = $this->getUser();
            $user->getCurrency();
            $rate = $data['rates'][$user->getCurrency()] / $data['rates'][ SelectData::getCurrenciesShort($to)];

        }catch (\Exception $e){
            $rate = 1;
        }
        return new JsonResponse([
            'rate' => round($rate, 9),
        ]);
    }
}