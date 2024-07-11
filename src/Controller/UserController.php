<?php

namespace App\Controller;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="app_user")
     */
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

   /**
 * @Route("/api/kine/{id}/emp", name="get_kine_emploi", methods={"GET"})
 */
public function getKineEmploi($id)
{
    // Récupérer le kiné à partir de son ID
    $kine = $this->getDoctrine()->getRepository(Kinesitherapeutes::class)->find($id);

    if (!$kine) {
        // Gérer le cas où le kiné n'est pas trouvé
        return new JsonResponse(['error' => 'Kiné not found.'], JsonResponse::HTTP_NOT_FOUND);
    }

    // Récupérer l'emploi du kiné
    $emploi = $this->getDoctrine()->getRepository(Emploi::class)->findOneBy(['kinesitherapeutes' => $id]);

    // Créer un tableau de 15 dates à partir de la date actuelle
    $dates = [];
    $currentDate = new \DateTime();
    for ($i = 0; $i < 15; $i++) {
        $dates[] = $currentDate->format('Y-m-d');
        $currentDate->add(new \DateInterval('P1D')); // Ajouter 1 jour à la date actuelle
    }

    // Assigner les horaires correspondants selon le jour de chaque date
    $emploiArray = [];
    foreach ($dates as $date) {
        $jourSemaine = (new \DateTime($date))->format('N'); // Jour de la semaine (1 = lundi, 2 = mardi, etc.)

        // Récupérer les horaires de travail du kiné pour ce jour de la semaine
        $horairesTravail = [];
        switch ($jourSemaine) {
            case 1: // Lundi
                $horairesTravail = explode(';', $emploi->getLundi());
                break;
            case 2: // Mardi
                $horairesTravail = explode(';', $emploi->getMardi());
                break;
            case 3: // Mercredi
                $horairesTravail = explode(';', $emploi->getMercredi());
                break;
            case 4: // Jeudi
                $horairesTravail = explode(';', $emploi->getJeudi());
                break;
            case 5: // Vendredi
                $horairesTravail = explode(';', $emploi->getVendredi());
                break;
            case 6: // Samedi
                $horairesTravail = explode(';', $emploi->getSamedi());
                break;
            case 7: // Dimanche
                $horairesTravail = explode(';', $emploi->getDimanche());
                break;
        }

        // Récupérer tous les rendez-vous pour cette date et ce kiné
        $rendezVous = $this->getDoctrine()->getRepository(RendezVous::class)->findBy([
            'kine' => $kine,
            'dateRendezVous' => $date,
        ]);

        // Filtrer les horaires qui ne sont pas déjà réservés
        $horairesDisponibles = [];
        foreach ($horairesTravail as $horaire) {
            $horaireOccupe = false;
            foreach ($rendezVous as $rv) {
                if ($rv->getHoraire() === $horaire) {
                    $horaireOccupe = true;
                    break; // L'horaire est déjà réservé, pas besoin de vérifier les autres rendez-vous
                }
            }
            if (!$horaireOccupe) {
                $horairesDisponibles[] = $horaire;
            }
        }

        // Ajouter la date et les horaires disponibles au tableau de l'emploi
        $emploiArray[] = [
            'date' => $date,
            'horaires' => $horairesDisponibles,
        ];
    }

    // Retourner l'emploi du kiné sous forme de réponse JSON
    return new JsonResponse($emploiArray);
}

   /**
 * @Route("/{id}/editrend", name="edi_rendez_vous", methods={"POST"})
 */
public function EditRendezVous($id, Request $request, EntityManagerInterface $entityManager,MailerInterface $mailer): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    // Vérifiez si les données 'date' et 'horaire' sont présentes dans la requête
   

        // Recherchez les rendez-vous du kiné avec l'id $id
        $rendezVous = $this->getDoctrine()->getRepository(RendezVous::class)->find($id);

        // Parcourez les rendez-vous pour trouver celui correspondant à la date et à l'heure
        

        $rendezVous->setDateRendezVous($data['datenew']);
        $rendezVous->setHoraire($data['horairenew']);
        $rendezVous->setStatut('reported');
        $entityManager->persist($rendezVous);
            $entityManager->flush();
  
           
       
   
    return new JsonResponse("rendez vous modifié avec succées");
}

 /**
 * @Route("/editstat", name="edi_stat", methods={"POST"})
 */
