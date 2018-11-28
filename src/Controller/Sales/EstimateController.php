<?php

namespace App\Controller\Sales;

use App\Base\Bridge\RESTBridge;
use App\Helper\RouteHelper;
use App\Helper\SelectData;
use App\Security\ApiUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/sales/estimate")
 */
class EstimateController extends Controller
{

    /**
     * @Route("/manage", name="sales.estimate.manage")
     * @Template();
     */
    public function manageAction()
    {
        /** @var RESTBridge $bridge */
        $bridge = $this->get('app_rest_bridge');
        /** @var ApiUser $user */
        $user = $this->getUser();
        $resourceList = RouteHelper::replaceParamInRoute(
            ['{business_id}' => $user->getBusinessId()],
            $this->getParameter('rest_endpoints')['sales_estimate_list']
        );
        $response = $bridge->get($resourceList);
        $estimates = [];
        if (Response::HTTP_OK === $response['code']) {
            $estimates = $response['data'];
        }
        return [
            'data' => $estimates,
        ];
    }

    /**
     * @Route("/new", name="sales.estimate.new")
     * @Template();
     */
    public function newAction()
    {
        $bridge = $this->get('app_rest_bridge');
        $sales_estimate_default_load = sprintf($this->getParameter('rest_endpoints')['sales_estimate_default_load'], 7);
        $businessUrl = $this->getParameter('rest_endpoints')['settings_list_user_business_endpoint'];
        $business = $bridge->get($businessUrl);
        $sales_estimate_default_loadResponse = $bridge->get($sales_estimate_default_load);
        $resourceCustomerList = RouteHelper::replaceParamInRoute(
            ['{business_id}' => $this->getUser()->getBusinessId()],
            $this->getParameter('rest_endpoints')['customer_list_endpoint']
        );
        $resourceProductList = RouteHelper::replaceParamInRoute(
            ['{business_id}' => $this->getUser()->getBusinessId()],
            $this->getParameter('rest_endpoints')['sales_product_list_endpoint']
        );
        $resourceTaxList = RouteHelper::replaceParamInRoute(
            ['{business_id}' => $this->getUser()->getBusinessId()],
            $this->getParameter('rest_endpoints')['tax_list_endpoint']
        );
        $customers = $bridge->get($resourceCustomerList);
        $products = $bridge->get($resourceProductList);
        $taxes = $bridge->get($resourceTaxList);
        $tax_list = $this->getFields($taxes['data'], [
            'id' => 'id',
            'text' => 'name',
            'tax_rate' => 'tax_rate',
            'abbreviation' => 'abbreviation'
        ]);
        $data = $sales_estimate_default_loadResponse["data"];
        $data['customer_list'] = $customers['data'];
        $default_products = $this->getFields($products['data'], [
                'id' => 'id',
                'text' => 'name',
                'price' => 'price',
                'description' => 'description',
                'tax_id' => 'tax_id'
            ]) ?? [];

        return [
            'data' => [
                'default_products' => $default_products,
                'customer_list' => $customers['data'] ?? [],
                'tax_list' => $tax_list,
                'total' => $data['total'],
                'business_currency' => SelectData::getCurrencyCodeForShort($this->getUser()->getCurrency()),
            ],
            'businesses' => $business['data'] ?? [],
            'currencies' => SelectData::getCurrencies(),
            'business_currency' => SelectData::getCurrencyCodeForShort($this->getUser()->getCurrency()),
            'business_currency_short' => $this->getUser()->getCurrency(),
        ];
    }

    private function getFields(array $data, array $items)
    {
        $result = [];
        foreach ($data as $item) {
            $p = [];
            foreach ($items as $key => $value) {
                $p[$key] = $item[$value];
            }
            if (!empty($p))
                $result[] = $p;
        }

        return $result;
    }

    /**
     * @Route("/new/store", name="sales.estimate.new_store")
     * @Template();
     */
    public function createNewAction(Request $request)
    {
        /** @var RESTBridge $bridge */
        $bridge = $this->get('app_rest_bridge');
        /** @var ApiUser $user */
        $user = $this->getUser();
        $resourceList = RouteHelper::replaceParamInRoute(
            ['{business_id}' => $user->getBusinessId()],
            $this->getParameter('rest_endpoints')['sales_estimate_create']
        );
        $productResponse = $bridge->postJson($resourceList, $request->request->all());

        return new JsonResponse(array(
            "code" => Response::HTTP_OK,
            "data" => $productResponse
        ));

    }

