<?php

// src/Controller/MailerController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\RendezVous;
use App\Entity\Patients;
use App\Entity\Kinesitherapeutes;
use App\Entity\Paiements;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Programme;
use App\Entity\Exercices;
use App\Entity\Emploi;
use App\Entity\DossiersMedicaux;


class MailerController extends AbstractController
{
   
/**
 * @Route("/send-email", name="send_email", methods={"POST"})
 */
    public function sendEmail(MailerInterface $mailer): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $kinesitherapeute = $entityManager->getRepository(Kinesitherapeutes::class)->find(6);


        $email = (new Email())
            ->from('hello@example.com')
            ->to('Hamzarjiba7@gmail.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('rendezvous')
            ->text($kinesitherapeute->getPassword())
            ->html('<h1>Demande de Rendez-vous</h1>
            <p>Date : 2023-10-10</p>
            <p>Horaire : 12</p>');

        $mailer->send($email);

        return  new JsonResponse(['message' => 'Email sent successfully']);
    }

    /**
 * @Route("/send-code", name="send_email_verif", methods={"POST"})
 */
public function sendmailvrif(Request $request,MailerInterface $mailer): JsonResponse
{

    
    $data = json_decode($request->getContent(), true);
    $code=$data['code'];
    $mail=$data['mail'];

    $email = (new Email())
        ->from('hello@example.com')
        ->to($mail)
        //->cc('cc@example.com')
        //->bcc('bcc@example.com')
        //->replyTo('fabien@example.com')
        //->priority(Email::PRIORITY_HIGH)
        ->subject('rendezvous')
        ->text('votre code est'.$code)
        ->html('<h1>Votre code de vérifciation est</h1>
        <p>'.$code.'</p>
        ');

    $mailer->send($email);

    return  new JsonResponse(['code' => $code,'email' => $mail]);
}

/**
 * @Route("/kinesitherapeutes/{id}/modifier-email", name="modifier_email_kine", methods={"POST"})
 */
public function modifierEmailKine(Request $request, int $id,EntityManagerInterface $entityManager): JsonResponse
{
    $entityManager = $this->getDoctrine()->getManager();
    $kine = $entityManager->getRepository(Kinesitherapeutes::class)->find($id);

    if (!$kine) {
        return new JsonResponse(['message' => 'Kiné non trouvé'], 404);
    }
    $data = json_decode($request->getContent(), true);
    $email = $data['email'];
    $code1 = $data['code1'];
    $code2 = $data['code2'];

    if ($code1 === $code2) {
        $kine->setEmail($email);
        $entityManager->flush();

        return new JsonResponse(['message' => 'E-mail modifié avec succès'], 200);
    } else {
        return new JsonResponse(['message' => 'Code de vérification invalide'], 400);
    }
}

}

