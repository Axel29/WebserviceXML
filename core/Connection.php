<?php
class Connection
{
    /**
     * Get PDO's connection
     *
     * @return PDO $pdo PDO object
     */
    public static function getPDO()
    {
        try {
            $pdo = new PDO(DB, USER, PASSWORD);
            $pdo->exec('SET NAMES utf8');
            return $pdo;
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

}
