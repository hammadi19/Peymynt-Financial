<?php
namespace App\Controller\Accounting;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Route("/accounting")
 */
class ReconciliationController  extends Controller
{
    /**
     * @Route("/reconciliation", name="accounting.reconciliation")
     */
    public function indexAction()
    {

    }
}