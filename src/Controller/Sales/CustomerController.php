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
 * @Route("/sales/customer")
 */
class CustomerController  extends Controller
{
    /**
     * @Route("/manage", name="sales.customer.manage")
     * @Template();
     */
    public function manageAction()
    {
        /** @var ApiUser $user */
        $user = $this->getUser();
        /** @var RESTBridge $bridge */
        $bridge = $this->get('app_rest_bridge');
        $resourceCustomerList = RouteHelper::replaceParamInRoute(
            ['{business_id}' => $user->getBusinessId()],
            $this->getParameter('rest_endpoints')['customer_list_endpoint']
        );
        $response = $bridge->get($resourceCustomerList);
        $customers = [];
        if(Response::HTTP_OK === $response['code']){
            $customers = $response['data'];
        }
        return [
            'customers' => $customers,
        ];
    }

    /**
     * @Route("/add", name="sales.customer.new")
     * @Template();
     */
    public function create(Request $request)
    {
        $errors = array();
        $success = null;

        $bridge = $this->get('app_rest_bridge');
        $resourceCustomerAdd = $this->getParameter('rest_endpoints')['customer_add_endpoint'];
        $businessUrl = $this->getParameter('rest_endpoints')['settings_list_user_business_endpoint'];
        $business = $bridge->get($businessUrl);
        if ($request->request->get('addCustomerForm')) {

            $formValues = $request->request->get('addCustomerForm');
            $company_name = $formValues['company_name'];
            $business_id = $formValues['business_id'];

            if (empty($company_name)) {
                array_push($errors, 'Company field cannot be empty');
            }
            if (empty($business_id)) {
                array_push($errors, 'Business field cannot be empty');
            }

            if(0 === count($errors)) {

                $addCustomerResponseArray = $bridge->post($resourceCustomerAdd, $formValues);
                if(200 == $addCustomerResponseArray['code']){
                    $success = $addCustomerResponseArray['message'];
                    $request->getSession()
                        ->getFlashBag()
                        ->add('success', $success);
                    return $this->redirectToRoute('sales.customer.manage');

                }else{
                    array_push($errors,$addCustomerResponseArray['message']);
                }
            }

        }

        return array(
            "success"=>$success,
            "errors" => $errors,
            'businesses' => $business['data'] ?? [],
        );
    }

    /**
     * @Route("/add-ajax", name="sales.customer.new-ajax")
     */
    public function createAjax(Request $request)
    {
        $errors = array();
        $success = null;
        $code = 400;
        $bridge = $this->get('app_rest_bridge');
        $resourceCustomerAdd = $this->getParameter('rest_endpoints')['customer_add_endpoint'];
        if ($request->request->get('addCustomerForm')) {

            $formValues = $request->request->get('addCustomerForm');
            $company_name = $formValues['company_name'];
            $business_id = $formValues['business_id'];

            if (empty($company_name)) {
                array_push($errors, 'Company field cannot be empty');
            }
            if (empty($business_id)) {
                array_push($errors, 'Business field cannot be empty');
            }

            if (0 === count($errors)) {

                $addCustomerResponseArray = $bridge->post($resourceCustomerAdd, $formValues);
                if (200 == $addCustomerResponseArray['code']) {
                    $success = $addCustomerResponseArray['message'];
                    $id = $addCustomerResponseArray['id'];
                    $code = 200;
                } else {
                    array_push($errors, $addCustomerResponseArray['message']);
                }
            }
        }

        return new JsonResponse([
            "success" => $success,
            "errors" => $errors,
            'businesses' => $business['data'] ?? [],
            'id' => $id ?? null
        ], $code);
    }

    /**
     * @Route("/delete/{id}", name="sales.customer.delete")
     * @param $id
     * @return RedirectResponse
     */
    public function delete(int $id){
        /** @var ApiUser $user */
        $user = $this->getUser();
        /** @var RESTBridge $bridge */
        $bridge = $this->get('app_rest_bridge');
        $resourceCustomerDelete = RouteHelper::replaceParamInRoute(
            [
                '{business_id}' => $user->getBusinessId(),
                '{customer_id}' => $id,
            ],
            $this->getParameter('rest_endpoints')['customer_delete_endpoint']
        );

        $response = $bridge->delete($resourceCustomerDelete);
        $message = $response['message'];
        if(200 == $response['code']){
            $this->get('session')
                ->getFlashBag()
                ->add('success', $message);
        }else{
            $this->get('session')
                ->getFlashBag()
                ->add('error', $message);
        }

        return $this->redirectToRoute('sales.customer.manage');
    }


    /**
     * @Route("/edit/{id}", name="sales.customer.edit")
     * @param $id
     * @return RedirectResponse
     */
    public function edit(Request $request, int $id){
        /** @var ApiUser $user */
        $user = $this->getUser();
        /** @var RESTBridge $bridge */
        $bridge = $this->get('app_rest_bridge');
        $resourceCustomerUpdate = RouteHelper::replaceParamInRoute(
            [
                '{business_id}' => $user->getBusinessId(),
                '{customer_id}' => $id,
            ],
            $this->getParameter('rest_endpoints')['customer_update_endpoint']
        );
        $formValues = $request->request->get('addCustomerForm');

        $response = $bridge->put($resourceCustomerUpdate, $formValues);
        $message = $response['message'];
        if(200 == $response['code']){
            $this->get('session')
                ->getFlashBag()
                ->add('success', $message);
        }else{
            $this->get('session')
                ->getFlashBag()
                ->add('error', $message);
        }

        return $this->redirectToRoute('sales.customer.manage');
    }

    /**
     * @Route("/view/{id}", name="sales.customer.view")
     * @param $id
     * @Template()
     */
    public function view(int $id)
    {
        /** @var ApiUser $user */
        $user = $this->getUser();
        /** @var RESTBridge $bridge */
        $bridge = $this->get('app_rest_bridge');
        $resourceCustomerView = RouteHelper::replaceParamInRoute(
            [
                '{business_id}' => $user->getBusinessId(),
                '{customer_id}' => $id,
            ],
            $this->getParameter('rest_endpoints')['customer_view_endpoint']
        );
        $businessUrl = $this->getParameter('rest_endpoints')['settings_list_user_business_endpoint'];
        $business = $bridge->get($businessUrl);
        $customer = $bridge->get($resourceCustomerView);
        $errors = [];
        if(Response::HTTP_OK !== $business['code'] || Response::HTTP_OK !== $customer['code']){
            return $this->redirectToRoute('sales.customer.manage');
        }

        return [
            'customer' => $customer['data'][0],
            'businesses' => $business['data'],
            'errors' => $errors,
            'countries' => SelectData::getCountries(),
            'provinces' => SelectData::getProvinces(),
            'currencies' => SelectData::getCurrencies(),
        ];
    }
}