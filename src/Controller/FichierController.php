<?php

namespace App\Controller;

use App\Repository\FichierRepository;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class FichierController extends AbstractController
{
    private $fichierRepository;

    public function __construct(FichierRepository $pfeRepository)
    {
        $this->fichierRepository = $pfeRepository;
    }

    #[Route('/api/file/{id}', name: 'app_file_download', methods: ['GET'])]
    public function download(int $id): BinaryFileResponse
    {
        $pfe = $this->fichierRepository->find($id);

        if ($pfe->getFichier() == null) {
            throw $this->createNotFoundException('The file does not exist');
        }

        $filePath = $this->getParameter('kernel.project_dir') .'/public/uploads/file/' . $pfe->getFichier();

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('The file does not exist');
        }

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $pfe->getFichier());

        return $response;
    }
}