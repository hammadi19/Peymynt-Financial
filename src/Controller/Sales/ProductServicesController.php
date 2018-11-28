<?php
namespace App\Controller\Sales;

use App\Base\Bridge\RESTBridge;
use App\Helper\ResponseSessionHelper;
use App\Helper\RouteHelper;
use App\Security\ApiUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


/**
 * @Route("/sales/product_services")
 */
class ProductServicesController  extends Controller
{
    /**
     * @Route("/manage", name="sales.product_services.manage")
     * @Template();
     */
    public function manageAction()
    {
        /** @var ApiUser $user */
        $user = $this->getUser();
        /** @var RESTBridge $bridge */
        $bridge = $this->get('app_rest_bridge');
        $resourceProductList = RouteHelper::replaceParamInRoute(
            ['{business_id}' => $user->getBusinessId()],
            $this->getParameter('rest_endpoints')['sales_product_list_endpoint']
        );
        $response = $bridge->get($resourceProductList);
        $products = [];
        if(Response::HTTP_OK === $response['code']){
            $products = $response['data'];
        }
        return [
            'data' => $products,
            'errors' => [],
            'success' => [],
        ];
    }

    /**
     * @Route("/new", name="sales.product_services.new")
     * @Template();
     */
    public function newAction(Request $request)
    {
        $incomeAccountArray["consulting_income"] = "Consulting Income";
        $incomeAccountArray["sales"] = "Sales";

        $expenseAccountArray["accounting_fees"] = "Accounting Fees";
        $expenseAccountArray["bank_service_charges"] = "Bank Service Charges";
        $expenseAccountArray["computer_hardware"] = "Computer – Hardware";
        $expenseAccountArray["computer_hosting"] = "Computer – Hosting";
        $expenseAccountArray["computer_internet"] = "Computer – Internet";
        $expenseAccountArray["computer_software"] = "Computer – Software";
        $expenseAccountArray["depreciation_expense"] = "Depreciation Expense";
        $expenseAccountArray["dues_subscriptions"] = "Dues & Subscriptions";
        $expenseAccountArray["education_training"] = "Education & Training";
        $expenseAccountArray["insurance_general_liability"] = "Insurance – General Liability";
        $expenseAccountArray["insurance_vehicles"] = "Insurance – Vehicles";
        $expenseAccountArray["interest_expense"] = "Interest Expense";
        $expenseAccountArray["meals_and_entertainment"] = "Meals and Entertainment";
        $expenseAccountArray["office_supplies"] = "Office Supplies";
        $expenseAccountArray["payroll_employee_benefits"] = "Payroll – Employee Benefits";
        $expenseAccountArray["payroll_employer_share_benefits"] = "Payroll – Employer's Share of Benefits";
        $expenseAccountArray["payroll_salary"] = "Payroll – Salary & Wages";
        $expenseAccountArray["professional_fees"] = "Professional Fees";
        $expenseAccountArray["rent_expense"] = "Rent Expense";
        $expenseAccountArray["repairs_maintenance"] = "Repairs & Maintenance";
        $expenseAccountArray["telephone_land_line"] = "Telephone – Land Line";
        $expenseAccountArray["telephone_wireless"] = "Telephone – Wireless";
        $expenseAccountArray["travel_expense"] = "Travel Expense";
        $expenseAccountArray["utilities"] = "Utilities";
        $expenseAccountArray["vehicle_fuel"] = "Vehicle – Fuel";
        $expenseAccountArray["vehicle_repair_maintenance"] = "Vehicle – Repairs & Maintenance";

        $errors = array();
        $success = array();
        $userDataArray = array();

        /** @var RESTBridge $bridge */
        $bridge = $this->get('app_rest_bridge');
        $user = $this->getUser();
        $resourceTaxes = RouteHelper::replaceParamInRoute(
            ['{business_id}' => $user->getBusinessId()],
            $this->getParameter('rest_endpoints')['tax_list_endpoint']
        );
        $taxes =  $bridge->get($resourceTaxes);


        if($request->request->get('createProductForm')) {
            $formValues = $request->request->get('createProductForm');

            $userDataArray = $formValues;
            $name = $formValues['name'];
            $description = $formValues['description'];
            $price = $formValues['price'];
            $isSell = !empty($formValues['is_sell']) ? true : false;
            $isBuy = !empty($formValues['is_buy']) ? true : false;
            $salesTax = $formValues['sales_tax'];
            $incomeAccount = ($isSell == true) ? $formValues['income_account'] : '';
            $expenseAccount = ($isBuy == true) ? $formValues['expense_account'] : '';

            if (empty($name)) {
                array_push($errors, 'Product Name field cannot be empty');
            }
            if (empty($price) || !is_numeric($price)) {
                array_push($errors, 'Product Price cannot be empty and should be number');
            }
            if ($isBuy === false && $isSell === false) {
                array_push($errors, 'Please indicate whether you will be buying or selling this product or both.');
            }
            if (empty($salesTax) || !is_numeric($salesTax)) {
                array_push($errors, 'Product Sales Tax cannot be empty and should be number');
            }
           
            if(0 === count($errors)) {
                $params = array(
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'is_sell' => (($isSell) ? true : false),
                    'is_buy' => (($isBuy) ? true : false),
                    'sales_tax' => $salesTax,
                    'income_account' => $isSell ? $incomeAccount : false,
                    'expense_account' => $isBuy ? $expenseAccount : false,
                );

                $resourceProduct = RouteHelper::replaceParamInRoute(
                    ['{business_id}' => $user->getBusinessId()],
                    $this->getParameter('rest_endpoints')['sales_product_create_endpoint']
                );
                $responseArray = $bridge->post($resourceProduct, $params);
                if(200 == $responseArray['code']){
                    $success = $responseArray['message'];
                    $this->get('session')
                        ->getFlashBag()
                        ->add('success', $success);
                    return $this->redirectToRoute('sales.product_services.manage');
                } else {
                    array_push($errors, $responseArray['message']);
                }
            }
            
        }

        return array(
            'success'=>$success,
            'errors' => $errors,
            'data' => $userDataArray,
            'expenseAccountArray' => $expenseAccountArray,
            'incomeAccountArray' => $incomeAccountArray,
            'taxes' => $taxes['data']
        );
    }

