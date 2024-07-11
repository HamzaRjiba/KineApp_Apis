<?php

namespace App\Controller;

use App\Entity\Kinesitherapeutes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Finder\Finder;
use App\Entity\Emploi;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Patients;
use App\Entity\RendezVous;
use App\Entity\Admin;

class KineController extends AbstractController
{
    /**
     * @Route("/api/kinesitherapeutes/register", name="kinesitherapeutes_register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        // Récupérer les données du formulaire d'inscription
        $data = json_decode($request->getContent(), true);
        $imageBlob = $data['image'];
        $imageBlob2 = $data['photo'];

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
         // Vérifier si une image a été envoyée
         if ($imageBlob2) {
            // Décodez l'image blob ici
            $imageData2 = base64_decode($imageBlob2);

            // Exemple : enregistrez l'image dans un dossier temporaire sur le serveur
            $uploadDirectory2 = $this->getParameter('kernel.project_dir') . '/public/uploads';
            $uploadDirectory2 = str_replace('\\', '/', $uploadDirectory);
            $fileName2 = md5(uniqid()) . '.jpg'; // Vous pouvez spécifier le type d'image approprié ici
            
            file_put_contents($uploadDirectory2 . '/' . $fileName2, $imageData2);

            
        }


        // Vérifier si l'email est déjà utilisé
        $existingUser = $this->getDoctrine()->getRepository(Kinesitherapeutes::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(['message' => 'L\'adresse e-mail est déjà utilisée.'], Response::HTTP_BAD_REQUEST);
        }

        // Créer un nouvel utilisateur
        $user = new Kinesitherapeutes();
        $user->setNom($data['nom']);
        $user->setPrenom($data['prenom']);
        $user->setDateNaissance(new \DateTime($data['date_naissance']));
        $user->setGenre($data['genre']);
        $user->setAdresse($data['cabinetaddress']);
        $user->setTelephone($data['telephone']);
        $user->setPiece($fileName2);
        $user->setEmail($data['email']);
        $user->setMdp($passwordEncoder->encodePassword($user, $data['mdp']));
        $user->setNomcabinet($data['cabinetname']);
        $user->setTelcabinet($data['cabinetnumber']);
        $user->setAdressecabinet($data['cabinetaddress']);
        $user->setMailcabinet($data['cabinetmail']);
        $user->setPhoto($fileName);
       



        

        // Enregistrer l'utilisateur dans la base de données
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // Retourner une réponse de succès
        return new JsonResponse(['message' => 'Inscription réussie.'], Response::HTTP_CREATED);
    }

   

   /**
 * @Route("/api/upload", name="upload_image", methods={"POST"})
 */
public function uploadImage(Request $request, EntityManagerInterface $entityManager)
{
    $data = json_decode($request->getContent(), true);
    $imageBlob = $data['image'];
    $kineId = $data['kineId']; // L'ID du kiné envoyé depuis l'interface React Native

    // Vérifier si une image a été envoyée
    if ($imageBlob && $kineId) {
        // Décodez l'image blob ici
        $imageData = base64_decode($imageBlob);

        // Exemple : enregistrez l'image dans un dossier temporaire sur le serveur
        $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads';
        $uploadDirectory = str_replace('\\', '/', $uploadDirectory);
        $fileName = md5(uniqid()) . '.jpg'; // Vous pouvez spécifier le type d'image approprié ici
        
        file_put_contents($uploadDirectory . '/' . $fileName, $imageData);

        // Récupérer l'entité Kinesitherapeutes associée à l'ID reçu
        $kinetherapeute = $entityManager->getRepository(Kinesitherapeutes::class)->find($kineId);

        if ($kinetherapeute) {
            // Mettre à jour le champ 'photo' de l'entité Kinesitherapeutes
            $kinetherapeute->setPhoto($uploadDirectory.'/'. $fileName);

            $entityManager->persist($kinetherapeute);
            $entityManager->flush();

            return new JsonResponse(['message' => 'Image uploaded successfully']);
        } else {
            return new JsonResponse(['message' => 'Kinesitherapeute not found'], 404);
        }
    }

    return new JsonResponse(['message' => 'No image uploaded'], 400);
}



  


