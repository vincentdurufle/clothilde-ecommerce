<?php

namespace App\Controller;

use App\Entity\Item;
use App\Repository\ItemRepository;
use App\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
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
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/shop")
 */
class ShopController extends AbstractController
{
    /**
     * @var ItemRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(
        ItemRepository $repository,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Mailer $mailer
    )
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->mailer = $mailer;
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
            'billing_address_collection' => 'required',
            'shipping_address_collection' => [
                'allowed_countries' => [
                    'AT', 'BE', 'BG', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IE', 'IT',
                    'LV', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'GB'
                ],
            ],
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
                ],
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $this->translator->trans('item.shipping_fee', [], 'app')
                        ],
                        'unit_amount' => $item->getShippingFee() !== null ? $item->getShippingFee() * 100 : 0
                    ],
                    'quantity' => 1
                ]
            ],
            'metadata' => [
                'item-slug' => $item->getSlug()
            ],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('shop_item_sucess', ['slug' => $item->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('shop_index', [], UrlGeneratorInterface::ABSOLUTE_URL)
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

        function log($val)
        {
            return file_put_contents('php://stderr', print_r($val, TRUE));
        }

        $webhookSecret = $this->getParameter('stripe.webhook_secret');

        $payload = @file_get_contents('php://input');
        $sig_header = $request->headers->get('Stripe-Signature');
        $event = null;

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $webhookSecret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            throw new BadRequestHttpException('Invalid payload');
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            throw new BadRequestHttpException('Invalid signature');
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            log($session);
        }

        return new Response();
    }

    /**
     * @Route("/checkout/success/{slug}", name="shop_item_sucess")
     *
     * @return Response
     */
    public function success(string $slug): Response
    {
        $item = $this->repository->findOneBy(['slug' => $slug]);
        if ($item) {
            $item->setSold(true);

//            $this->mailer->sendEmail([
//                'to' => 'vincent.durufle@hotmail.fr',
//                'from' => $this->getParameter('contact_mail'),
//                'subject' => $this->translator->trans('mail.success.subject', [], 'app'),
//                'template' => 'shop/success_email.html.twig'
//            ]);
        }

        $this->entityManager->persist($item);
        $this->entityManager->flush();

        return $this->render('shop/shop_success.html.twig');
    }
}