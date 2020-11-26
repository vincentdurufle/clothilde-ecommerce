<?php

namespace App\Controller;

use App\Repository\ItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/shop")
 */
class ShopController extends AbstractController
{
    /**
     * @var ItemRepository
     */
    private $repository;

    public function __construct(ItemRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/", name="shop_index")
     *
     * @return Response
     */
    public function index(): Response
    {
        $items = $this->repository->findAll();

        return $this->render('shop/index.html.twig', [
            'items' => $items
        ]);
    }

    /**
     * @Route("/item/{slug}", name="shop_item_show")
     *
     * @return Response
     */
    public function item(): Response
    {

    }
}