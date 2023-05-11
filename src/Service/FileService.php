<?php

namespace App\Service;


use App\Entity\Historique;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use PDO;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


use App\Service\Db;
use Exception;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileService
{
   

    public function __construct(
        private ParameterBagInterface $parameters,
        private Helpers $helpers,
        private ManagerRegistry $doctrine,
        private UploaderService $uploaderService,
        private Environment $environment,
        private Db $db,
    ) {

    }

    public function handleFileForm(FormInterface $fileForm): JsonResponse {

        if ($fileForm->isValid()) {
            return $this->handleValidFileForm($fileForm);
        } else {
            return $this->handleValidFileForm($fileForm);
        }

    }

    public function handleValidFileForm(FormInterface $fileForm): JsonResponse {

        /** @var UploadedFile $file */
        $file = $fileForm['chargement']->getData();
        $debut = $fileForm['date_debut_extraction']->getData();
        $fin = $fileForm['date_fin_extraction']->getData();

        $response = $this->loadFile($file, $debut, $fin);
        $content = json_decode($response->getContent(), true);
     
        return new JsonResponse([
            'code' => $content['code'],
            'response' =>  $content['response']
        ]);

    }

    public function handleInvalidFileForm(FormInterface $fileForm): JsonResponse {

        return new JsonResponse([
            'code' => 400,
            'response' => $this->getErrorsMessages($fileForm)
        ]);
    }

    public function getErrorsMessages(FormInterface $form){
        $errors = [];

        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        
        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorsMessages($child);
            }
        }

        return $errors;
    }


    public function isCorrectFile($path) {
        $spreadsheet = IOFactory::load($path);
        $rows = $spreadsheet->getActiveSheet()->toArray();
        $isCorrectFile = true;
        foreach ($rows as $row) {
            if ($isCorrectFile == true and strlen($row[1]) != 11) {
                $isCorrectFile = false;
            }
        }
        return $isCorrectFile;
    }

    public function getExtractionsCompteNumbers($path) {
        $spreadsheet = IOFactory::load($path);
        $rows = $spreadsheet->getActiveSheet()->toArray();
        $isCorrectFile = true;
        $numbers = "";
        $cpt = 1;
  
        foreach ($rows as $row) {
            if ($isCorrectFile == true and strlen($row[1]) != 11) {
                $isCorrectFile = false;
                return;
            }
            if ($cpt < count($rows)) {
                $numbers= $numbers."'".$row[1]."',";
                $cpt = $cpt + 1;
            } else {
                $numbers= $numbers."'".$row[1]."'";
            } 
        }
        return $isCorrectFile == true ? $numbers : null;
    }

    public function getExctractionDates($debut, $fin) {
        $debut = $this->helpers->dateFormat($debut);
        $fin = $this->helpers->dateFormat($fin);
        return 'TO_DATE(\''.$debut.'\', \'DD/MM/YY\') and TO_DATE(\''.$fin.'\', \'DD/MM/YY\')';
    }


    public function getExtractionSqlRequest($path, $debut, $fin) {
        $comptes = $this->getExtractionsCompteNumbers($path);
        $dates = $this->getExctractionDates( $debut, $fin);
        $originalSql = $this->parameters->get('MONETIQUE_SQL_EXTRACTION');
        $originalCondition = $this->parameters->get('MONETIQUE_SQL_EXTRACTION_CONDITION');
        $sql = $originalSql.' '.$dates.' '.$originalCondition.' ('.$comptes .')';
        return $sql;
    }

    public function getExtractData($path, $debut, $fin) {
        $sql = $this->getExtractionSqlRequest($path, $debut, $fin);
        $oci = $this->db->connexion();
        $request = $oci->prepare($sql);
        $request->execute();
        return $request->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createExtractionFile($path, $filename, $debut, $fin, $historique) {

        try {

            $data =  $this->getExtractData($path, $debut, $fin);
            $lineNumber = count($data);

            $pos = strripos($filename, '.');
            $newFilename = 'Ex_'.substr($filename, 0, $pos).'.xlsx';

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            /**
             *  Code ASCII (Alphabet Majuscule)
             *  65 => A
             *  90 => Z
            **/
            $codeASCII = 65;

            foreach ($data[0] as $key => $value) {
                $cellule = chr($codeASCII).'1';
                $codeASCII = $codeASCII + 1;
                $sheet->setCellValue($cellule, $key);
            }

            $codeASCII = 65;

            for ($i = 0; $i < $lineNumber; $i++) {
                foreach ($data[$i] as $value) {
                    $cellule = chr($codeASCII).''.($i+2);
                    if(chr($codeASCII) == 'F') {
                        $newDate = (new DateTime($value))->format('d/m/Y');
                        $sheet->setCellValue($cellule, $newDate);
                    } else {
                        $sheet->setCellValue($cellule, $value);
                    }
                    $codeASCII = $codeASCII + 1;
                }
                $codeASCII = 65;
            }

            $newDirectoryFolder = $this->parameters->get('MONETIQUE_EXTRACTION_DIRECTORY');

            // Write a new .xlsx file
            $writer = new Xlsx($spreadsheet);
            // Save the new .xlsx file
            $writer->save($newDirectoryFolder.'/'.$newFilename);

            $historique->setExtraction($newFilename);

            $manager = $this->doctrine->getManager();
            $manager->persist($historique);
            $manager->flush();
            $manager->refresh($historique);
            
            return new JsonResponse([
                'code' => 200,
                'response' => $historique
            ]);


        } catch (Exception $e) {
            return new JsonResponse([
                'code' => 400,
                'response' => $e->getMessage()
            ]);
        }


    }

    public function loadFile($file, $debut, $fin) {

        $fileExtension = strtolower($file->getClientOriginalExtension());

        if ($fileExtension === 'xls' OR $fileExtension === 'xlsx' OR $fileExtension === 'csv') {

            if (
                $this->helpers->date1UpperToDate2($fin, $debut)
                OR $this->helpers->date1EqualToDate2($fin, $debut)
            ) {

                if (
                    $this->helpers->date1UpperToDate2(new DateTime('now'), $fin)
                    OR $this->helpers->date1EqualToDate2(new DateTime('now'), $fin)
                ) {

                    $historique = new Historique();
                    $filename = $this->uploaderService->getFileName($file);
                    $directory = $this->parameters->get('MONETIQUE_LOADING_DIRECTORY');
                    
                    if ($this->isCorrectFile($file->getPathname()) === true) {
                        $fichierRepository = $this->doctrine->getRepository(Historique::class);
                        $foundFile = $fichierRepository->findBy(['chargement' => $filename]);

                        // if (false) {
                        if ($foundFile != null) {
                            return new JsonResponse([
                                'code' => 400,
                                'response' => 'Fichier existant!'
                            ]);
                        } else {

                            $this->uploaderService->uploadFile($file, $directory);
                            $historique->setChargement($filename);
                            $historique->setDateChargement(new DateTime('now'));
                            $historique->setDateDebutExtraction($debut);
                            $historique->setDateFinExtraction($fin);


                            $manager = $this->doctrine->getManager();
                            $manager->persist($historique);
                            $manager->flush();
                            $manager->refresh($historique);

                            $extractionResponse = $this->createExtractionFile($directory.'/'.$filename, $file->getClientOriginalName(), $debut, $fin, $historique);
                            $content = json_decode($extractionResponse->getContent(), true);
                            
                            if ($content['code'] == 200) {
                                $manager->refresh($historique);
                            }

                            return new JsonResponse([
                                'code' => 200,
                                'response' => $this->environment->render('fichier/fichier.html.twig', [
                                    'historique' => $historique,
                                    'filename' => $file->getClientOriginalName(),
                                    'debut' => $debut,
                                    'fin' => $fin
                                ])
                            ]);
                        } 

                    } else {
                        return new JsonResponse([
                            'code' => 400,
                            'response' => 'Fichier incorrect !'
                        ]);
                    } 
                } else {
                    return new JsonResponse([
                        'code' => 400,
                        'response' => 'La date de fin doit être inferieure ou égale à la date du jour!'
                    ]);
                }

            } else {
                return new JsonResponse([
                    'code' => 400,
                    'response' => 'La date de début doit être inferieure ou égale à la date de fin!'
                ]);
            }
        } else {
            return new JsonResponse([
                'code' => 400,
                'response' => 'Veuillez charger un fichier excel !'
            ]);
        }
    }


}