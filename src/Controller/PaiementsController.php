<?php

namespace App\Controller;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\RendezVous;
use App\Entity\Patients;
use App\Entity\Kinesitherapeutes;
use App\Entity\Paiements;
use Doctrine\ORM\EntityManagerInterface;

class PaiementsController extends AbstractController
{
    /**
     * @Route("/paiements", name="app_paiements",methods={"GET"}))
     */
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PaiementsController.php',
        ]);
    }

}
