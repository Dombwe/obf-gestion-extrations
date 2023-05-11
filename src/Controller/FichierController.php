<?php

namespace App\Controller;

use App\Entity\Fichier;
use App\Entity\Historique;
use App\Form\UploadFileType;
use App\Repository\HistoriqueRepository;
use App\Service\FileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use DateTime;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Validator\Constraints\Length;
use App\Service\Helpers;
use App\Service\Db;
use App\Service\UploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PDO;
use SebastianBergmann\Environment\Console;

#[Route('/', options: ['expose' => true])]


class FichierController extends AbstractController
{
    #[Route('', name: 'fichiers', methods: ['POST', 'GET'], options: ['expose' => true])]
    public function index(
        Historique $historique = null,
        RequestStack $requestStack,
        FileService $fileService,
        HistoriqueRepository $historiqueRepository
    ): Response {
 
        $request = $requestStack->getMainRequest();
        $fileForm = $this->createForm(UploadFileType::class, $historique);
        $fileForm->handleRequest($request);

        if ($fileForm->isSubmitted()) {
            return $fileService->handleFileForm($fileForm);
        }

        return $this->render('fichier/index.html.twig', [
            'controller_name' => 'FichierController',
            'form' => $fileForm->createView(),
            'historiques' => $historiqueRepository->getHistoriques(),
        ]);
    }

    


}
