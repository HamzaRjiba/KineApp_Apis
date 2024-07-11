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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;





class TestController extends AbstractController
{
    
 /**
 * @Route("/rendez-vous", name="ajouter_rendez_vous", methods={"POST"})
 */
public function ajouterRendezVous(Request $request,MailerInterface $mailer)
{
    $data = json_decode($request->getContent(), true);

    $entityManager = $this->getDoctrine()->getManager();

    $rendezVous = new RendezVous();
    $rendezVous->setDateRendezVous($data['date']);
    $rendezVous->setHoraire($data['horaire']);
    $rendezVous->setMotif($data['motif']);

    $patient = $entityManager->getRepository(Patients::class)->find($data['patient']);
    $rendezVous->setPatient($patient);

    $kin = $entityManager->getRepository(Kinesitherapeutes::class)->find($data['kine']);
    $rendezVous->setKine($kin);
    $rendezVous->setStatut('en attente');

    $entityManager->persist($rendezVous);
    $entityManager->flush();
    
    $email = (new Email())
    ->from('hello@example.com')
    ->to('Hamzarjiba7@gmail.com')
    //->cc('cc@example.com')
    //->bcc('bcc@example.com')
    //->replyTo('fabien@example.com')
    //->priority(Email::PRIORITY_HIGH)
    ->subject('rendezvous')
    ->text('hhh')
    ->html('<h1>Demande de Rendez-vous</h1>
    <p>Date : '.$data['date'].'</p>
    <p>Horaire : 12</p>');
    
    $mailer->send($email);

    // Return JSON response
    return new JsonResponse([
        'message' => 'Le rendez-vous a été ajouté avec succès!',
        'status' => JsonResponse::HTTP_CREATED
    ]);
}


    /**
     * @Route("/all", name="get_rendezvous", methods={"GET"})
     */
    public function getRSSSSRendezVous()
    {
        $rendezVousRepository = $this->getDoctrine()->getRepository(RendezVous::class);
        $rendezVous = $rendezVousRepository->findAll();

        $data = [];
        foreach ($rendezVous as $rv) {
            $data[] = [
                'id' => $rv->getId(),
                'date' => $rv->getCreatedAt()->format('Y-m-d H:i:s'),
                'patient' => $rv->getPatient()->getNom(),
                'kinesitherapeute' => $rv->getKine()->getNom(),
                // ajouter ici d'autres champs si nécessaire
            ];
        }

        $response = new Response(json_encode($data), Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


    /**
     * @Route("/api/patients/{id}/rendez-vous", methods={"GET"})
     */
    public function getRendezVousByPatient($id)
    {
        $rendezVous = $this->getDoctrine()->getRepository(RendezVous::class)->findBy(['patient' => $id]);
        $rendezVousArrayAVenir = []; // Tableau pour les rendez-vous à venir
        $rendezVousArrayRestants = []; // Tableau pour les autres rendez-vous
    
        $today = new \DateTime(); 
    $todayString = $today->format('Y-m-d');// Obtenez la date d'aujourd'hui
        
    
        foreach ($rendezVous as $rendezVousItem) {
            $rendezVousDate = $rendezVousItem->getDateRendezVous();
    
    
    
            // Comparez la date du rendez-vous avec la date d'aujourd'hui
            if ($rendezVousDate > $todayString) {
                // Rendez-vous à venir
                $rendezVousArrayAVenir[] = [
                    'id' => $rendezVousItem->getId(),
                    'date_rendez_vous' => substr($rendezVousItem->getDateRendezVous(), 0, 10),
                    'motif' => $rendezVousItem->getMotif(),
                    'remarques' => $rendezVousItem->getRemarques(),
                    'horaire' => $rendezVousItem->getHoraire(),
                    'statut' => $rendezVousItem->getStatut(),
                    'patient' => [
                        'id' => $rendezVousItem->getPatient()->getId(),
                        'nom' => $rendezVousItem->getPatient()->getNom(),
                        'prenom' => $rendezVousItem->getPatient()->getPrenom()
                    ]
                ];
            } else {
                // Autres rendez-vous
                $rendezVousArrayRestants[] = [
                    'id' => $rendezVousItem->getId(),
                    'date_rendez_vous' => substr($rendezVousItem->getDateRendezVous(), 0, 10),
                    'motif' => $rendezVousItem->getMotif(),
                    'remarques' => $rendezVousItem->getRemarques(),
                    'horaire' => $rendezVousItem->getHoraire(),
                    'statut' => $rendezVousItem->getStatut(),
                    'patient' => [
                        'id' => $rendezVousItem->getPatient()->getId(),
                        'nom' => $rendezVousItem->getPatient()->getNom(),
                        'prenom' => $rendezVousItem->getPatient()->getPrenom()
                    ]
                ];
            }
        }
    
        // Retournez les deux tableaux dans une réponse JSON
        return $this->json([
            'rendezVousAVenir' => $rendezVousArrayAVenir,
            'rendezVousRestants' => $rendezVousArrayRestants
        ]);
    }
    
    /**
 * @Route("/kine/{id}/rendez-vous", name="kine_rendez_vous", methods={"GET"})
 */
public function getRendezVous($id): JsonResponse
{

    $rendezVous = $this->getDoctrine()->getRepository(RendezVous::class)->findBy(['kine' => $id]);
    $rendezVousArrayAVenir = []; // Tableau pour les rendez-vous à venir
    $rendezVousArrayRestants = []; // Tableau pour les autres rendez-vous

    $today = new \DateTime(); 
$todayString = $today->format('Y-m-d');// Obtenez la date d'aujourd'hui
    

    foreach ($rendezVous as $rendezVousItem) {
        $rendezVousDate = $rendezVousItem->getDateRendezVous();



        // Comparez la date du rendez-vous avec la date d'aujourd'hui
        if ($rendezVousDate > $todayString) {
            // Rendez-vous à venir
            $rendezVousArrayAVenir[] = [
                'id' => $rendezVousItem->getId(),
                'date_rendez_vous' => substr($rendezVousItem->getDateRendezVous(), 0, 10),
                'motif' => $rendezVousItem->getMotif(),
                'remarques' => $rendezVousItem->getRemarques(),
                'horaire' => $rendezVousItem->getHoraire(),
                'statut' => $rendezVousItem->getStatut(),
                'patient' => [
                    'id' => $rendezVousItem->getPatient()->getId(),
                    'nom' => $rendezVousItem->getPatient()->getNom(),
                    'prenom' => $rendezVousItem->getPatient()->getPrenom(),
                    'photo' => $rendezVousItem->getPatient()->getPhoto()

                ]
            ];
        } else {
            // Autres rendez-vous
            $rendezVousArrayRestants[] = [
                'id' => $rendezVousItem->getId(),
                'date_rendez_vous' => substr($rendezVousItem->getDateRendezVous(), 0, 10),
                'motif' => $rendezVousItem->getMotif(),
                'remarques' => $rendezVousItem->getRemarques(),
                'horaire' => $rendezVousItem->getHoraire(),
                'statut' => $rendezVousItem->getStatut(),
                'patient' => [
                    'id' => $rendezVousItem->getPatient()->getId(),
                    'nom' => $rendezVousItem->getPatient()->getNom(),
                    'prenom' => $rendezVousItem->getPatient()->getPrenom(),
                    'photo' => $rendezVousItem->getPatient()->getPhoto()
                ]
            ];
        }
    }

    // Retournez les deux tableaux dans une réponse JSON
    return $this->json([
        'rendezVousAVenir' => $rendezVousArrayAVenir,
        'rendezVousRestants' => $rendezVousArrayRestants
    ]);
}



   /**
 * @Route("/paiements", name="api_create_paiement", methods={"POST"})
 */
public function createPaiement(Request $request, EntityManagerInterface $entityManager)
{
    $data = json_decode($request->getContent(), true);
    $patient = $this->getDoctrine()->getRepository(Patients::class)->find($data['clientName']);
    $kine = $this->getDoctrine()->getRepository(Kinesitherapeutes::class)->find(6);

    $paiement=new Paiements();
    $paiement->setMoyenDePaiement($data['paymentMethod']);
    $paiement->setMontant($data['amountPaid']);
    $paiement->setPatient($patient);
    $paiement->setKine($kine);
    
    $entityManager->persist( $paiement);
    $entityManager->flush();

    return new JsonResponse("paiement ajouté avec succés");

    
  
}

    /**
     * @Route("/api/patients/{id}/paiements", name="get_patient_paiements", methods={"GET"})
     */
    public function getPatientPaiements($id)
    {
         // Récupérer le patient à partir de son ID
         $kine = $this->getDoctrine()->getRepository(Patients::class)->find($id);

         if (!$kine) {
             // Gérer le cas où le patient n'est pas trouvé
             return new JsonResponse(['error' => 'Patient not found.'], JsonResponse::HTTP_NOT_FOUND);
         }
 
         // Récupérer les paiements associés au patient
         $paimentRepository = $this->getDoctrine()->getRepository(Paiements::class);
         $paiements = $paimentRepository->findBy(['patient' => $id]);
        // $paiements = $patient->getPaiements();
 
         // Convertir les paiements en tableau pour faciliter la serialization
         $paiementsArray = [];
         foreach ($paiements as $paiement) {
             $nom=$paiement->getKine()->getNom();
             $prenom=$paiement->getKine()->getPrenom();
             $paiementsArray[] = [
                 'id' => $paiement->getId(),
                 'date_paiement' => $paiement->getCreatedAt()->format('Y-m-d'),
                 'montant' => $paiement->getMontant(),
                 'moyen_de_paiement' => $paiement->getMoyenDePaiement(),
                 'patientname'=>$nom.' '.$prenom,
             ];
         }
 
         // Retourner les paiements en tant que réponse JSON
         return new JsonResponse($paiementsArray);
     }
 

    /**
     * @Route("/api/kine/{id}/paiements", name="get_kine_paiements", methods={"GET"})
     */
    public function getKinePaiements($id)
    {
        // Récupérer le patient à partir de son ID
        $kine = $this->getDoctrine()->getRepository(Kinesitherapeutes::class)->find($id);

        if (!$kine) {
            // Gérer le cas où le patient n'est pas trouvé
            return new JsonResponse(['error' => 'kine not found.'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Récupérer les paiements associés au patient
        $paimentRepository = $this->getDoctrine()->getRepository(Paiements::class);
        $paiements = $paimentRepository->findBy(['kine' => $id]);
       // $paiements = $patient->getPaiements();

        // Convertir les paiements en tableau pour faciliter la serialization
        $paiementsArray = [];
        foreach ($paiements as $paiement) {
            $nom=$paiement->getPatient()->getNom();
            $prenom=$paiement->getPatient()->getPrenom();
            $paiementsArray[] = [
                'id' => $paiement->getId(),
                'date_paiement' => $paiement->getCreatedAt()->format('Y-m-d'),
                'montant' => $paiement->getMontant(),
                'moyen_de_paiement' => $paiement->getMoyenDePaiement(),
                'patientname'=>$nom.' '.$prenom,
            ];
        }

        // Retourner les paiements en tant que réponse JSON
        return new JsonResponse($paiementsArray);
    }


    
   /**
 * @Route("/api/programAjout", name="programAjout", methods={"POST"})
 */
public function uploadImageProgram(Request $request, EntityManagerInterface $entityManager)
{
   
        $data = json_decode($request->getContent(), true);

        if (!empty($data['imageData'])) {
            $imageData = $data['imageData'];
            $programme = new Programme();
            $temps=$data['temps'];
            $nbs=$data['nbSeance'];
            $motif= $data['motif'];
            $idkine= $data['idkine'];

            // Decode the base64 image data
            $decodedImage = base64_decode($imageData);

            // Generate a unique filename
            $filename = uniqid() . '.jpg';

            // Define the path to save the image
            $uploadPath = $this->getParameter('kernel.project_dir') . '/public/uploads/';
            $uploadPath = str_replace('\\', '/', $uploadPath);
            $imagePath = $uploadPath . $filename;

            // Save the image
            file_put_contents($imagePath, $decodedImage);
            $programme->setNom($motif);
            $programme->setDescription($filename);
           $programme->setKineid($idkine);
           $programme->setNbseance($nbs);
           $programme->setTemps($temps);

           $programme->setNomprog($motif.$programme->getId());


            $entityManager->persist($programme);
            $entityManager->flush();
            
            
            $id= $programme-> getId();
            $programme = $entityManager->getRepository(Programme::class)->find($id);
            $programme->setNomprog($motif.$id);
            $entityManager->persist($programme);
            $entityManager->flush();


            // Return a response
            return new JsonResponse(['message' =>  $id], Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'Image data not provided'], Response::HTTP_BAD_REQUEST);
    }

    
  
    /**
 * @Route("/api/tr", name="upload_videos", methods={"POST"})
 */
public function tr(Request $request, EntityManagerInterface $entityManager)
{
    
    $requestData = json_decode($request->getContent(), true);

    $videoBase64 = $requestData['video_base64'];
    $videoNames = explode(';', $requestData['video_names']);
    $idprog=$requestData['idprog'];
    $idkine= $requestData['idkine'];


    $videoBase64Array = explode(';;;', $videoBase64);

    $uploadPath = $this->getParameter('kernel.project_dir') . '/public/uploads/';
   
    
    

    
        if (count($videoNames) === count($videoBase64Array)) {
            foreach ($videoNames as $index => $videoName) {
                $base64Data = $videoBase64Array[$index];

                $Nomved = uniqid() . '.mp4';
                $videoData = base64_decode($base64Data);
                $videoPath = $uploadPath .  $Nomved;
                $videoPath = str_replace('\\', '/', $videoPath);
                file_put_contents($videoPath, $videoData);
                $exercice = new Exercices();
                $exercice->setNom($videoName);
                $exercice->setRepetitions(10);
                $exercice->setSeries(3);
                $exercice->setChemin( $Nomved);
                $kine = $this->getDoctrine()->getRepository(Kinesitherapeutes::class)->find($idkine);
                $programme = $this->getDoctrine()->getRepository(Programme::class)->find($idprog);
                $exercice->setProgramme($programme);
                $exercice->setKine($kine);
            
                $entityManager->persist($exercice);
                $entityManager->flush();
                // Maintenant, vous pouvez accéder aux valeurs de $videoName et $base64Data
                
            }
       
   
       


    }

    return new JsonResponse(['message' =>  "video inséré"]);
}

/**
 * @Route("/api/kineprog/{id}", name="get_kine_programme", methods={"GET"})
 */

public function getKinesthesProgramsAndExercises(int $id,EntityManagerInterface $entityManager)
{
    

    // Récupérez les programmes associés à ce kiné
    $programmes = $entityManager->getRepository(Programme::class)->findBy(['kineid' => $id]);

    $response = [];

    foreach ($programmes as $programme) {
        $programmeData = [
            'id' => $programme->getId(),
            'nom' => $programme->getNom(),
            'nomprog' => $programme->getNomprog(),
            'description' => $programme->getDescription(),
            'created_at' => $programme->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $programme->getUpdatedAt()->format('Y-m-d H:i:s'),
            'nbs'=>$programme->getNbseance(),
            'temps'=>$programme->getTemps(),
            'exercices' => []
        ];

        // Récupérez les exercices associés à ce programme
        $exercices =$entityManager->getRepository(Exercices::class)->findBy(['programme' => $programme]);

        foreach ($exercices as $exercice) {
            $exerciceData = [
                'id' => $exercice->getId(),
                'repetitions' => $exercice->getRepetitions(),
                'series' => $exercice->getSeries(),
                'chemin' => $exercice->getChemin(),
                'created_at' => $exercice->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $exercice->getUpdatedAt()->format('Y-m-d H:i:s'),
                'nom' => $exercice->getNom()
            ];

            $programmeData['exercices'][] = $exerciceData;
        }

        $response[] = $programmeData;
    }

    return new JsonResponse($response);
}

/**
 * @Route("/api/patientprog/{id}", name="get_patient_programme", methods={"GET"})
 */
public function getPatientProgramsAndExercises(int $id, EntityManagerInterface $entityManager)
{
    // Récupérez le gestionnaire d'entités (Entity Manager)
    $entityManager = $this->getDoctrine()->getManager();

    // Recherchez le rendez-vous par son ID
    $kineIds = $entityManager->getRepository(RendezVous::class)->findBy(['patient' => $id]);

    if (!is_array($kineIds)) {
        $kineIds = [$kineIds];
    }

    $response = [];
    $importedProgramIds = [];

    foreach ($kineIds as $kineId) {
        // Récupérez les programmes associés à ce kiné
        $programmes = $entityManager->getRepository(Programme::class)->findBy(['kineid' => $kineId->getKine()->getId()]);

        foreach ($programmes as $programme) {
            $programmeId = $programme->getId();

            if (!in_array($programmeId, $importedProgramIds)) {
                // Si le programme n'a pas déjà été importé, ajoutez-le à la réponse
                $programmeData = [
                    'id' => $programmeId,
                    'nom' => $programme->getNom(),
                    'nomprog' => $programme->getNomprog(),
                    'description' => $programme->getDescription(),
                    'created_at' => $programme->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updated_at' => $programme->getUpdatedAt()->format('Y-m-d H:i:s'),
                    'nbs' => $programme->getNbseance(),
                    'temps' => $programme->getTemps(),
                    'exercices' => []
                ];

                // Récupérez les exercices associés à ce programme
                $exercices = $entityManager->getRepository(Exercices::class)->findBy(['programme' => $programme]);

                foreach ($exercices as $exercice) {
                    $exerciceData = [
                        'id' => $exercice->getId(),
                        'repetitions' => $exercice->getRepetitions(),
                        'series' => $exercice->getSeries(),
                        'chemin' => $exercice->getChemin(),
                        'created_at' => $exercice->getCreatedAt()->format('Y-m-d H:i:s'),
                        'updated_at' => $exercice->getUpdatedAt()->format('Y-m-d H:i:s'),
                        'nom' => $exercice->getNom()
                    ];

                    $programmeData['exercices'][] = $exerciceData;
                }

                // Ajoutez les données du programme à la réponse
                $response[] = $programmeData;

                // Ajoutez l'ID du programme au tableau des ID importés
                $importedProgramIds[] = $programmeId;
            }
        }
    }

    return new JsonResponse($response);
}



 
/**
 * @Route("/api/nompatients/{id}", name="get_kine_pat", methods={"GET"})
 */
public function getKinestheesProgramsAndExercises(int $id, EntityManagerInterface $entityManager)
{
    $kine = $this->getDoctrine()->getRepository(Kinesitherapeutes::class)->find($id);

    // Récupérez les programmes associés à ce kiné
    $rdvs = $entityManager->getRepository(RendezVous::class)->findBy(['kine' => $kine]);
    
    $uniquePatientIds = [];
    $results = [];

    foreach ($rdvs as $rdv) {
        $patientId = $rdv->getPatient()->getId();
        $nom = $rdv->getPatient()->getNom();
        $prenom = $rdv->getPatient()->getPrenom();
        
        // Check if the patient ID is unique
        if (!in_array($patientId, $uniquePatientIds)) {
            $uniquePatientIds[] = $patientId; // Add the ID to the unique IDs array
            $results[] = [
                'id' => $patientId,
                'nom' => $nom,
                'prenom' => $prenom,
            ];
        }
    }

    return new JsonResponse($results);
}


}