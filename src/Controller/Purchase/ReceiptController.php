<?php
namespace App\Controller\Purchase;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Route("/purchase")
 */
class ReceiptController  extends Controller
{
    /**
     * @Route("/receipt", name="purchase.receipt")
     */
    public function indexAction()
    {

    }
}