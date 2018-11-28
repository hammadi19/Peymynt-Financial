<?php

namespace App\Controller;

use App\Base\Bridge\RESTBridge;
use App\Helper\ResponseSessionHelper;
use App\Helper\RouteHelper;
use App\Helper\SelectData;
use App\Security\ApiUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 *  UserSettingsController
 * @Route("/secure/user/settings")
 */
class UserSettingsController extends Controller
{

    /**
     * @Route("/", name="app_user_settings")
     * @Template()
     */
    public function settings(Request $request)
    {

        $errors = array();
        $success = null;
        $userDataArray = array();

        $user = $this->getUser();
        $bridge = $this->get('app_rest_bridge');
        $resourceProfile = $this->getParameter('rest_endpoints')['settings_view_profile_endpoint'];
        $resourceUpdateProfile = $this->getParameter('rest_endpoints')['settings_update_profile_endpoint'];
        $profileResponse = $bridge->get($resourceProfile);
        $profileResponseArray = $profileResponse["data"];

        if ($request->request->get('accountInfoForm')) {

            $formValues = $request->request->get('accountInfoForm');
            $email = $formValues['email'];
            $first_name = $formValues['first_name'];
            $last_name = $formValues['last_name'];
            $country = $formValues['country'];
            $province = $formValues['province'];
            $city = $formValues['city'];
            $post_code = $formValues['post_code'];
            $dob = $formValues['dob'];

//            if (empty($first_name)) {
//                array_push($errors, 'First Name field cannot be empty');
//            }
//            if (empty($last_name)) {
//                array_push($errors, 'Last Name field cannot be empty');
//            }
//            if (empty($country)) {
//                array_push($errors, 'Country must be selected');
//            }
//            if (empty($province)) {
//                array_push($errors, 'Province must be selected');
//            }
//            if (empty($province)) {
//                array_push($errors, 'City field cannot be empty');
//            }

            if(0 === count($errors)) {


                $filesBag = $request->files->all();
                $profileImageArr = array();
//                if ($filesBag['profile_image'] != "") {
//                    $path = $this->container->get('app_system_path');
//                    $file = $filesBag['profile_image'];
//                    $filename = $file->getClientOriginalName();
//                    $src = $path->webDir1 . '/uploads/temp/';
//                    $exploded = explode(".", $filename);
//                    $extension = end($exploded);
//                    $newFileName = md5(uniqid()) . "." . $extension;
//                    $file->move($src, $newFileName);
//                    $profileImageArr["name"] = "profile_image";
//                    $profileImageArr["contents"] = fopen( $src.$newFileName, 'r' );
//                }

                $params = array(
                    'email' => $email,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'country' => $country,
                    'province' => $province,
                    'city' => $city,
                    'post_code' => $post_code,
                    'date_of_birth' => $dob
                );
                $updAccountResponseArray = $bridge->postMultipartRequest($resourceUpdateProfile, $params, $profileImageArr);
                if (202 == $updAccountResponseArray['code']) {

                    $apiKey = $updAccountResponseArray['data']['token'];
                    $tokenParts = explode('.', $apiKey);
                    $payload = json_decode(base64_decode($tokenParts[1]), true);
                    $roles   = isset($payload['roles']) ? $payload['roles'] : [];
                    $apiUser = new ApiUser(
                        $user->getUsername() ,
                        null ,
                        '' ,
                        $roles,
                        $apiKey,
                        $payload['business_id'] ?? 0,
                        $payload['business_id'] ?? 0,
                        $payload['business_currency'] ?? 'USD'
                    );
                    $userPasswordToken = new UsernamePasswordToken(
                        $apiUser,
                        $apiUser->getPassword(),
                        'main',
                        $apiUser->getRoles(),
                        $payload['business_id'] ?? 0,
                        $payload['business_id'] ?? 0,
                        $payload['business_currency'] ?? 'USD'
                    );

                    $userPasswordToken->setAttribute('_first_name',$payload['first_name']);
                    $userPasswordToken->setAttribute('_profile_image',$payload['profile_image']);
                    $this->get('security.token_storage')->setToken($userPasswordToken);
                    $success = $updAccountResponseArray['data']['message'];

                }else if(200 == $updAccountResponseArray['code']){
                    $success = $updAccountResponseArray['message'];
                    $request->getSession()
                        ->getFlashBag()
                        ->add('success', $success);
                    return $this->redirectToRoute('app_user_settings');

                }else{
                    array_push($errors,$updAccountResponseArray['message']);
                }
            }

        }

        return array(
            "data" => $profileResponseArray,
            "success"=>$success,
            "errors" => $errors,
        );
    }





