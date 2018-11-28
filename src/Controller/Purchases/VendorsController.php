<?php
declare(strict_types=1);

namespace App\Controller\Purchases;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class BillController
 * @package App\Controller\Purchases
 * @Route("/purchases/vendors")
 */
class VendorsController extends Controller
{
    /**
     * @Route("/manage", name="purchases.vendors.manage")
     * @Template();
     */
    public function manageAction()
    {
        return [

        ];
    }

    /**
     * @Route("/add", name="purchases.vendors.add")
     * @Template();
     */
    public function createAction()
    {
        return [

        ];
    }
}