public function EditStat(Request $request, EntityManagerInterface $entityManager): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    // Vérifiez si les données 'date' et 'horaire' sont présentes dans la requête
    
        $id = $data['id'];
        $stt=$data['statut'];

        // Recherchez les rendez-vous du kiné avec l'id $id
        $rendezVous = $this->getDoctrine()->getRepository(RendezVous::class)->find(['id' => $id]);

        // Parcourez les rendez-vous pour trouver celui correspondant à la date et à l'heure

        
        $rendezVous->setStatut($stt);
        $entityManager->persist($rendezVous);
            $entityManager->flush();

       
    
    return new JsonResponse("rendez vous confirmé");
}


    /**
     * @Route("/insertdocument", name="dossiers_medicaux_insert", methods={"POST"})
     */
    public function insertDossierMedical(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérez les données du formulaire JSON envoyé dans la requête
        $data = json_decode($request->getContent(), true);
        $id=$data['rdvId'];
        $rendezVous = $this->getDoctrine()->getRepository(RendezVous::class)->find($id);
        $imageBlob = $data['document'];

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

        // Créez une nouvelle instance de l'entité DossiersMedicaux
        $dossierMedical = new DossiersMedicaux();
        $dossierMedical->setRdv($rendezVous);
        $dossierMedical->setDocument($fileName);
        $dossierMedical->settype($data['type']);
        $entityManager->persist($dossierMedical);
        $entityManager->flush();

        // Répondez avec une réponse JSON ou toute autre réponse appropriée
        return $this->json(['message' => 'Dossier médical inséré avec succès'], Response::HTTP_CREATED);
    }

   
    /**
     * @Route("/api/dossiers-medicaux-by-rendez-vous", name="getDossiersMedicauxByRendezVous", methods={"POST"})
     */
    public function getDossiersMedicauxByRendezVous(Request $request, EntityManagerInterface $entityManager)
    {
        // Récupérez la liste des ID des rendez-vous depuis la requête POST
        $data = json_decode($request->getContent(), true);
        //$rendezVousIds = $data['rendezvous'];
        $id = $data['id'];
        $patient = $this->getDoctrine()->getRepository(Patients::class)->findOneBy(['id' => $id]);

        $rendezVousIds = $this->getDoctrine()->getRepository(RendezVous::class)->findBy(['patient' => $patient]);


        // Utilisez Doctrine pour récupérer les dossiers médicaux correspondants aux rendez-vous
        $dossiersMedicaux = $entityManager
            ->getRepository(DossiersMedicaux::class)
            ->findBy(['rdv' => $rendezVousIds]);

        // Transformez les dossiers médicaux en tableau associatif pour la réponse JSON
        $dossiersMedicauxArray = [];
        foreach ($dossiersMedicaux as $dossierMedical) {
            $dossiersMedicauxArray[] = [
                'id' => $dossierMedical->getId(),
                'date_creation' => $dossierMedical->getCreatedAt()->format('Y-m-d'),
                'document' => $dossierMedical->getDocument(),
                'type' => $dossierMedical->getType(),
                // Ajoutez d'autres champs au besoin
            ];
        }

        return new JsonResponse($dossiersMedicauxArray);
    }

      /**
     * @Route("/api/document", name="getDocument", methods={"POST"})
     */
    public function getDocuments(Request $request, EntityManagerInterface $entityManager)
    {
        // Récupérez la liste des ID des rendez-vous depuis la requête POST
        $data = json_decode($request->getContent(), true);
        //$rendezVousIds = $data['rendezvous'];
        $id = $data['id'];
    

        $rendezVousIds = $this->getDoctrine()->getRepository(RendezVous::class)->find(['id' => $id]);


        // Utilisez Doctrine pour récupérer les dossiers médicaux correspondants aux rendez-vous
        $dossiersMedicaux = $entityManager
            ->getRepository(DossiersMedicaux::class)
            ->findBy(['rdv' => $rendezVousIds]);

        // Transformez les dossiers médicaux en tableau associatif pour la réponse JSON
        $dossiersMedicauxArray = [];
        foreach ($dossiersMedicaux as $dossierMedical) {
            $dossiersMedicauxArray[] = [
                'id' => $dossierMedical->getId(),
                'date_creation' => $dossierMedical->getCreatedAt()->format('Y-m-d'),
                'document' => $dossierMedical->getDocument(),
                'type' => $dossierMedical->getType(),
                // Ajoutez d'autres champs au besoin
            ];
        }

        return new JsonResponse($dossiersMedicauxArray);
    }

    
  /**
     * @Route("/api/programmes/{id}", name="programme_update_description", methods={"PUT"})
     */
    public function updateDescription(Request $request, $id,EntityManagerInterface $entityManager)
    {
        // Récupérez le programme à mettre à jour par son ID
        $programme = $this->getDoctrine()->getRepository(Programme::class)->find($id);
        $data = json_decode($request->getContent(), true);
        $imageBlob = $data['description'];
        $temps=$data['temps'];
            $nbs=$data['nbSeance'];

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


        if (!$programme) {
            return new JsonResponse(['message' => 'Programme non trouvé'], 404);
        }

        // Récupérez la nouvelle description à partir de la requête

        // Mettez à jour la description du programme
        if($imageBlob){
        $programme->setDescription($fileName);
         }
         if($nbs){
        $programme->setNbseance($nbs);
         }
        if($temps){
           $programme->setTemps($temps);}

        // Enregistrez les modifications dans la base de données
        $entityManager->flush();

        return new JsonResponse(['message' => 'Description mise à jour avec succès']);
    }

