<?php

namespace App\Service;


use App\Entity\Journal;
use App\Entity\Log;
use App\Entity\User;
use DateTime;
use DateTimeInterface;
use Doctrine\Persistence\ManagerRegistry;
use PDO;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

class Helpers
{
    public function __construct(
        private LoggerInterface $logger, 
        private Security $security, 
        private ManagerRegistry $doctrine) {
    }

    public function getDatabase() {

        // $db_username = "youusername";
        // $db_password = "yourpassword";
        // $db = "oci:dbname=yoursid";
        $db_username = "dbedi";
        $db_password = "dbedi";
        $db = "oci:dbname=sbabfte";
        $conn = new PDO($db,$db_username,$db_password);
        return $conn;
    }

    public function dateFormat(DateTimeInterface $date): string {
        // Extraction du jour, mois et année de la date
        $year = date_format($date, 'y');
        $month = date_format($date, 'm');
        $day = date_format($date, 'd');
        return $day.'/'.$month.'/'.$year;
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