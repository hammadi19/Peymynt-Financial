<?php
namespace App\Controller\Sales;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


/**
 * @Route("/sales/customer-statement")
 */
class CustomerStatementController  extends Controller
{
    /**
     * @Route("/manage", name="sales.customer_statement.manage")
     * @Template();
     */
    public function manageAction()
    {

    }

    /**
     * @Route("/new", name="sales.customer_statement.new")
     * @Template();
     */
    public function newAction()
    {

    }
}