/**
 * @Route("/api/patients/{kineId}", name="get_patients_by_kine", methods={"GET"})
 */
public function getPatientsByKine($kineId)
{
    $rendezVous = $this->getDoctrine()->getRepository(RendezVous::class)->findBy(['kine' => $kineId]);
    
    $rendezVousArray = [];
    $patientsAlreadyAdded = []; // Tableau pour garder une trace des patients déjà retournés
    
    foreach ($rendezVous as $rendezVousItem) {
        $patientId = $rendezVousItem->getPatient()->getId();
        
        // Vérifiez si ce patient a déjà été ajouté
        if (!in_array($patientId, $patientsAlreadyAdded)) {
            $reportedCount = $this->getDoctrine()
                ->getRepository(RendezVous::class)
                ->createQueryBuilder('r')
                ->select('COUNT(r.id)')
                ->where('r.kine = :kineId')
                ->andWhere('r.patient = :patientId')
                ->andWhere('r.statut = :statut')
                ->setParameter('kineId', $kineId)
                ->setParameter('patientId', $patientId)
                ->setParameter('statut', 'absent')
                ->getQuery()
                ->getSingleScalarResult();
            
            $rendezVousArray[] = [
                'statut' => $rendezVousItem->getStatut(),
                'patient' => [
                    'id' => $patientId,
                    'nom' => $rendezVousItem->getPatient()->getNom(),
                    'prenom' => $rendezVousItem->getPatient()->getPrenom(),
                    'photo' => $rendezVousItem->getPatient()->getPhoto(),
                    'adresse'=>$rendezVousItem->getPatient()->getAdresse(),
                    'tel'=>$rendezVousItem->getPatient()->getTelephone(),
                    'email'=>$rendezVousItem->getPatient()->getEmail(),

                ],
                'nombre_reported' => $reportedCount // Nombre de rendez-vous avec statut "reported"
            ];
            
            // Ajoutez ce patient au tableau des patients déjà ajoutés
            $patientsAlreadyAdded[] = $patientId;
        }
    }

    return $this->json($rendezVousArray);
}

  /**
     * @Route("/kinesitherapeutes/{id}", name="get_kinesitherapeute_by_id", methods={"GET"})
     */
    public function getKinesitherapeuteById($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $kinesitherapeute = $entityManager->getRepository(Kinesitherapeutes::class)->find($id);

        if (!$kinesitherapeute) {
            return new JsonResponse(['message' => 'Kiné non trouvé'], 404);
        }

        // Vous pouvez personnaliser les données à renvoyer ici
        $data = [
            'id' => $kinesitherapeute->getId(),
            'nom' => $kinesitherapeute->getNom(),
            'prenom' => $kinesitherapeute->getPrenom(),
            'date_naissance' => $kinesitherapeute->getDateNaissance()->format('Y-m-d'), // Format de date personnalisé
            'genre' => $kinesitherapeute->getGenre(),
            'adresse' => $kinesitherapeute->getAdresse(),
            'telephone' => $kinesitherapeute->getTelephone(),
            'piece' => $kinesitherapeute->getPiece(),
            'email' => $kinesitherapeute->getEmail(),
            'nomcabinet' => $kinesitherapeute->getNomcabinet(),
            'telcabinet' => $kinesitherapeute->getTelcabinet(),
            'adressecabinet' => $kinesitherapeute->getAdressecabinet(),
            'mailcabinet' => $kinesitherapeute->getMailcabinet(),
        ];

        return new JsonResponse($data);
    }

    /**
     * @Route("/programme/supp/{id}", name="delete_programme", methods={"DELETE"})
     */
    public function deleteProgramme($id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $programme = $entityManager->getRepository(Programme::class)->find($id);
        $Ex =$entityManager->getRepository(Exercices::class)->findBy(['programme'=>$programme]);
        if (count($Ex) === 0) {
            // Aucun élément trouvé, $Ex est un tableau vide
            echo "Aucun élément trouvé.";
        } elseif (count($Ex) === 1) {
            // Un seul élément trouvé, $Ex est un tableau avec un élément
            $exercice = $Ex[0];
            $entityManager->remove($exercice);

        } else {
            
        foreach ($Ex as $exercice) {
            $entityManager->remove($exercice);
        }}
        $entityManager->remove($programme);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Le programme a été supprimé avec succès'],200);
    }

    /**
 * @Route("/modifier-kine/{id}", name="modifier_kine", methods={"PUT"})
 */