    /**
     * @Route("/update/{estimate_id}", name="sales.estimate.update")
     * @Template();
     */
    public function updateAction(Request $request, int $estimate_id)
    {
        /** @var RESTBridge $bridge */
        $bridge = $this->get('app_rest_bridge');
        /** @var ApiUser $user */
        $user = $this->getUser();
        $resourceList = RouteHelper::replaceParamInRoute(
            [
                '{business_id}' => $user->getBusinessId(),
                '{estimate_id}' => $estimate_id,
            ],
            $this->getParameter('rest_endpoints')['sales_estimate_update']
        );
        $productResponse = $bridge->put($resourceList, $request->request->all());
        return new JsonResponse(array(
            "code" => Response::HTTP_OK,
            "data" => $productResponse
        ));

    }

    /**
     * @Route("/{estimate_id}/view/{customer}", name="sales.estimate.view", defaults={"customer":"view"},
     *     requirements={"customer":"customer|pdf"})
     */
    public function viewAction(int $estimate_id, $customer)
    {
        $data = $this->getData($estimate_id);
        if ($customer === 'pdf') {
            $mpdf = new \Mpdf\Mpdf(['format' => [216, 280]]);
            $mpdf->WriteHTML($this->render('sales/estimate/pdf.html.twig', $data));

            return $mpdf->Output($data['data']['estimate_data']['title'] . '.pdf', 'D');
        }

        $view = $customer === 'view' ? 'sales/estimate/view.html.twig' : 'sales/estimate/pdf.html.twig';
        return $this->render($view, $data);
    }

    private function getData($estimate_id)
    {
        /** @var ApiUser $user */
        $user = $this->getUser();
        /** @var RESTBridge $bridge */
        $bridge = $this->get('app_rest_bridge');
        $resource = RouteHelper::replaceParamInRoute(
            [
                '{business_id}' => $user->getBusinessId(),
                '{estimate_id}' => $estimate_id,
            ],
            $this->getParameter('rest_endpoints')['sales_estimate_view']
        );
        $estimate = $bridge->get($resource);
        $taxes = [];
        foreach ($estimate['data']['estimate_product_data'] as $estimate_product) {
            if ($estimate_product['taxes']) {
                $ep_taxes = json_decode($estimate_product['taxes']);
                foreach ($ep_taxes as $tax) {
                    $taxes[$tax] = $taxes[$tax] ?? 0;
                    $taxes[$tax] += (float)$estimate_product['price'] * $estimate_product['quantity'];
                }
            }
        }
        $resourceTaxList = RouteHelper::replaceParamInRoute(
            ['{business_id}' => $this->getUser()->getBusinessId()],
            $this->getParameter('rest_endpoints')['tax_list_endpoint']
        );
        $taxesApi = $bridge->get($resourceTaxList);
        foreach ($taxesApi['data'] as $tax) {
            if (in_array($tax['id'], array_keys($taxes))) {
                $taxes[$tax['id']] = [
                    'value' => $taxes[$tax['id']] * (float)$tax['tax_rate'] / 100,
                    'name' => $tax['name'],
                    'rate' => $tax['tax_rate'],
                ];
            }
        }
        // TODO set symbol
        $currency = $estimate['data']['estimate_data']['currency'];
        if (Response::HTTP_OK !== $estimate['code']) {
            return $this->redirectToRoute('sales.customer.manage');
        }

        return [
            'data' => $estimate['data'],
            'taxes' => $taxes,
            'errors' => [],
            'currency' => SelectData::getCurrencySymbol($currency),
        ];
    }

    /**
     * @Route(path="/send-email/{estimate_id}", methods={"POST"}, name="sales.estimate.send")
     */
    public function sendEmail(Request $request, \Swift_Mailer $mailer, $estimate_id)
    {
        $data = $this->getData($estimate_id);
        $input = $request->request->all();
        $data['text'] = $input['message'] ?? '';
        $text = $this->renderView('sales/estimate/pdf.html.twig', $data);
        $mails = is_array($input['emails']) ? $input['emails'] : [];
        if($input['own'] === 'on'){
            /** @var ApiUser $user*/
            $user = $this->getUser();
            $mails[] = $user->getEmail();
        }

        $mails = array_filter($mails,function ($mail){
            return filter_var($mail, FILTER_VALIDATE_EMAIL);
        });

        $subject = $data['data']['estimate_data']['title'] . ' #' . $data['data']['estimate_data']['estimate_no'] . 'from ' . $data['data']['business_data']['business_name'];
        foreach ($mails as $email){
            $message = (new \Swift_Message($subject))
                ->setFrom('postmaster@techalexa.com')
                ->setTo($email)
                ->setBody(
                    $text,
                    'text/html'
                );
            //TODO remove this when normal mail account is set
            try{
                $mailer->send($message);
            }catch (\Exception $e){}
        }

        return new JsonResponse([]);
    }