    /**
     * @Route("/change-password", name="app_user_settings.change_password")
     * @Template()
     */
    public function changePassword(Request $request)
    {
        $errors = array();
        $success = null;
        $userDataArray = array();

        $bridge = $this->get('app_rest_bridge');
        $resourceChangePassword = $this->getParameter('rest_endpoints')['settings_change_password_endpoint'];

        if ($request->request->get('changePasswordForm')) {
            $formValues = $request->request->get('changePasswordForm');
            $current_password = $formValues['o_pass'];
            $new_password = $formValues['n_pass'];
            $repeat_new_password = $formValues['cn_pass'];
            if(empty($current_password) OR empty($new_password) OR empty($repeat_new_password)){
                array_push($errors, 'Password fields cannot be empty' );
            }
            elseif($new_password != $repeat_new_password){
                array_push($errors, 'New and Repeat Passwords do not match' );
            }
            if(0 === count($errors)) {
                $params = array(
                    'old_password' => $current_password,
                    'password' => $new_password
                );
                $chgPassResponseArray = $bridge->put($resourceChangePassword, $params);
                if(200 == $chgPassResponseArray['code']){
                    $success = $chgPassResponseArray["data"]['message'];
                    $request->getSession()
                        ->getFlashBag()
                        ->add('success', $success);
                    return $this->redirectToRoute('app_user_settings.change_password');
                }else{
                    array_push($errors,$chgPassResponseArray['message']);
                }
            }

        }

        return array(
            "success"=>$success,
            "errors" => $errors,
        );
    }




    /**
     * @Route("/email-notifications", name="app_user_settings.email_notifications")
     * @Template()
     */
    public function emailNotifications(Request $request)
    {

        $errors = array();
        $success = null;
        $userDataArray = array();

        $user = $this->getUser();
        $bridge = $this->get('app_rest_bridge');
        $resourceSettings = $this->getParameter('rest_endpoints')['settings_get_user_setting_endpoint'];
        $resourceUpdateSettings = $this->getParameter('rest_endpoints')['settings_update_user_setting_endpoint'];
        $settingsResponse = $bridge->get($resourceSettings);
        $settingsResponseArray = json_decode($settingsResponse["data"]);

        if ($request->request->get('updateEmailNotifiSettingForm')) {

            $formValues = $request->request->get('updateEmailNotifiSettingForm');

            if(isset($formValues['accounting'])){
                $settingsResponseArray->accounting->is_active = true;
            }else{
                $settingsResponseArray->accounting->is_active = false;
            }
            if(isset($formValues['sales'])){
                $settingsResponseArray->sales->is_active = true;
            }else{
                $settingsResponseArray->sales->is_active = false;
            }
            if(isset($formValues['payroll'])){
                $settingsResponseArray->payroll->is_active = true;
            }else{
                $settingsResponseArray->payroll->is_active = false;
            }
            if(isset($formValues['payments'])){
                $settingsResponseArray->payments->is_active = true;
            }else{
                $settingsResponseArray->payments->is_active = false;
            }
            if(isset($formValues['purchases'])){
                $settingsResponseArray->purchases->is_active = true;
            }else{
                $settingsResponseArray->purchases->is_active = false;
            }
            if(isset($formValues['banking'])){
                $settingsResponseArray->banking->is_active = true;
            }else{
                $settingsResponseArray->banking->is_active = false;
            }



            if(0 === count($errors)) {

                $params = array(
                    'accounting' => (array) $settingsResponseArray->accounting,
                    'sales' => (array) $settingsResponseArray->sales,
                    'payroll' => (array) $settingsResponseArray->payroll,
                    'payments' => (array) $settingsResponseArray->payments,
                    'purchases' => (array) $settingsResponseArray->purchases,
                    'banking' => (array) $settingsResponseArray->banking
                );

                $updEmailNotfSetResponseArray = $bridge->postJson($resourceUpdateSettings, $params);
                if(200 == $updEmailNotfSetResponseArray['code']){
                    $success = $updEmailNotfSetResponseArray['message'];
                    $request->getSession()
                        ->getFlashBag()
                        ->add('success', $success);
                    return $this->redirectToRoute('app_user_settings.email_notifications');
                }else{
                    array_push($errors,$updEmailNotfSetResponseArray['message']);
                }

            }

        }


        return array(
            "data" => $settingsResponseArray,
            "success"=>$success,
            "errors" => $errors,
        );
    }