    /**
     * @Route("/api/search", methods={"POST"})
     */
    public function searchKinesitherapeutes(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $searchText = $data['searchText'];

        $entityManager = $this->getDoctrine()->getManager();

        // Effectuer la recherche dans votre base de données
        $query = $entityManager->createQueryBuilder()
            ->select('k.nom', 'k.prenom', 'k.piece','k.photo')
            ->from(Kinesitherapeutes::class, 'k')
            ->where('k.nom LIKE :searchText')
            ->orWhere('k.prenom LIKE :searchText')
            ->setParameter('searchText', '%'.$searchText.'%')
            ->getQuery();

        $kinesitherapeutes = $query->getResult();

        foreach ($kinesitherapeutes as &$kine) {
            if (empty($kine['photo'])) {
                $kine['photo'] = 'C:/Users/GAMING/Documents/FirstProject/assets/log.png';
            }
        }
        // Retourner les résultats sous forme de réponse JSON
        return new JsonResponse($kinesitherapeutes);
    }


 
/**
 * @Route("/api/loginKine", methods={"POST"})
 */
public function login(Request $request, UserPasswordEncoderInterface $passwordEncoder): JsonResponse
{
    $requestData = json_decode($request->getContent(), true);

    $email = $requestData['email'];
    $password = $requestData['password'];

    // Recherche du Kinesitherapeutes par email
    $Kinesitherapeutes = $this->getDoctrine()->getRepository(Kinesitherapeutes::class)->findOneBy(['email' => $email]);
    $Patients = $this->getDoctrine()->getRepository(Patients::class)->findOneBy(['email' => $email]);
    $admin=$this->getDoctrine()->getRepository(Admin::class)->findOneBy(['email' => $email]);

    

    if (!$Kinesitherapeutes && !$Patients && !$admin) {
        // Le Kinesitherapeutes n'existe pas
        return new JsonResponse(['error' => 'user not found'], 404);
    }

    if($Kinesitherapeutes){
    $isPasswordValid = $passwordEncoder->isPasswordValid($Kinesitherapeutes, $password);
    if (!$isPasswordValid) {
        // Mot de passe incorrect
        return new JsonResponse(['error' => 'Invalid password'], 401);
    }}
    if($Patients){
        $isPasswordValid = $passwordEncoder->isPasswordValid($Patients, $password);
        if (!$isPasswordValid) {
            // Mot de passe incorrect
            return new JsonResponse(['error' => 'Invalid password'], 401);
        }}

    if($admin){
        if($admin->getMdp()!==$password){
            return new JsonResponse(['error' => 'Invalid password'], 401);
        }
    }

    // Connexion réussie, préparez les données à renvoyer
    if($Patients){
    $responseData = [
        'message' => 'Login successful',
        'patients' => [
            'id' => $Patients->getId(),
            'nom' => $Patients->getNom(),
            'prenom' => $Patients->getPrenom(),
            
        ],
    ];}
    else if ( $Kinesitherapeutes) {
        $responseData = [
            'message' => 'Login successful',
            'Kinesitherapeutes' => [
                'id' => $Kinesitherapeutes->getId(),
                'nom' => $Kinesitherapeutes->getNom(),
                'prenom' => $Kinesitherapeutes->getPrenom(),
                
                
            ],
        ];  
    }
    if($admin){
        $responseData = [
            'message' => 'Login successful',
            'admin' => [
                'id' => $admin->getId(),
                'email' => $admin->getEmail(),
                
            ],
        ];}

    // Stocker les données du Kinesitherapeutes dans la session
   

    // Renvoyer les données du Kinesitherapeutes en réponse JSON
    return new JsonResponse($responseData, 200);
}


   

    /**
 * @Route("/uploads/{filename}", name="uploads")
 */
public function serveUploadedFiles($filename)
{
    $path = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $filename;
    $file = new File($path);

    return new BinaryFileResponse($file);

}

  /**
     * @Route("/api/send-data", name="send_data", methods={"POST"})
     */
    public function sendDataAction(Request $request): JsonResponse
    {
        // Récupérer les données du corps de la requête (nom et prénom)
        $data = json_decode($request->getContent(), true);

        $nom = $data['nom'] ;
        $prenom = $data['prenom'] ;
        $photo = $data['photo'] ;

     // Recherchez le kinésithérapeute en utilisant le nom de la photo
     $kinesitherapeute = $this->getDoctrine()->getRepository(Kinesitherapeutes::class)->findOneBy(['photo' => $photo]);

     if (!$kinesitherapeute) {
         return $this->json(['message' => 'Kinesitherapeute not found'], Response::HTTP_NOT_FOUND);
     }

     // Renvoyez les données du kinésithérapeute
     return $this->json([
         'kinesitherapeute' => [
             'id' => $kinesitherapeute->getId(),
             'nom' => $kinesitherapeute->getNom(),
             'prenom' => $kinesitherapeute->getPrenom(),
             'dateNaissance' => $kinesitherapeute->getDateNaissance(),
             'genre' => $kinesitherapeute->getGenre(),
             'adresse' => $kinesitherapeute->getAdresse(),
             'telephone' => $kinesitherapeute->getTelephone(),
             'piece' => $kinesitherapeute->getPiece(),
             'email' => $kinesitherapeute->getEmail(),
             'photo' => $kinesitherapeute->getPhoto(),
         ],
     ]);
        return $this->json($responseData);
    }

