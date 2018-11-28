<?php
namespace App\Controller\Sales;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


/**
 * @Route("/sales/invoice")
 */
class InvoiceController  extends Controller
{
    /**
     * @Route("/manage", name="sales.invoice.manage")
     * @Template();
     */
    public function manageAction()
    {

    }

    /**
     * @Route("/new", name="sales.invoice.new")
     * @Template();
     */
    public function newAction()
    {

    }

}