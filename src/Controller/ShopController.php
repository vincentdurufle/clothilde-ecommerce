<?php

namespace App\Controller;

use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * @Route("/shop")
 */
class ShopController extends AbstractController
{
    public const COUNTRY_FR = ['FR'];
    public const COUNTRY_EU = ['AT', 'BE', 'BG', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IE', 'IT',
                    'LV', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'GB'];
    public const COUNTRY_WW = ['US', 'CA', 'KR', 'BR'];
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
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var UploaderHelper;
     */
    private $uploaderHelper;

    /**
     * @var CacheManager;
     */
    private $cacheManager;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(
        ItemRepository $repository,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        SerializerInterface $serializer,
        UploaderHelper $uploaderHelper,
        CacheManager $cacheManager,
        SessionInterface $session
    ) {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->serializer = $serializer;
        $this->uploaderHelper = $uploaderHelper;
        $this->cacheManager = $cacheManager;
        $this->session = $session;
    }

    /**
     * @Route("/", name="shop_index")
     *
     * @return Response
     */
    public function index(): Response
    {
        $items = $this->repository->findByEnabledAndOrdered();

        return $this->render('shop/index.html.twig', [
            'items' => $items
        ]);
    }

    /**
     * @Route("/category/{category}", name="show_category")
     *
     * @param string $category
     *
     * @return JsonResponse
     */
    public function category(string $category): JsonResponse
    {
        $items = $this->repository->findByTypeOrdered($category);

        $response = [];
        foreach ($items as $item) {
            $path = $this->uploaderHelper->asset($item, 'coverFile');
            $response['items'][] = $this->serializer->serialize($item, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['images']]);
            $response['covers'][] = $this->cacheManager->getBrowserPath($path, sprintf('%s', 'cover'));
        }

        return new JsonResponse($response, Response::HTTP_OK);
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
     * @Route("/create-checkout-session", name="checkout_session", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ApiErrorException
     */
    public function checkout(Request $request): JsonResponse
    {
        $slugs = $this->session->get(CartController::CART_SESSION);
        $checkoutDestination = $request->query->get('shipping', 'fr');

        if (!$slugs) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $items = $this->repository->findBy([
            'slug' => json_decode($slugs, false),
            'disabled' => false,
            'sold' => false
        ]);

        if (!$items) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $lineItems = [];
        $itemSlugs = [];

        foreach ($items as $item) {
            $itemSlugs[] = $item->getSlug();
            $shipping = $item->getOneShippingFee($checkoutDestination) * 100;
            $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $item->getName()
                        ],
                        'unit_amount' => $item->getPrice() * 100
                    ],
                    'quantity' => 1
                ];

            $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $this->translator->trans('item.shipping_fee', [], 'app')
                        ],
                        'unit_amount' => $shipping
                    ],
                    'quantity' => 1
            ];
        }

        Stripe::setApiKey($this->getParameter('stripe.api_key'));
        $session = Session::create([
            'billing_address_collection' => 'required',
            'shipping_address_collection' => [
                'allowed_countries' => $this->getAllowedCountries($checkoutDestination),
            ],
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'metadata' => [
                'item-slugs' => json_encode($itemSlugs)
            ],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('shop_item_sucess', [], UrlGeneratorInterface::ABSOLUTE_URL),
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
     * @Route("/checkout/success", name="shop_item_sucess")
     *
     * @return Response
     */
    public function success(): Response
    {
        $slugs = $this->session->get(CartController::CART_SESSION);

        $items = $this->repository->findBy([
            'slug' => json_decode($slugs, false),
            'disabled' => false,
            'sold' => false
        ]);

        if ($items) {
            foreach ($items as $item) {
                $item->setSold(true);
                $this->entityManager->persist($item);
            }

            $this->entityManager->flush();
        }

        $this->session->remove(CartController::CART_SESSION);

        return $this->render('shop/shop_success.html.twig');
    }

    private function getAllowedCountries(string $destination): array
    {
        if ($destination === 'eu') {
            return self::COUNTRY_EU;
        }
        if ($destination === 'ww') {
            return self::COUNTRY_WW;
        }

        return self::COUNTRY_FR;
    }
}