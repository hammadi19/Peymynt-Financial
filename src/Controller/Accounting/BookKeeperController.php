<?php
namespace App\Controller\Accounting;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Route("/accounting")
 */
class BookKeeperController  extends Controller
{
    /**
     * @Route("/hire-book-keeper", name="accounting.hire_book_keeper")
     */
    public function indexAction()
    {

    }
}