    /**
     * @Route(name="/ajax-new", name="sales.product_services.ajax-new")
     * @param Request $request
     */
    public function ajaxNew(Request $request){
        $formValues = $request->request->all();
        $errors = [];
        $name = $formValues['name'];
        $description = $formValues['description'];
        $price = $formValues['price'];

        if (empty($name)) {
            array_push($errors, 'Product Name field cannot be empty');
        }
        if (empty($price) || !is_numeric($price)) {
            array_push($errors, 'Product Price cannot be empty and should be number');
        }

        if(0 === count($errors)) {
            $params = array(
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'is_sell' => true,
                'income_account' => $formValues['income_account'],
                'is_buy' => false,
                'expense_account' => false,
            );

            /** @var RESTBridge $bridge */
            $bridge = $this->get('app_rest_bridge');
            $user = $this->getUser();
            $resourceProduct = RouteHelper::replaceParamInRoute(
                ['{business_id}' => $user->getBusinessId()],
                $this->getParameter('rest_endpoints')['sales_product_create_endpoint']
            );
            $responseArray = $bridge->post($resourceProduct, $params);

            return new JsonResponse($responseArray, $responseArray['code']);
        }

        return new JsonResponse($errors, 400);
    }

    /**
     * @Route("/update/{id}", name="sales.product_services.update")
     * @Template();
     */
    public function updateAction(Request $request,$id)
    {
        $errors = [];
        if($request->request->get('createProductForm')) {
            $formValues = $request->request->get('createProductForm');

            $name = $formValues['name'];
            $description = $formValues['description'];
            $price = $formValues['price'];
            $isSell = !empty($formValues['is_sell'])?true:false;
            $isBuy =  !empty($formValues['is_buy'])?true:false;
            $salesTax = $formValues['sales_tax'];
            $incomeAccount = ($isSell==true)?$formValues['income_account']:'';
            $expenseAccount = ($isBuy==true)?$formValues['expense_account']:'';

            if (empty($name)) {
                array_push($errors, 'Product Name field cannot be empty');
            }
            if (empty($price) || !is_numeric($price)) {
                array_push($errors, 'Product Price cannot be empty and should be number');
            }
            if ($isBuy === false && $isSell === false) {
                array_push($errors, 'Please indicate whether you will be buying or selling this product or both.');
            }
            if (empty($salesTax) || !is_numeric($salesTax)) {
                array_push($errors, 'Product Sales Tax cannot be empty and should be number');
            }
           
            if(0 === count($errors)) {
                $params = array(
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'is_sell' => (($isSell) ? true : false),
                    'is_buy' => (($isBuy) ? true : false),
                    'sales_tax' => $salesTax,
                    'income_account' => $isSell ? $incomeAccount : false,
                    'expense_account' => $isBuy ? $expenseAccount : false,
                    'product_id' => $id,

                );

                /** @var ApiUser $user */
                $user = $this->getUser();
                /** @var RESTBridge $bridge */
                $bridge = $this->get('app_rest_bridge');
                $resourceUpdateProduct = RouteHelper::replaceParamInRoute(
                    [
                        '{business_id}' => $user->getBusinessId(),
                        '{product_id}' => $id,
                    ],
                    $this->getParameter('rest_endpoints')['sales_product_update_endpoint']
                );
                $response = $bridge->put($resourceUpdateProduct, $params);
                ResponseSessionHelper::addResponseMessageToSession($this->get('session'), $response);
            }else{
                foreach ($errors as $error){
                    $this->get('session')
                        ->getFlashBag()
                        ->add('error', $error);
                }
            }
        }

        return $this->redirectToRoute('sales.product_services.manage');
    }

