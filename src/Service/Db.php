<?php

namespace App\Service;

use Exception;
use PDO;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Db {
    private $db = '';
    private $user = '';
    private $pass = '';

    public function __construct(
        private ParameterBagInterface $parameters,
    ) {
        $this->db = $this->parameters->get('DB_NAME');
        $this->user = $this->parameters->get('DB_USER');
        $this->pass = $this->parameters->get('DB_PASS');
    }

    public function connexion() {
        $db = new PDO($this->db, $this->user, $this->pass);
        // $db->exec("SET CARACTER SET utf8");
        try {
            if ($db) {
                // echo "Connexion Ok!";
                return $db;
            }
        } catch (Exception $e) {
            // echo "Connexion error: " . $e->getMessage();
            return false;
        }
    }

}