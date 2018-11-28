<?php
namespace App\Controller\Purchase;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Route("/purchase")
 */
class BillController  extends Controller
{
    /**
     * @Route("/bill", name="purchase.bill")
     */
    public function indexAction()
    {

    }
}