    /**
     * @Route("/view/{id}", name="sales.product_services.view")
     * @param $id
     * @Template()
     */
    public function view(int $id)
    {
        $incomeAccountArray["consulting_income"] = "Consulting Income";
        $incomeAccountArray["sales"] = "Sales";

        $expenseAccountArray["accounting_fees"] = "Accounting Fees";
        $expenseAccountArray["bank_service_charges"] = "Bank Service Charges";
        $expenseAccountArray["computer_hardware"] = "Computer – Hardware";
        $expenseAccountArray["computer_hosting"] = "Computer – Hosting";
        $expenseAccountArray["computer_internet"] = "Computer – Internet";
        $expenseAccountArray["computer_software"] = "Computer – Software";
        $expenseAccountArray["depreciation_expense"] = "Depreciation Expense";
        $expenseAccountArray["dues_subscriptions"] = "Dues & Subscriptions";
        $expenseAccountArray["education_training"] = "Education & Training";
        $expenseAccountArray["insurance_general_liability"] = "Insurance – General Liability";
        $expenseAccountArray["insurance_vehicles"] = "Insurance – Vehicles";
        $expenseAccountArray["interest_expense"] = "Interest Expense";
        $expenseAccountArray["meals_and_entertainment"] = "Meals and Entertainment";
        $expenseAccountArray["office_supplies"] = "Office Supplies";
        $expenseAccountArray["payroll_employee_benefits"] = "Payroll – Employee Benefits";
        $expenseAccountArray["payroll_employer_share_benefits"] = "Payroll – Employer's Share of Benefits";
        $expenseAccountArray["payroll_salary"] = "Payroll – Salary & Wages";
        $expenseAccountArray["professional_fees"] = "Professional Fees";
        $expenseAccountArray["rent_expense"] = "Rent Expense";
        $expenseAccountArray["repairs_maintenance"] = "Repairs & Maintenance";
        $expenseAccountArray["telephone_land_line"] = "Telephone – Land Line";
        $expenseAccountArray["telephone_wireless"] = "Telephone – Wireless";
        $expenseAccountArray["travel_expense"] = "Travel Expense";
        $expenseAccountArray["utilities"] = "Utilities";
        $expenseAccountArray["vehicle_fuel"] = "Vehicle – Fuel";
        $expenseAccountArray["vehicle_repair_maintenance"] = "Vehicle – Repairs & Maintenance";

        /** @var ApiUser $user */
        $user = $this->getUser();
        /** @var RESTBridge $bridge */
        $bridge = $this->get('app_rest_bridge');
        $resourceTaxes = RouteHelper::replaceParamInRoute(
            ['{business_id}' => $user->getBusinessId()],
            $this->getParameter('rest_endpoints')['tax_list_endpoint']
        );
        $taxes =  $bridge->get($resourceTaxes);
        $productResource = RouteHelper::replaceParamInRoute(
            [
                '{business_id}' => $user->getBusinessId(),
                '{product_id}' => $id,
            ],
            $this->getParameter('rest_endpoints')['sales_product_view_endpoint']
        );
        $response = $bridge->get($productResource);
        if (Response::HTTP_OK !== $response['code']) {
            $this->get('session')
                ->getFlashBag()
                ->add('error', $response['message']);
            return $this->redirectToRoute('sales.customer.manage');
        }

        return [
            'product' => $response['data'],
            'errors' => [],
            'success' => [],
            'expenseAccountArray' => $expenseAccountArray,
            'incomeAccountArray' => $incomeAccountArray,
            'taxes' => $taxes['data'],
        ];
    }

    /**
     * @Route("/delete/{id}", name="sales.product_services.delete")
     * @param int $id
     * @return RedirectResponse
     */
    public function deleteAction(int $id)
    {
        /** @var ApiUser $user */
        $user = $this->getUser();
        /** @var RESTBridge $bridge */
        $bridge = $this->get('app_rest_bridge');
        $resourceProductDelete = RouteHelper::replaceParamInRoute(
            [
                '{business_id}' => $user->getBusinessId(),
                '{product_id}' => $id,
            ],
            $this->getParameter('rest_endpoints')['sales_product_delete_endpoint']
        );

        $response = $bridge->delete($resourceProductDelete);
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

        return $this->redirectToRoute('sales.product_services.manage');
    }
}