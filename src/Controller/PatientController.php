<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Patients;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Finder;
use App\Entity\Emploi;

class PatientController extends AbstractController
{
    

    /**
     * @Route("/register_patient", methods={"POST"})
     */
    public function registerPatient(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $imageBlob = $data['image'];

        // Vérifier si une image a été envoyée
        if ($imageBlob) {
            // Décodez l'image blob ici
            $imageData = base64_decode($imageBlob);

            // Exemple : enregistrez l'image dans un dossier temporaire sur le serveur
            $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads';
            $uploadDirectory = str_replace('\\', '/', $uploadDirectory);
            $fileName = md5(uniqid()) . '.jpg'; // Vous pouvez spécifier le type d'image approprié ici
            
            file_put_contents($uploadDirectory . '/' . $fileName, $imageData);

            
        }


        // Vérifier si l'email est déjà utilisé
        $existingUser = $this->getDoctrine()->getRepository(Patients::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(['message' => 'L\'adresse e-mail est déjà utilisée.'], Response::HTTP_BAD_REQUEST);
        }

        // Créer un nouvel utilisateur
        $user = new Patients();
        $user->setNom($data['nom']);
        $user->setPrenom($data['prenom']);
        $user->setDateNaissance(new \DateTime($data['date_naissance']));
        $user->setGenre($data['genre']);
        $user->setAdresse($data['adresse']);
        $user->setTelephone($data['telephone']);
        $user->setPhoto($fileName);
        $user->setEmail($data['email']);
        $user->setMdp($passwordEncoder->encodePassword($user, $data['mdp']));

        

        // Enregistrer l'utilisateur dans la base de données
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // Retourner une réponse de succès
        return new JsonResponse(['message' => 'Inscription réussie.'], Response::HTTP_CREATED);
    }
 /**
 * @Route("/api/login", methods={"POST"})
 */
public function login(Request $request, UserPasswordEncoderInterface $passwordEncoder, SessionInterface $session): JsonResponse
{
    $requestData = json_decode($request->getContent(), true);

    $email = $requestData['email'];
    $password = $requestData['password'];

    // Recherche du patient par email
    $patient = $this->getDoctrine()->getRepository(Patients::class)->findOneBy(['email' => $email]);

    if (!$patient) {
        // Le patient n'existe pas
        return new JsonResponse(['error' => 'Patient not found'], 404);
    }

    // Vérification du mot de passe
    $isPasswordValid = $passwordEncoder->isPasswordValid($patient, $password);
    if (!$isPasswordValid) {
        // Mot de passe incorrect
        return new JsonResponse(['error' => 'Invalid password'], 401);
    }

    // Connexion réussie, préparez les données à renvoyer
    $responseData = [
        'message' => 'Login successful',
        'patient' => [
            'id' => $patient->getId(),
            'nom' => $patient->getNom(),
            'prenom' => $patient->getPrenom(),
            // Ajoutez d'autres champs du patient que vous souhaitez inclure ici
        ],
    ];

    // Stocker les données du patient dans la session
    $session->set('userData', $responseData['patient']);

    // Renvoyer les données du patient en réponse JSON
    return new JsonResponse($responseData, 200);
}


    
}
