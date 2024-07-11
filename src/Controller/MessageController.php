<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Patients;
use App\Entity\Kinesitherapeutes;
use App\Entity\Messages;

class MessageController extends AbstractController
{
   

    /**
     * @Route("/messages/envoyer", name="messages_envoyer", methods={"POST"})
     */
    public function envoyerMessage(Request $request): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();

        $requestData = json_decode($request->getContent(), true);

        // Récupérer les données envoyées via le formulaire
        $message = $requestData['message'];
        $kineId = $requestData['kine_id'];
        $patientId = $requestData['patient_id'];
        $role = $requestData['role'];
       

        // Récupérer les entités Kinesitherapeutes et Patients correspondantes
        $kine = $entityManager->getRepository(Kinesitherapeutes::class)->find($kineId);
        $patient = $entityManager->getRepository(Patients::class)->find($patientId);

        // Créer un nouvel objet Messages
        $nouveauMessage = new Messages();
        $nouveauMessage->setMessage($message);
        $nouveauMessage->setKine($kine);
        $nouveauMessage->setPatient($patient);
        $nouveauMessage->setRole($role);

        // Enregistrer l'objet dans la base de données
        $entityManager->persist($nouveauMessage);
        $entityManager->flush();

        // Rediriger vers une page ou renvoyer une réponse JSON
        return $this->json([
            'message' => 'Le message a été envoyé avec succès.',
            'path' => 'src/Controller/MessagesController.php',
        ]);
    }


     /**
     * @Route("/conversation/{kineId}/{patientId}", name="conversation", methods={"GET"})
     */
    public function getConversation($kineId, $patientId): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();

        // Récupérer tous les messages échangés entre le kinésithérapeute et le patient
        $messages = $entityManager->getRepository(Messages::class)->findBy([
            'kine' => $kineId,
            'patient' => $patientId
        ]);

        // Construire un tableau contenant les messages avec le rôle
        $conversation = [];
        foreach ($messages as $message) {
            $conversation[] = [
                'id' => $message->getId(),
                'message' => $message->getMessage(),
                'timestamp' => $message->getTimestamp(),
                'role' => $message->getRole()
            ];
        }

        // Retourner la réponse JSON contenant la conversation
        return $this->json($conversation);
    }
}