public function modifierKine(Request $request, EntityManagerInterface $entityManager, $id): JsonResponse
{
    $kine = $entityManager->getRepository(Kinesitherapeutes::class)->find($id);
    echo $kine->getNom();
    if (!$kine) {
        return new JsonResponse(['message' => 'Kiné non trouvé'], Response::HTTP_NOT_FOUND);
    }

    $data = json_decode($request->getContent(), true);

    if (!empty($data['nom'])) {
        $kine->setNom($data['nom']);
    }

    if (isset($data['prenom']) && !empty($data['prenom'])) {
        $kine->setPrenom($data['prenom']);
    } 

    if (isset($data['date_naissance']) && !empty($data['date_naissance'])) {
        $date=substr($data['date_naissance'], 0, 10);
        $kine->setDateNaissance(new \DateTime($date));
    }

    if (isset($data['genre']) && !empty($data['genre'])) {
        $kine->setGenre($data['genre']);
    }

    if (isset($data['adresse']) && !empty($data['adresse'])) {
        $kine->setAdresse($data['adresse']);
    }

    if (isset($data['telephone']) && !empty($data['telephone'])) {
        $kine->setTelephone($data['telephone']);
    }

    if (isset($data['piece']) && !empty($data['piece'])) {
        $kine->setPiece($data['piece']);
    }

    if (isset($data['photo']) && !empty($data['photo'])) {
        $kine->setPhoto($data['photo']);
    }
    
    if (isset($data['email']) && !empty($data['email'])) {
        $kine->setEmail($data['email']);
    }
    
    if (isset($data['mdp']) && !empty($data['mdp'])) {
        // Vous devez peut-être appliquer une logique de hachage au mot de passe ici
        $kine->setMdp($data['mdp']);
    }
    
    if (isset($data['cabinetname']) && !empty($data['cabinetname'])) {
        $kine->setNomcabinet($data['cabinetname']);
    }
    
    if (isset($data['cabinetnumber']) && !empty($data['cabinetnumber'])) {
        $kine->setTelcabinet($data['cabinetnumber']);
    }
    
    if (isset($data['cabinetaddress']) && !empty($data['cabinetaddress'])) {
        $kine->setAdressecabinet($data['cabinetaddress']);
    }
    
    if (isset($data['cabinetmail']) && !empty($data['cabinetmail'])) {
        $kine->setMailcabinet($data['cabinetmail']);
    }

    // Faites de même pour les autres champs que vous souhaitez mettre à jour

    $entityManager->persist($kine);
    $entityManager->flush();

    return new JsonResponse(['message' => 'Kiné mis à jour avec succès'], Response::HTTP_OK);
}

