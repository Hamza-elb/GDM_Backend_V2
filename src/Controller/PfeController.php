<?php

namespace App\Controller;

use App\Entity\Fichier;
use App\Entity\Pfe;
use App\Form\PfeType;
use App\Repository\FichierRepository;
use App\Repository\PfeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Proxy;
use FOS\RestBundle\Controller\Annotations\Route;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PfeController extends AbstractController
{
    private $repository;
    public function __construct(EntityManagerInterface $entityManager,
                                PfeRepository $repository,
                                FichierRepository $fichierRepository)
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        $this->fichierRepository = $fichierRepository;
    }
    public function  __invoke(Request $request): JsonResponse
    {
        $titre = $request->get('titre');
        $type = $request->get('type');
        $nom1 = $request->get('nom1');
        $nom2 = $request->get('nom2');
        $specialite = $request->get('specialite');
        $resume = $request->get('resume');
        $encadrePar = $request->get('encadrePar');
        $technologies = $request->get('technologies');
        $autreTechnologie  = $request->get('autreTechnologie');

        $pfe = new Pfe();
        $pfe->setTitre($titre);
        $pfe->setType($type);
        $pfe->setNom1($nom1);
        $pfe->setNom2($nom2);
        $pfe->setSpecialite($specialite);
        $pfe->setResume($resume);
        $pfe->setEncadrePar($encadrePar);
        $pfe->setTechnologies($technologies);
        $pfe->setAutreTechnologie($autreTechnologie);

        // Gérer le fichier uploadé
        $uploadedFile = $request->files->get('file');
        $fichierName = $request->get('fichier');
        if ($uploadedFile) {
            $fichier = new Fichier();
            $fichier->setFichier($fichierName);
            $fichier->setFile($uploadedFile);
            $fichier->setUpdatedAt(new \DateTimeImmutable());


            $this->entityManager->persist($fichier);
            $this->entityManager->flush();


            $pfe->setFichier($fichier);
        }

        $this->entityManager->persist($pfe);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'PFE créé avec succès'], 201);
    }

    #[Route('/api/pfes/{id}', name: 'app_update_pfe', methods: ['POST'])]
    public function updatePfe(Request $request, $id, LoggerInterface $logger): JsonResponse
    {

        $pfe = $this->repository->find($id);
        $fichierInDB = $this->fichierRepository->find($pfe->getFichier()->getId());
        // Vérifier si l'objet est un proxy non initialisé
        if ($fichierInDB instanceof Proxy) {
            $fichierInDB->__load(); // Forcer l'initialisation du proxy
        }
        if (!$pfe) {
            return new JsonResponse(['error' => 'PFE not found'], 404);
        }
        // Créer et gérer le formulaire
        $form = $this->createForm(PfeType::class, $pfe);
        $form->handleRequest($request);

        // Log des données reçues pour le débogage
        $formData = $request->request->all();


        $logger->info('Form Data: ' . json_encode($formData));
        // Récupérer le fichier uploadé
        $uploadedFile = $request->files->get('file');
        if ($uploadedFile) {
            // Supprimer l'ancien fichier si nécessaire
            if ($pfe->getFichier()) {
                $oldFileName = $fichierInDB->getFichier();

                $oldFile = $this->getParameter('kernel.project_dir') .'/public/uploads/file/' . $oldFileName;
                $filesystem = new Filesystem();
                // Vérifier si le fichier existe et le supprimer
                if ($filesystem->exists($oldFile)) {
                    $filesystem->remove($oldFile);
                    $logger->info('Ancien fichier supprimé: ' . $oldFile);
                }
                $this->entityManager->remove($fichierInDB);
            }

            // Gérer le nouveau fichier
            $fichier = new Fichier();
            $fichier->setFile($uploadedFile);
            $fichier->setUpdatedAt(new \DateTimeImmutable());
            // Persister le nouveau fichier
            $this->entityManager->persist($fichier);
            $pfe->setFichier($fichier);
        }
        // Remplacer les anciennes données par les nouvelles données
        $pfe->setTitre($request->get('titre', $pfe->getTitre()));
        $pfe->setType($request->get('type', $pfe->getType()));
        $pfe->setNom1($request->get('nom1', $pfe->getNom1()));
        $pfe->setNom2($request->get('nom2', $pfe->getNom2()));
        $pfe->setSpecialite($request->get('specialite', $pfe->getSpecialite()));
        $pfe->setResume($request->get('resume', $pfe->getResume()));
        $pfe->setEncadrePar($request->get('encadrePar', $pfe->getEncadrePar()));
        $technologies = $request->get('technologies', json_encode($pfe->getTechnologies()));
        if ($technologies) {
            $pfe->setTechnologies(json_decode($technologies, true));
        }
        $pfe->setAutreTechnologie($request->get('autreTechnologie', $pfe->getAutreTechnologie()));
        // Persister les nouvelles données dans la base
        $this->entityManager->persist($pfe);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'PFE mis à jour avec succès'], 200);
    }
    #[Route('/api/pfes/{id}', name: 'delete_pfe', methods: ['DELETE'])]
    public function deletePfe(Request $request, int $id): JsonResponse
    {
        $pfe = $this->repository->find($id);
        $fichierInDB = $this->fichierRepository->find($pfe->getFichier()->getId());

        if (!$pfe) {
            return new JsonResponse(['error' => 'PFE not found'], 404);
        }

        // Get the associated Fichier entity (if it exists)
        $fichier = $fichierInDB->getFichier();

        if ($fichier) {
            // Remove the file from the filesystem
            $filePath = $this->getParameter('kernel.project_dir') . '/public/uploads/file/' . $fichier;

            $filesystem = new Filesystem();
            if ($filesystem->exists($filePath)) {
                $filesystem->remove($filePath);
            }
            // Setting the Fichier reference to null (if not deleting PFE)
            $pfe->setFichier(null);
        }

        // Remove the PFE entity, and cascade will take care of the Fichier
        // attention salma il faut supprimer le parent avant le fils
        $this->entityManager->remove($pfe);
        $this->entityManager->flush();

        $this->entityManager->remove($fichierInDB);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'PFE and associated file deleted successfully'], 200);
    }



}