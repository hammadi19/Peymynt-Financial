<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;


class FrontController extends Controller
{

    /**
     * @Route("/", name="peymynt.welcome")
     */
    public function index()
    {
        return $this->render('front/welcome.html.twig', []);
    }

    /**
     * @Route("/secure/", name="peymynt.home")
     */
    public function home()
    {
        return $this->render('front/home.html.twig', []);
    }


    /**
     * @Route("/static-page-1", name="peymynt.static_page_1")
     */
    public function staticPage1()
    {
        return $this->render('front/static-page-1.html.twig', []);
    }

    /**
     * @Route("/static-page-2", name="peymynt.static_page_2")
     */
    public function staticPage2()
    {
        return $this->render('front/static-page-2.html.twig', []);
    }


    /**
     * display all suitable content page views
     * @Route("/{slug}", name="peymynt.content_pages",
     *  requirements={"slug":"contact|blog|partners|accounting|pricing|invoices|receipts|support|about|terms|additional|security|privacy|careers|success_stories"}
     * )
     */
    public function contentPages(Request $request, $slug)
    {

        $contentTemplate = '';
        switch ($slug) {
            case 'contact':
                $contentTemplate = 'front/contact.html.twig';
                break;
            case 'blog':
                $contentTemplate = 'front/blog.html.twig';
                break;
            case 'partners':
                $contentTemplate = 'front/partners.html.twig';
                break;
            case 'accounting':
                $contentTemplate = 'front/accounting.html.twig';
                break;
            case 'pricing':
                $contentTemplate = 'front/pricing.html.twig';
                break;
            case 'invoices':
                $contentTemplate = 'front/invoices.html.twig';
                break;
            case 'receipts':
                $contentTemplate = 'front/receipts.html.twig';
                break;
            case 'support':
                $contentTemplate = 'front/support.html.twig';
                break;
            case 'about':
                $contentTemplate = 'front/about.html.twig';
                break;
            case 'terms':
                $contentTemplate = 'front/terms.html.twig';
                break;
            case 'additional':
                $contentTemplate = 'front/additional_terms.html.twig';
                break;
            case 'security':
                $contentTemplate = 'front/security.html.twig';
                break;
            case 'privacy':
                $contentTemplate = 'front/privacy.html.twig';
                break;
            case 'careers':
                $contentTemplate = 'front/careers.html.twig';
                break;
            case 'success_stories':
                $contentTemplate = 'front/success_stories.html.twig';
                break;
        }

        return $this->render($contentTemplate, array());
    }








    /**
     * @Route("/contacts", name="taskbee.front.contact")
     */
    public function contact(Request $request)
    {

        $contentManager = $this->get('app_content_manager');
        $userProfileInfo = $contentManager->userProfileInfo();

        $errors = array();
        $success=null;
        $userDataArray = array();
        if ($request->request->get('contactform')) {

            $formValues = $request->request->get('contactform');


            $name = $formValues['name'];
            $email = $formValues['email'];
            $contact_no = $formValues['contact_no'];
            $message = $formValues['message'];
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $ipAddress = $_SERVER['REMOTE_ADDR'];

            $userDataArray['name'] = $name;
            $userDataArray['email'] = $email;
            $userDataArray['contact_no'] = $contact_no;
            $userDataArray['message'] = $message;
            $userDataArray['user_agent'] = $userAgent;
            $userDataArray['ip_address'] = $ipAddress;


            if (empty($name)) {
                array_push($errors, 'Name field cannot be empty');
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                array_push($errors, 'Provide valid email');
            }
            if (empty($contact_no)) {
                array_push($errors, 'Contact no field cannot be empty');
            }

            if (0 === count($errors)) {

                $data = [
                    'form_params' => $userDataArray
                ];

                $client = new Client(['http_errors' => false]);
                $resource = "http://taskbee.server/app/content/contact-us";
                $response = $client->request('POST', $resource , $data);
                $code = $response->getStatusCode();
                if(200 == $code){
                    $success=true;
                }else{
                    array_push($errors,"Some error occured. please try later.");
                    $success=false;
                }
            }
        }


        return $this->render('front/contact.html.twig', [

            "title" => 'Contact',
            "success"=>$success,
            "errors" => $errors,
            "data" => $userDataArray,
            "userProfileInfo" => $userProfileInfo
        ]);
    }

    /**
     * @Route("/abc", name="abc.welcome")
     */
    public function abc(Request $request)
    {


        $client = new Client(['http_errors' => false]);
        $baseUrl = 'http://bee-server.local';
        $tokenString = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1MzQzMDU3MzQsImV4cCI6MTUzNDMwOTMzNCwicm9sZXMiOlsiUk9MRV9VU0VSIiwiUk9MRV9UQVNLRVIiXSwidXNlcm5hbWUiOiJtX2thbXJhbnNoYWh6YWRAaG90bWFpbC5jb20iLCJ1aWQiOjMsImZpcnN0X25hbWUiOiJLYW1yYW4iLCJsYXN0X25hbWUiOiJTaGFoemFkIiwiZW1haWwiOiJtX2thbXJhbnNoYWh6YWRAaG90bWFpbC5jb20iLCJwcm9maWxlX2ltYWdlIjpudWxsfQ.gxEVUgt2aFgClUaxKePvaHrHH3jaj8HrDrYlj7qHCpHJPtDlWAsz2sutpSfvYtwUDgQxbulhA5bJy50FSjDgpuL34_Itu4PcgJhKYQcmTuI__nEKBAjC901P8OSdu9V29BX4UYx6ocyqMyk4amtqhgPX62fJTac9qM2WJaKczm1ECP_OyAF2E_TPCad3DwvTWQAJN-gUTEEI2K0JE6SSRzy0uegtBqtB14md2oVFhEDbTXaSXHD0KsIR7yX0YEqDn0g2FbEO-Ei8bRToMKm1gkb8xxMuVTq8_0UVuEEYchLL5EhO2Fw9_MQxr6WTBIY8UUsNhbCif9cySVyUy2T5YFw3qmnTahsMYg9RIwBrQr2d4jmxKU6W5KM-Ih8n6JLsRYsotkEmJWk09cSN8Ikbzc-YE7G8W5emvb3pOx0Xu3scYlAaWo2cBdR1JBn83tytbnDfBUX2kIYDqHJRMOnrBPfv5vJt3txFJdVeUas6XH8bon3-su3GYnY0nsQb9CFR0dhtuY8NfC5KWpLSx95UVCB2ZHDLACDAXPFie2t_KJZqpR-RBCz6K7EnGmfYhqysrN3fUxv1dbQvD8sm82KSH_dALuUcrk6Gvk6WtoELLibeiC12rFZp4rj0akv3FIq20EzEjZ8RUEzcrhqDNRGX2N69i5tXgP6vHVEOebw9QlA";
        $resource = $baseUrl . $request->query->get('q') ;
        $response = $client->request('GET' , $resource  , ['headers'=> ['Authorization' => "Bearer ".$tokenString ],]);

        return new JsonResponse(
            array(
                'code' => $response->getStatusCode(),
                'data' => \GuzzleHttp\json_decode($response->getBody())
            )
        );
    }



}