/**
 * @Route("/send-email", name="send_email", methods={"POST"})
 */
public function sendEmail(MailerInterface $mailer): JsonResponse
{
    // Create an email message
    $email = (new Email())
        ->from('Hamzarjb7@gmail.com')
        ->to('Hamzarjiba7@gmail.com')
        ->subject('Hello, this is an email from Symfony')
        ->text('This is the plain text message content.')
        ->html('<p>This is the HTML message content.</p>');

    // Send the email
    $mailer->send($email);

    // Return a JSON response to confirm the email was sent
    return new JsonResponse(['message' => 'Email sent successfully']);
}

    /**
     * @Route("/admin/listkine", name="get_all_kinesitherapeutes", methods={"GET"})
     */
    public function getAllKinesitherapeutes()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $kinesitherapeutes = $entityManager->getRepository(Kinesitherapeutes::class)->findBy([], ['createdAt' => 'DESC']);

        $formattedKinesitherapeutes = [];
        foreach ($kinesitherapeutes as $kine) {
            $formattedKinesitherapeutes[] = [
                'id' => $kine->getId(),
                'nom' => $kine->getNom(),
                'prenom' => $kine->getPrenom(),
                'date_naissance' => $kine->getDateNaissance()->format('Y-m-d'), // Format de date personnalisé
                'photo'=>$kine->getPhoto(),
                'piece' => $kine->getPiece(),
                'createdAt' => $kine->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($formattedKinesitherapeutes);
    }

     /**
     * @Route("/admin/supp/{id}", name="get_kinesitherapeute", methods={"DELETE"})
     */
    public function getKinesitherapeute($id,EntityManagerInterface $entityManager)
    {
        $kine = $entityManager->getRepository(Kinesitherapeutes::class)->find($id);
        

        if (!$kine) {
            return new JsonResponse(['message' => 'Kinésithérapeute non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }
        $entityManager->remove($kine);
        $entityManager->flush();
        // Vous pouvez ensuite retourner le kinésithérapeute sous forme de réponse JSON
        return new JsonResponse('kiné supprimé', JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/ex/supp/{id}", name="supp_ex", methods={"DELETE"})
     */
    public function suppEx($id,EntityManagerInterface $entityManager)
    {
        $kine = $entityManager->getRepository(Exercices::class)->find($id);
        

        if (!$kine) {
            return new JsonResponse(['message' => 'excercice non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }
        $entityManager->remove($kine);
        $entityManager->flush();
        // Vous pouvez ensuite retourner le kinésithérapeute sous forme de réponse JSON
        return new JsonResponse('excercice supprimé', JsonResponse::HTTP_OK);
    }

    


}