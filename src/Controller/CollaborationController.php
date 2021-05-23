<?php

namespace App\Controller;

use App\Entity\Collaboration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CollaborationController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/collaborations", name="collaboration_index")
     *
     * @return Response
     */
    public function index(): Response
    {
        $repo = $this->entityManager->getRepository(Collaboration::class);
        $collaborations = $repo->findBy(['disabled' => false]);

        return $this->render('collaboration/collaboration_index.html.twig', [
            'collaborations' => $collaborations
        ]);
    }
}