<?php

namespace App\Controller;

use App\Entity\Item;
use App\Repository\ItemRepository;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
     * @param string $slug
     *
     * @return Response
     */
    public function item(string $slug): Response
    {
        $item = $this->repository->findOneBy(['slug' => $slug]);

        return $this->render('shop/item_show.html.twig', [
            'item' => $item
        ]);
    }

    /**
     * @Route("/create-checkout-session/{slug}", name="checkout_session", methods={"GET", "POST"})
     *
     * @param string $slug
     *
     * @return JsonResponse
     * @throws ApiErrorException
     */
    public function checkout(string $slug): JsonResponse
    {
        /** @var Item $item */
        $item = $this->repository->findOneBy(['slug' => $slug]);

        Stripe::setApiKey($this->getParameter('stripe.api_key'));
        $session = Session::create([
           'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $item->getName()
                        ],
                        'unit_amount' => $item->getPrice() * 100
                    ],
                    'quantity' => 1
                ]
            ],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('shop_item_sucess', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('shop_item_failure', [], UrlGeneratorInterface::ABSOLUTE_URL)
        ]);

        $response = [
            'id' => $session->id
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * @Route("/checkout/completed", name="checkout_webhook")
     *
     * @param Request $request
     */
    public function checkoutWebhook(Request $request)
    {
        Stripe::setApiKey($this->getParameter('stripe.api_key'));

        function log($val) {
            return file_put_contents('php://stderr', print_r($val, TRUE));
        }
        $webhookSecret = 'whsec_aBphZyB46Z41mX0pM6qj1Ri9fCPp6ALq';

        $payload = @file_get_contents('php://input');
        $sig_header = $request->headers->get('HTTP_STRIPE_SIGNATURE');
        $event = null;

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $webhookSecret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            throw new BadRequestHttpException('Invalid payload');
        } catch(SignatureVerificationException $e) {
            // Invalid signature
            throw new BadRequestHttpException('Invalid signature');
        }
//        https://stripe.com/docs/payments/checkout/fulfill-orders

        log($payload);

        return new Response();
    }

    /**
     * @Route("/checkout/success", name="shop_item_sucess")
     *
     * @return Response
     */
    public function success(): Response
    {
        return $this->render('shop/shop_success.html.twig');
    }

    /**
     * @Route("/checkout/failure", name="shop_item_failure")
     *
     * @return Response
     */
    public function failure(): Response
    {
        return $this->render('shop/shop_success.html.twig');
    }
}