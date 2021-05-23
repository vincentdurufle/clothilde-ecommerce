<?php

namespace App\Controller;

use App\Entity\Collaboration;
use App\Form\CollaborationType;
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

        return $this->render('admin/index_items.html.twig', [
            'collaborations' => $collaborations
        ]);
    }

    /**
     * @Route("/admin/collaborations", name="admin_collaborations_index")
     *
     * @return Response
     */
    public function adminIndex(): Response
    {
        $repo = $this->entityManager->getRepository(Collaboration::class);
        $collaborations = $repo->findAll();

        return $this->render('admin/index_items.html.twig', [
            'collaborations' => $collaborations
        ]);
    }

    /**
     * @Route("admin/collaboration/add", name="collaboration_add")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function add(Request $request): Response
    {
        $collaboration = new Collaboration();
        $form = $this->createForm(CollaborationType::class, $collaboration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($collaboration);
            $this->entityManager->flush();

            $this->addFlash('success', 'Collaboration successfully added');

            return $this->redirectToRoute('admin_collaborations_index');
        }

        return $this->render('collaboration/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/collaboration/{slug}/edit", name="collaboration_edit")
     *
     * @param Request $request
     * @param string $slug
     *
     * @return Response
     */
    public function edit(Request $request, string $slug): Response
    {
        $repo = $this->entityManager->getRepository(Collaboration::class);

        if (null === $collaboration = $repo->findOneBy(['slug' => $slug])) {
            $this->createNotFoundException('Collaboration not found');
        }

        $form = $this->createForm(CollaborationType::class, $collaboration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($collaboration);
            $this->entityManager->flush();

            $this->addFlash('success', 'Collaboration successfully edited');

            return $this->redirectToRoute('admin_collaborations_index');
        }

        return $this->render('admin/add_item.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/collaboration/{slug}/delete", name="collaboration_delete")
     *
     * @param string $slug
     *
     * @return Response
     */
    public function delete(string $slug): Response
    {
        $repo = $this->entityManager->getRepository(Collaboration::class);

        if (null === $collaboration = $repo->findOneBy(['slug' => $slug])) {
            $this->createNotFoundException('Item not found');
        }

        $this->entityManager->remove($collaboration);
        $this->entityManager->flush();

        $this->addFlash('success', 'Collaboration successfully deleted');

        return $this->redirectToRoute('admin_collaborations_index');
    }
}