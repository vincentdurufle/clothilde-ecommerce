<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/about")
 */
class AboutController extends AbstractController
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
     * @Route("/", name="about_index")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
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

        return $this->render('about/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}