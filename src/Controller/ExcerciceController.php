<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Programme;
use App\Entity\Exercices;
use App\Entity\DossiersMedicaux;

class ExcerciceController extends AbstractController
{
    

    /**
     * @Route("/exercices/ajouter", name="exercices_ajouter", methods={"POST"})
     */
    public function ajouter(Request $request,EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();

        $requestData = json_decode($request->getContent(), true);

        // Récupérer les données envoyées via le formulaire
        $nom = $requestData['nom'];
        $description = $requestData['description'];

        // Créer un nouvel objet Exercices
        $exercices = new Exercices();
        $exercices->setNom($nom);
        $exercices->setDescription($description);

        // Enregistrer l'objet dans la base de données
        $entityManager->persist($exercices);
        $entityManager->flush();

        // Rediriger vers la liste des exercices
        return $this->json([
            'message' => 'insertion effectué',
            'path' => 'src/Controller/ExcerciceController.php',
        ]);
    }

     /**
     * @Route("/programmes/ajouter", name="programmes_ajouter", methods={"POST"})
     */
    public function addProg(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();

        $requestData = json_decode($request->getContent(), true);

        // Récupérer les données envoyées via le formulaire
        $repetitions = $requestData['repetitions'];
        $series = $requestData['series'];
        $duree = $requestData['duree'];
        $exerciceId = $requestData['exercice_id'];
        $dossierMedicalId = $requestData['dossier_medical_id'];

        // Récupérer les entités Exercices et DossiersMedicaux correspondantes
        $exercice = $entityManager->getRepository(Exercices::class)->find($exerciceId);
        $dossierMedical = $entityManager->getRepository(DossiersMedicaux::class)->find($dossierMedicalId);

        // Créer un nouvel objet Programme
        $programme = new Programme();
        $programme->setRepetitions($repetitions);
        $programme->setSeries($series);
        $programme->setDuree($duree);
        $programme->setExercice($exercice);
        $programme->setDossierMedical($dossierMedical);

        // Enregistrer l'objet dans la base de données
        $entityManager->persist($programme);
        $entityManager->flush();

        // Rediriger ou retourner une réponse JSON
        return $this->json([
            'message' => 'Programme créé avec succès',
            'programme_id' => $programme->getId(),
        ]);
    }


    /**
     * @Route("/programmes/{kineId}", methods={"GET"})
     */
    public function getProgrammeByKineId(int $kineId): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $dossierRepository = $entityManager->getRepository(DossiersMedicaux::class);
        $progRepository = $entityManager->getRepository(Programme::class);
        $dossiersMedicaux = $dossierRepository->findBy(['kine' => $kineId]);
        
        // Vérifie si le kinésithérapeute a des dossiers médicaux
        if (empty($dossiersMedicaux)) {
            return new JsonResponse(['message' => 'Aucun dossier médical trouvé pour le kinésithérapeute.'], Response::HTTP_NOT_FOUND);
        }
        
        $programmesArray = [];
        
        // Parcours des dossiers médicaux pour récupérer les programmes associés
        foreach ($dossiersMedicaux as $dossierMedical) {
            $progs = $progRepository->findBy(['dossierMedical' => $dossierMedical->getId()]);
            
            // Parcours des programmes et ajout au tableau
            foreach ($progs as $programme) {
                $programmesArray[] = [
                    'id' => $programme->getId(),
                    'repetitions' => $programme->getRepetitions(),
                    'series' => $programme->getSeries(),
                    'duree' => $programme->getDuree(),
                    'createdAt' => $programme->getCreatedAt(),
                    'updatedAt' => $programme->getUpdatedAt(),
                ];
            }
        }
        
        return new JsonResponse($programmesArray, Response::HTTP_OK);
    }


    /**
     * @Route("/programmespat/{PatientId}", methods={"GET"})
     */
    public function getProgrammeByPatientId(int $PatientId): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $dossierRepository = $entityManager->getRepository(DossiersMedicaux::class);
        $progRepository = $entityManager->getRepository(Programme::class);
        $dossiersMedicaux = $dossierRepository->findBy(['patient' => $PatientId]);
        
        // Vérifie si le kinésithérapeute a des dossiers médicaux
        if (empty($dossiersMedicaux)) {
            return new JsonResponse(['message' => 'Aucun dossier médical trouvé pour le patient.'], Response::HTTP_NOT_FOUND);
        }
        
        $programmesArray = [];
        
        // Parcours des dossiers médicaux pour récupérer les programmes associés
        foreach ($dossiersMedicaux as $dossierMedical) {
            $progs = $progRepository->findBy(['dossierMedical' => $dossierMedical->getId()]);
            
            // Parcours des programmes et ajout au tableau
            foreach ($progs as $programme) {
                $programmesArray[] = [
                    'id' => $programme->getId(),
                    'repetitions' => $programme->getRepetitions(),
                    'series' => $programme->getSeries(),
                    'duree' => $programme->getDuree(),
                    'createdAt' => $programme->getCreatedAt(),
                    'updatedAt' => $programme->getUpdatedAt(),
                ];
            }
        }
        
        return new JsonResponse($programmesArray, Response::HTTP_OK);
    }

/**
 * @Route("/api/exercices", methods={"POST"})
 */
public function uploadExerciceVideo(Request $request): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    if (!isset($data['video_base64'])) {
        return new JsonResponse(['error' => 'Video base64 data not provided'], Response::HTTP_BAD_REQUEST);
    }

    $videoBase64 = $data['video_base64'];

    try {
        // Sauvegardez la vidéo encodée dans un fichier temporaire
        $tempFile = tempnam(sys_get_temp_dir(), 'exercice_video_');
        file_put_contents($tempFile, base64_decode($videoBase64));

        // Obtenez le chemin du dossier de destination dans le projet Symfony
        $destinationPath = $this->getParameter('kernel.project_dir') . '/public/uploads/exercices_videos/';

        // Générez un nom de fichier unique pour le fichier vidéo
        $filename = uniqid('exercice_video_') . '.mp4';

        // Déplacez le fichier temporaire vers le dossier de destination
        $moved = rename($tempFile, $destinationPath . $filename);

        if ($moved) {
            // Retournez le chemin du fichier enregistré à l'interface React Native
            return new JsonResponse(['file_path' => '/uploads/exercices_videos/' . $filename], Response::HTTP_OK);
        } else {
            return new JsonResponse(['error' => 'Failed to save video'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    } catch (\Exception $e) {
        return new JsonResponse(['error' => 'An error occurred while saving the video'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

    
}


