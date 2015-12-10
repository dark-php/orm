<?php

namespace Darktec\ORM;

use PDO;

class DB
{
    public static $pdo;

    /**
     * Creates a PDO connection to the database
     *
     * @param string $host
     * @param string $dbname
     * @param string $user
     * @param string $password
     */
    public static function init(\string $host, \string $dbname, \string $user, \string $password)
    {
        try {
            self::$pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ));
        } catch (\PDOException $e) {
            die('Could not connect: ' . $e->getMessage());
        }
    }

    /**
     * Create table with defined columns
     *
     * @param string $table
     * @param array $columns
     */
    public static function createTable(\string $table, array $columns)
    {
        $sql = "CREATE TABLE $table (id INT(10) AUTO_INCREMENT PRIMARY KEY, ";

        foreach ($columns as $k => $column) {
            $null = "NOT NULL";
            if ($column['null']) $null = "NULL";
            $sql .= "$k ".$column['type']." $null, ";
        }
        $sql = substr($sql, 0, -2) . ");";

        try {
            self::$pdo->exec($sql);
        } catch (\PDOException $e) {
            die("Failed to create table: ".$e->getMessage());
        }
    }

    /**
     * Insert values into table
     *
     * @param string $table
     * @param array $values
     */
    public static function insert(\string $table, array $values)
    {
        $query = "INSERT INTO $table (";

        foreach (array_keys($values) as $k) {
            $query .= $k . ", ";
        }
        $query = substr($query, 0, -2) . ") VALUES (";

        for ($i=0; $i < count($values); $i++) {
            $query .= "?, ";
        }
        $query = substr($query, 0, -2) . ");";

        $stmt = self::$pdo->prepare($query);
        $j=1;
        foreach ($values as $v) {
            $stmt->bindValue($j, $v);
            $j++;
        }

        try {
            $stmt->execute();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function select(\string $table, \int $id) {
        $query = "SELECT * FROM $table WHERE id = $id";

        try {
            return self::$pdo->query($query);
        } catch (\PDOException $e) {
            die("Item not found: ".$e->getMessage());
        }
    }
}