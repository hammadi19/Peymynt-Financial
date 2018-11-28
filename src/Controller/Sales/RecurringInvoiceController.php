<?php
namespace App\Controller\Sales;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


/**
 * @Route("/sales/recurring-invoice")
 */
class RecurringInvoiceController  extends Controller
{
    /**
     * @Route("/manage", name="sales.recurring_invoice.manage")
     * @Template();
     */
    public function manageAction()
    {

    }

    /**
     * @Route("/new", name="sales.recurring_invoice.new")
     * @Template();
     */
    public function newAction()
    {

    }

}