<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/item")
 */
class ItemController extends AbstractController
{
    /**
     * @Route("/", name="items_index")
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('admin/item_index.html.twig');
    }

    public function add(): Response
    {
        return $this->render('admin/add_item.html.twig');
    }
}