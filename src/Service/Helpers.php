<?php

namespace App\Service;

use App\Entity\Attestation;
use App\Entity\Attestion;
use App\Entity\Contrat;
use App\Entity\Demande;
use App\Entity\Journal;
use App\Entity\Log;
use App\Entity\Role;
use App\Entity\TypeContrat;
use App\Entity\TypeDemande;
use App\Entity\User;
use DateTime;
use DateTimeInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

class Helpers
{
    public function __construct(
        private LoggerInterface $logger, 
        private Security $security, 
        private ManagerRegistry $doctrine) {
    }
   
    public function getUser(): User {
        $user = $this->security->getUser();
        if ($user instanceof User) {
            return $user;
        }
    }

    // Fonction de comparaison de superiorité entre deux dates
    public function date1UpperToDate2(DateTimeInterface $date1, DateTimeInterface $date2) : bool {

        // Extraction du jour, mois et année de la premiere date
        $d1year = date_format($date1, 'y');
        $d1month = date_format($date1, 'm');
        $d1day = date_format($date1, 'd');

        // Extraction du jour, mois et année de la deuxieme date
        $d2year = date_format($date2, 'y');
        $d2month = date_format($date2, 'm');
        $d2day = date_format($date2, 'd');

        if ($d1year > $d2year) {
            return true;
        } else if ($d1year == $d2year) {
            if ($d1month > $d2month) {
                return true;
            } else if ($d1month == $d2month) {
                if ($d1day > $d2day) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else{
            return false;
        }
    }

    // Fonction de comparaison d'égalité entre deux dates
    public function date1EqualToDate2(DateTimeInterface $date1, DateTimeInterface $date2) : bool {

        // Extraction du jour, mois et année de la premiere date
        $d1year = date_format($date1, 'y');
        $d1month = date_format($date1, 'm');
        $d1day = date_format($date1, 'd');

        // Extraction du jour, mois et année de la deuxieme date
        $d2year = date_format($date2, 'y');
        $d2month = date_format($date2, 'm');
        $d2day = date_format($date2, 'd');

        if ($d1year > $d2year) {
            return false;
        } else if ($d1year < $d2year) {
            return false;
        } else{
            if ($d1month > $d2month) {
                return false;
            } else if ($d1month < $d2month) {
                return false;
            } else {
                if ($d1day > $d2day) {
                    return false;
                } else if ($d1day < $d2day) {
                    return false;
                } else {
                    return true;
                }
            }
        }
    }

    public function imageToBase64($path) {
        $path = $path;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }

    public function genererReferenceContrat(
        ManagerRegistry $doctrine,
    ): string {
        $repository = $doctrine->getRepository(Contrat::class);
        $lastId = 0;
        $nbContrat = count([]);
        $nbAleatoire = rand();
        $annee = date_format(new DateTime('now'), 'y');
        $mois = date_format(new DateTime('now'), 'm');
        $jour = date_format(new DateTime('now'), 'd');
        $ref = 'DCH-OBF-STAGE-'.$nbAleatoire.'-'.$jour.'-'.$mois.'-'.$annee.'-'.$nbContrat;
        return $ref;
    }

    public function hasContrat(
        ManagerRegistry $doctrine, 
        Demande $demande,
    ): bool {
        $repository = $doctrine->getRepository(Contrat::class);
        $contratFound = $repository->findBy(
            [
                'debut' => $demande->getDebut(),
                'fin' => $demande->getFin(),
                'demande' => $demande,
            ]
        );
        return $contratFound != null;
    }
    
    public function newContrat(
        ManagerRegistry $doctrine, 
        Demande $demande,
    ): void {

        $contrat = new Contrat();
        $debut = $demande->getDebut();
        $fin = $demande->getFin();

        // Renseigner mon objet contrat
        $contrat->setReference($this->genererReferenceContrat($doctrine));
        $contrat->setDebut($debut);
        $contrat->setFin($fin);
        $contrat->setDemande($demande);
        $contrat->setCreatedAt(new DateTime('now'));

        // sauvegarder l'objet
        $manager = $doctrine->getManager();
        $manager->persist($contrat);
        $manager->flush($contrat);
        
    }

    public function hasAttestation(
        ManagerRegistry $doctrine, 
        Contrat $contrat,
    ): bool {
        $repository = $doctrine->getRepository(Attestation::class);
        $contratFound = $repository->findBy(
            [
                'contrat' => $contrat,
            ]
        );
        return $contratFound != null;
    }
    
    public function newAttestation(
        ManagerRegistry $doctrine, 
        Contrat $contrat,
    ): void {

        $attestation = new Attestation();
        
        // Renseigner mon objet attestation
        $attestation->setContrat($contrat);
        $attestation->setCreatedAt(new DateTime('now'));
        
        // sauvegarder l'objet attestation
        $manager = $doctrine->getManager();
        $manager->persist($attestation);
        $manager->flush($attestation);     
    }

    public function addLog(
        Log $log):void
    {
        $manager = $this->doctrine->getManager();
        $manager->persist($log);
        $manager->flush();
    }

    public function newJournal($action):Journal
    {
        $newJournal = new Journal();
        $newJournal->setUser($this->getUser());
        $newJournal->setAction($action);
        $newJournal->setCreatedAt(new DateTime('now'));
        return $newJournal;
    }

    public function addJournal($action):void
    {
        $newJournal = $this->newJournal($action);
        $manager = $this->doctrine->getManager();
        $manager->persist($newJournal);
        $manager->flush();
    }
}