<?php

namespace App\Controller;

use App\Entity\Collaboration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

    /**
     * @Route("/collaborations/{slug}", name="collaboration_show")
     *
     * @param string $slug
     *
     * @return Response
     */
    public function show(string $slug): Response
    {
       $collaboration = $this->entityManager->getRepository(Collaboration::class)
           ->findOneBy(['slug' => $slug, 'disabled' => false]);

       if (!$collaboration) {
           throw new NotFoundHttpException();
       }

       return $this->render('collaboration/collaboration_show.html.twig', [
           'collaboration' => $collaboration
       ]);
    }
}