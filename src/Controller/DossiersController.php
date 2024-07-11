<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\DossiersMedicaux;
use App\Entity\Patients;
use App\Entity\Kinesitherapeutes;

class DossiersController extends AbstractController
{
    

    /**
     * @Route("/ajoutdoc", name="dossiers_medicaux_create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $entityManager = $this->getDoctrine()->getManager();

        $dossierMedical = new DossiersMedicaux();
        $dossierMedical->setDateCreation(new \DateTime($data['creation']));
        $dossierMedical->setDocument($data['document']);

        $patient = $entityManager->getRepository(Patients::class)->find($data['patient_id']);        
        $dossierMedical->setPatient($patient);

        $kine = $entityManager->getRepository(Kinesitherapeutes::class)->find($data['kine_id']);        
        $dossierMedical->setKine($kine);

        $entityManager->persist($dossierMedical);
        $entityManager->flush();

        return $this->json([
            'message' => 'Le dossier médical a été créé avec succès !',
            
        ], 201);
    }
}
