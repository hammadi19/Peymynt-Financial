<?php
declare(strict_types=1);

namespace App\Controller\Sales;

use App\Base\Bridge\RESTBridge;
use App\Helper\RouteHelper;
use App\Helper\ValidatorHelper;
use App\Security\ApiUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TaxController
 * @package App\Controller\Sales
 * @Route(path="/api/tax")
 */
class TaxController  extends Controller
{
    /**
     * @Route(path="/ajax-create", name="sales.tax.ajax-create")
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxCreate(Request $request){
        /** @var ApiUser $user */
        $user = $this->getUser();

        $formValues = $request->request->all();
        $errors = [];
        $name = $formValues['name'];
        $abbreviation = $formValues['abbreviation'];
        $tax_rate = $formValues['tax_rate'];

        if (ValidatorHelper::checkIsEmpty($name)) {
            $errors[] =  'Tax Name field cannot be empty';
        }
        if (ValidatorHelper::checkIsEmpty($abbreviation)) {
            $errors[] =  'Tax Abbreviation field cannot be empty';
        }
        if (ValidatorHelper::checkIsNotNumericAndNotEmpty($tax_rate)) {
            $errors[] =  'Tax tax_rate field must be numeric';
        }

        if(0 === count($errors)) {
            $formValues['business_id'] = $user->getBusinessId();

            /** @var RESTBridge $bridge */
            $bridge = $this->get('app_rest_bridge');
            $taxResource = $this->getParameter('rest_endpoints')['tax_create_endpoint'];
            $responseArray = $bridge->post($taxResource, $formValues);

            return new JsonResponse($responseArray, $responseArray['code']);
        }

        return new JsonResponse(['message' => $errors], 400);
    }
}