    /**
     * @Route("/business/list", name="app_user_settings.business_list")
     * @Template()
     */
    public function businessList(Request $request)
    {

        $errors = array();
        $success = null;
        $userDataArray = array();

        $user = $this->getUser();
        $bridge = $this->get('app_rest_bridge');
        $resourceBusiness = $this->getParameter('rest_endpoints')['settings_list_user_business_endpoint'];
        $businessResponse = $bridge->get($resourceBusiness);

        return array(
            "data" => $businessResponse["data"],
            "success"=>$success,
            "errors" => $errors,
        );
    }



    /**
     * @Route("/business/create", name="app_user_settings.business_create")
     * @Template()
     */
    public function createBusiness(Request $request)
    {



        $errors = array();
        $success = null;
        $userDataArray = array();

        $bridge = $this->get('app_rest_bridge');
        $resourceBusiness = $this->getParameter('rest_endpoints')['settings_create_user_business_endpoint'];


        if ($request->request->get('createBusinessForm')) {
            $formValues = $request->request->get('createBusinessForm');
            $userDataArray = $formValues;
            $company_name = $formValues['company_name'];
            $business_type = $formValues['business_type'];
            $country = $formValues['country'];
            $business_currency = $formValues['business_currency'];
            $organization_type = $formValues['organization_type'];

            if (empty($company_name)) {
                array_push($errors, 'Company Name field cannot be empty');
            }
            if (empty($business_type)) {
                array_push($errors, 'Business Type must be selected');
            }
            if (empty($country)) {
                array_push($errors, 'Country must be selected');
            }
            if (empty($business_currency)) {
                array_push($errors, 'Business Currency must be selected');
            }
            if (empty($organization_type)) {
                array_push($errors, 'Organization Type must be selected');
            }


            if(0 === count($errors)) {
                $params = array(
                    'name' => $company_name,
                    'business_type' => $business_type,
                    'country' => $country,
                    'currency' => $business_currency,
                    'organization_type' => $organization_type
                );
                $responseArray = $bridge->post($resourceBusiness, $params);
                if(200 == $responseArray['code']){
                    $success = $responseArray['message'];
                    $request->getSession()
                        ->getFlashBag()
                        ->add('success', $success);
                    return $this->redirectToRoute('app_user_settings.business_list');
                }else{
                    array_push($errors,$responseArray['message']);
                }
            }

        }


        return array(
            "success"=>$success,
            "errors" => $errors,
            "data" => $userDataArray
        );
    }

