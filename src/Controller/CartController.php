<?php

namespace App\Controller;

use App\Repository\ItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cart")
 */
class CartController extends AbstractController
{
    const CART_SESSION = 'cart';

    /**
     * @var ItemRepository
     */
    private $repository;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(
        ItemRepository $repository,
        SessionInterface $session
    )
    {
        $this->repository = $repository;
        $this->session = $session;
    }

    /**
     * @Route("/", name="cart_index")
     *
     * @return Response
     */
    public function index(): Response
    {
        $slugs = json_decode($this->session->get(self::CART_SESSION), false);

        $items = $this->repository->findBy([
            'disabled' => false,
            'slug' => $slugs
        ]);

        return $this->render('shop/cart.html.twig', [
            'items' => $items
        ]);
    }

    /**
     * @Route("/add/{slug}", name="add_item_cart")
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function addItem(string $slug): JsonResponse
    {
        $sessionItems = $this->session->get(self::CART_SESSION);

        if ($sessionItems) {
            $items = json_decode($sessionItems, false);

            if (in_array($slug, $items, true)) {
                return new JsonResponse(null, Response::HTTP_ALREADY_REPORTED);
            }

            $items[] = $slug;
        } else {
            $items = [$slug];
        }

        $this->session->set(self::CART_SESSION, json_encode($items));

        return new JsonResponse();
    }

    /**
     * @Route("/remove/{slug}", name="remote_item_cart")
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function removeItem(string $slug): JsonResponse
    {
        $sessionItems = $this->session->get(self::CART_SESSION);

        if ($sessionItems) {
            $items = json_decode($sessionItems, false);

            if (in_array($slug, $items, true)) {
                $idx = array_search($slug, $items, true);
                if ($idx !== false) {
                    array_splice($items, $idx);
                }
            }

            $this->session->set(self::CART_SESSION, json_encode($items));
        }

        return new JsonResponse();
    }
}