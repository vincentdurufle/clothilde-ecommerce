<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\ContactType;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @Route("/", name="homepage_index")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getRepository(Item::class);
        $lastItems = $entityManager->findBy(['disabled' => false, 'sold' => false], ['createdAt' => 'DESC'], 3);

        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $contact = $this->getParameter('contact_mail');
            $params = [
                'from' => $contact,
                'to' => $contact,
                'replyTo' => $data['email'],
                'subject' => 'Prise de contact',
                'template' => 'about/contact_mail.html.twig',
                'params' => [
                    'name' => $data['name'],
                    'content' => $data['content'],
                    'userEmail' => $data['email']
                ]
            ];
            $this->mailer->sendEmail($params);

            return $this->redirectToRoute('homepage_index');
        }


        return $this->render('homepage/index.html.twig', [
            'items' => $lastItems,
            'contactForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/legals", name="legals_show")
     *
     * @return Response
     */
    public function legals(): Response
    {
        return $this->render('homepage/conditions.html.twig');
    }
}