    /**
     * @Route("/api/kinesitherapeutes/info", name="api_kinesitherapeute_by_photo", methods={"POST"})
     */
    public function getByPhoto(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Assurez-vous que le nom de la photo est présent dans les données
        if (!isset($data['photoName'])) {
            return $this->json(['message' => 'Photo name not provided'], Response::HTTP_BAD_REQUEST);
        }

        // Récupérez le nom de la photo depuis les données
        $photoName = $data['photoName'];

        // Recherchez le kinésithérapeute en utilisant le nom de la photo
        $kinesitherapeute = $this->getDoctrine()->getRepository(Kinesitherapeutes::class)->findOneBy(['photo' => $photoName]);

        if (!$kinesitherapeute) {
            return $this->json(['message' => 'Kinesitherapeute not found'], Response::HTTP_NOT_FOUND);
        }

        // Renvoyez les données du kinésithérapeute
        return $this->json([
            'kinesitherapeute' => [
                'id' => $kinesitherapeute->getId(),
                'nom' => $kinesitherapeute->getNom(),
                'prenom' => $kinesitherapeute->getPrenom(),
                'dateNaissance' => $kinesitherapeute->getDateNaissance(),
                'genre' => $kinesitherapeute->getGenre(),
                'adresse' => $kinesitherapeute->getAdresse(),
                'telephone' => $kinesitherapeute->getTelephone(),
                'piece' => $kinesitherapeute->getPiece(),
                'email' => $kinesitherapeute->getEmail(),
            ],
        ]);
    }

   /**
     * @Route("/kine/{id}", name="get_kine_by_id", methods={"GET"})
     */
    public function getKineById($id): JsonResponse
    {
        // Récupérez le repository du kiné
        $kineRepository = $this->getDoctrine()->getRepository(Kinesitherapeutes::class);

        // Recherchez le kiné par son ID
        $kine = $kineRepository->find($id);

        if (!$kine) {
            // Si le kiné n'est pas trouvé, renvoyez une réponse JSON appropriée
            return new JsonResponse(['message' => 'Kiné non trouvé'], 404);
        }

        // Construisez un tableau avec les données du kiné
        $kineData = [
            'id' => $kine->getId(),
            'nom' => $kine->getNom(),
            'prenom' => $kine->getPrenom(),
            'date_naissance' => $kine->getDateNaissance()->format('Y-m-d'), // Formatage de la date
            'genre' => $kine->getGenre(),
            'adresse' => $kine->getAdresse(),
            'telephone' => $kine->getTelephone(),
            'email' => $kine->getEmail(),
            'nom_cabinet' => $kine->getNomcabinet(),
            'tel_cabinet' => $kine->getTelcabinet(),
            'adresse_cabinet' => $kine->getAdressecabinet(),
            'mail_cabinet' => $kine->getMailcabinet(),
        ];

        // Retournez les données du kiné au format JSON
        return $this->json($kineData);
    }

    /**
 * @Route("/rendez-vous-supp/{id}", name="delete_rendez_vous", methods={"DELETE"})
 */
public function deleteRendezVous($id): JsonResponse
{
    // Récupérez le gestionnaire d'entités (Entity Manager)
    $entityManager = $this->getDoctrine()->getManager();

    // Recherchez le rendez-vous par son ID
    $rendezVous = $entityManager->getRepository(RendezVous::class)->find($id);

    if (!$rendezVous) {
        // Si le rendez-vous n'est pas trouvé, renvoyez une réponse JSON appropriée
        return new JsonResponse(['message' => 'Rendez-vous non trouvé'], 404);
    }

    // Récupérez la date et l'heure actuelles
    $dateHeureActuelles = new \DateTime();
    $dateHeureFormatees = $dateHeureActuelles->format('Y-m-d H:i:s');
    echo $dateHeureFormatees;

// Affichez la date et l'heure formattées

    // Récupérez la date et l'heure du rendez-vous au format "Y-m-d H:i:s"
    $dateHeureRendezVousString = substr($rendezVous->getDateRendezVous(), 0, 10) . ' ' . $rendezVous->getHoraire();

    // Créez un objet DateTime à partir de la chaîne de date complète
    $dateHeureRendezVous = \DateTime::createFromFormat('Y-m-d H:i', $dateHeureRendezVousString);

    // Vérifiez si la création de l'objet DateTime a réussi
    if ($dateHeureRendezVous === false) {
        // Gestion de l'erreur : la création de l'objet DateTime a échoué
        return new JsonResponse(['message' => 'Erreur lors de la conversion de la date du rendez-vous'], 500);
    }

    // Calculez la différence entre la date actuelle et la date du rendez-vous
    $difference = $dateHeureActuelles->diff($dateHeureRendezVous);
    echo "Différence : " . $difference->format('%Y années, %m mois, %d jours, %H heures, %i minutes');

    // Vérifiez si la différence est inférieure à 12 heures (43200 secondes)
    if($dateHeureActuelles<$dateHeureRendezVous){
        echo "hhh";
    if (($difference->s + ($difference->i * 60) + ($difference->h * 3600) + ($difference->days * 86400) < 43200) 
    && $rendezVous->getstatut()==='confirmé')
    {
        // Si moins de 12 heures restent, renvoyez une réponse JSON appropriée
      
        return new JsonResponse(['message' => 'Impossible de supprimer le rendez-vous, moins de 12 heures avant l\'heure du rendez-vous'], 400);
    }}

    // Supprimez le rendez-vous
    $entityManager->remove($rendezVous);
    $entityManager->flush();

    // Répondez avec un message de succès
    return new JsonResponse(['message' => 'Rendez-vous supprimé avec succès']);
}



}


