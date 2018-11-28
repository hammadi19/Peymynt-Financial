<?php
namespace App\Controller\Accounting;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Route("/accounting")
 */
class AccountController  extends Controller
{
    /**
     * @Route("/chart-of-account", name="accounting.chart_of_account")
     */
    public function indexAction()
    {

    }
}