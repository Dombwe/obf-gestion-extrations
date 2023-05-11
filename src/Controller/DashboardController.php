<?php

namespace App\Controller;

use App\Entity\Fichier;
use App\Form\UploadFileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard', options: ['expose' => true])]

class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard', methods: ['POST', 'GET'], options: ['expose' => true])]
    public function index(): Response
    {
        return $this->redirectToRoute('fichier.upload');

        // // return $this->render('fichier/index.html.twig', [
        // //     'controller_name' => 'FichierController',
        // // ]);
    }
}