    /**
     * @Route("/delete/{estimate_id}", name="sales.estimate.delete")
     * @param int $estimate_id
     * @return RedirectResponse
     */
    public function deleteAction(int $estimate_id)
    {
        /** @var ApiUser $user */
        $user = $this->getUser();
        /** @var RESTBridge $bridge */
        $bridge = $this->get('app_rest_bridge');
        $resourceDelete = RouteHelper::replaceParamInRoute(
            [
                '{business_id}' => $user->getBusinessId(),
                '{estimate_id}' => $estimate_id,
            ],
            $this->getParameter('rest_endpoints')['sales_estimate_delete']
        );

        $response = $bridge->delete($resourceDelete);
        $message = $response['message'];
        if (200 == $response['code']) {
            $this->get('session')
                ->getFlashBag()
                ->add('success', $message);
        } else {
            $this->get('session')
                ->getFlashBag()
                ->add('error', $message);
        }

        return $this->redirectToRoute('sales.estimate.manage');
    }

    /**
     * @Route("/{estimate_id}", name="sales.estimate.edit", requirements={"estimate_id" : "\d+"})
     * @param int $estimate_id
     * @Template();
     */
    public function editAction(int $estimate_id)
    {
        $bridge = $this->get('app_rest_bridge');
        $sales_estimate_default_load = sprintf($this->getParameter('rest_endpoints')['sales_estimate_default_load'], 7);
        $businessUrl = $this->getParameter('rest_endpoints')['settings_list_user_business_endpoint'];
        $business = $bridge->get($businessUrl);
        $sales_estimate_default_loadResponse = $bridge->get($sales_estimate_default_load);
        $resourceCustomerList = RouteHelper::replaceParamInRoute(
            ['{business_id}' => $this->getUser()->getBusinessId()],
            $this->getParameter('rest_endpoints')['customer_list_endpoint']
        );
        $resourceProductList = RouteHelper::replaceParamInRoute(
            ['{business_id}' => $this->getUser()->getBusinessId()],
            $this->getParameter('rest_endpoints')['sales_product_list_endpoint']
        );
        $resourceTaxList = RouteHelper::replaceParamInRoute(
            ['{business_id}' => $this->getUser()->getBusinessId()],
            $this->getParameter('rest_endpoints')['tax_list_endpoint']
        );
        $customers = $bridge->get($resourceCustomerList);
        $products = $bridge->get($resourceProductList);
        $taxes = $bridge->get($resourceTaxList);
        $tax_list = $this->getFields($taxes['data'], [
            'id' => 'id',
            'text' => 'name',
            'tax_rate' => 'tax_rate',
            'abbreviation' => 'abbreviation'
        ]);
        $data = $sales_estimate_default_loadResponse["data"];
        $data['customer_list'] = $customers['data'];
        $default_products = $this->getFields($products['data'], [
                'id' => 'id',
                'text' => 'name',
                'price' => 'price',
                'description' => 'description',
                'tax_id' => 'tax_id'
            ]) ?? [];
        $resource = RouteHelper::replaceParamInRoute(
            [
                '{business_id}' => $this->getUser()->getBusinessId(),
                '{estimate_id}' => $estimate_id,
            ],
            $this->getParameter('rest_endpoints')['sales_estimate_view']
        );
        $estimate = $bridge->get($resource);
        try {
            foreach ($estimate['data']['estimate_product_data'] as &$ep) {
                $ep['taxes'] = json_decode($ep['taxes']);
            }
        } catch (\Exception $e) {
        }
        return [
            'data' => [
                'default_products' => $default_products,
                'customer_list' => $customers['data'] ?? [],
                'estimate_data' => $estimate['data']['estimate_data'] ?? [],
                'customer_data' => $estimate['data']['customer_data'] ?? [],
                'estimate_product_data' => $estimate['data']['estimate_product_data'] ?? [],
                'tax_list' => $tax_list,
                'total' => $data['total'],
                'business_currency' => SelectData::getCurrencyCodeForShort($this->getUser()->getCurrency()),
            ],
            'businesses' => $business['data'] ?? [],
            'currencies' => SelectData::getCurrencies(),
            'business_currency' => SelectData::getCurrencyCodeForShort($this->getUser()->getCurrency()),
            'business_currency_short' => $this->getUser()->getCurrency(),
        ];
    }


}