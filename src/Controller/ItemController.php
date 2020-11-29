<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\ItemType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/item")
 */
class ItemController extends AbstractController
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
     * @Route("/", name="items_index")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(): Response
    {
        //TODO add pagination
        $repo = $this->entityManager->getRepository(Item::class);
        $items = $repo->findAll();

        return $this->render('admin/index_items.html.twig', [
            'items' => $items
        ]);
    }

    /**
     * @Route("/add", name="items_add")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function add(Request $request): Response
    {
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             $this->entityManager->persist($item);
             $this->entityManager->flush();

             $this->addFlash('success', 'Item successfully added');

            return $this->redirectToRoute('items_index');
        }

        return $this->render('admin/add_item.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="item_edit")
     *
     * @param Request $request
     * @param string $slug
     *
     * @return Response
     */
    public function edit(Request $request, string $slug): Response
    {
        $repo = $this->entityManager->getRepository(Item::class);

        if (null === $item = $repo->findOneBy(['slug' => $slug])) {
            $this->createNotFoundException('Item not found');
        }

        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($item);
            $this->entityManager->flush();

            $this->addFlash('success', 'Item successfully edited');

            return $this->redirectToRoute('items_index');
        }

        return $this->render('admin/add_item.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{slug}/delete", name="item_delete")
     *
     * @param string $slug
     *
     * @return Response
     */
    public function delete(string $slug): Response
    {
        $repo = $this->entityManager->getRepository(Item::class);

        if (null === $item = $repo->findOneBy(['slug' => $slug])) {
            $this->createNotFoundException('Item not found');
        }

        $this->entityManager->remove($item);
        $this->entityManager->flush();

        $this->addFlash('success', 'Item successfully deleted');

        return $this->redirectToRoute('items_index');
    }
}