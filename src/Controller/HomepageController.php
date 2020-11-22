<?php

namespace App\Controller;

use App\Entity\Item;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="homepage_index")
     *
     * @return Response
     */
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getRepository(Item::class);
        $lastItem = $entityManager->findOneBy([], ['createdAt' => 'DESC']);

        return $this->render('homepage/index.html.twig', [
            'item' => $lastItem
        ]);
    }
}