    /**
     * @Route("/business/{id}/view", name="app_user_settings.business_view")
     * @Template()
     */
    public function businessView(int $id)
    {
        $errors = array();
        $success = null;

        /** @var ApiUser $user */
        $user = $this->getUser();
        /** @var RESTBridge $bridge */
        $bridge = $this->get('app_rest_bridge');
        $businessView = RouteHelper::replaceParamInRoute(
            [
                '{business_id}' => $id,
            ],
            $this->getParameter('rest_endpoints')['settings_view_business_endpoint']
        );
        $business = $bridge->get($businessView);
        if(Response::HTTP_OK !== $business['code']){
            return $this->redirectToRoute('sales.customer.manage');
        }

        return [
            'business' => $business['data'][0],
            'errors' => $errors,
            'countries' => SelectData::getCountries(),
            'provinces' => SelectData::getProvinces(),
            'currencies' => SelectData::getCurrencies(),
            'business_types' => SelectData::getBusinessTypes(),
            'organization_types' => SelectData::getOrganizationTypes(),
        ];
    }

    /**
     * @Route("/business/{id}/update", name="app_user_settings.business_update")
     */
    public function businessUpdate(Request $request, int $id)
    {
        /** @var RESTBridge $bridge */
        $bridge = $this->get('app_rest_bridge');
        $businessView = RouteHelper::replaceParamInRoute(
            [
                '{business_id}' => $id,
            ],
            $this->getParameter('rest_endpoints')['settings_edit_business_endpoint']
        );
        $business = $bridge->put($businessView, $request->request->get('createBusinessForm'));

        ResponseSessionHelper::addResponseMessageToSession($this->get('session'), $business);

        return $this->redirectToRoute('app_user_settings.business_list');
    }

    /**
     * @Route("/emails-connected-accounts/list", name="app_user_settings.emails_connected_accounts_list")
     * @Template()
     */
    public function emailsConnectedAccounts(Request $request)
    {

        $errors = array();
        $success = null;
        $userDataArray = array();

        $user = $this->getUser();
        $bridge = $this->get('app_rest_bridge');
        $resourceEmailsAccounts = $this->getParameter('rest_endpoints')['settings_list_emails_connected_accounts_endpoint'];
        $emailsAccountsResponse = $bridge->get($resourceEmailsAccounts);

        return array(
            "data" => $emailsAccountsResponse["data"],
            "success"=>$success,
            "errors" => $errors,
        );
    }


    /**
     * @Route("/emails-connected-accounts/create", name="app_user_settings.emails_connected_accounts_create")
     * @Template()
     */
    public function createEmailsConnectedAccounts(Request $request)
    {



        $errors = array();
        $success = null;
        $userDataArray = array();

        $bridge = $this->get('app_rest_bridge');
        $resourceBusiness = $this->getParameter('rest_endpoints')['settings_create_emails_connected_accounts_endpoint'];


        if ($request->request->get('createEmailConnectedForm')) {

            $formValues = $request->request->get('createEmailConnectedForm');
            $userDataArray = $formValues;
            $email = $formValues['email'];

            if (empty($email)) {
                array_push($errors, 'Email field cannot be empty');
            }


            if(0 === count($errors)) {
                $params = array(
                    'email' => $email
                );
                $responseArray = $bridge->post($resourceBusiness, $params);
                if(200 == $responseArray['code']){
                    $success = $responseArray['message'];
                    $request->getSession()->getFlashBag()->add('success', $success);

                }else{
                    $error = $responseArray['message'];
                    $request->getSession()->getFlashBag()->add('error', $error);
                }
            }

        }

        return $this->redirectToRoute('app_user_settings.emails_connected_accounts_list');

    }

    /**
     * @Route(path="/business/update/{id}", name="update_user_primary_business")
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function updateUserBusiness(Request $request, int $id)
    {
        /** @var ApiUser $user */
        $user = $this->getUser();
        //TODO check is in list of business, but is not so urgent cause API will take care of that

        $bridge = $this->get('app_rest_bridge');
        $resourceMakePrimary = $this->getParameter('rest_endpoints')['settings_make_primary_user_business_endpoint'];
        $params = array(
            'business_id' => $id
        );
        $responseArray = $bridge->post($resourceMakePrimary, $params);
        if (202 == $responseArray['code']) {
            $user->setBusinessId($id);
        }
        return $this->redirectToRoute('app_user_dashboard');
    }

}
