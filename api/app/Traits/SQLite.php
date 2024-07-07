<?php

namespace App\Traits;

use PDO;
use PDOException;
use Illuminate\Support\Facades\DB;


trait SQLite
{
    //288 Females
    //215 males

    public function getRecords($query)
    {
        $connection = $this->sqlite_con();
        // Execute queries on the connection
        $data = $connection->query($query)->fetchAll();

        return $this->cleanseItems($data);
    }

    public function getNames($gender)
    {
        $query = "SELECT fullName, gender FROM fakenames WHERE gender = '$gender' ";
        $records = $this->getRecords($query);

        $data = [];

        foreach ($records as $record) {
            $data[] = $record['fullName'];
        }

        return $data;
    }

    public function insertIntoDb($fullName, $gender)
    {
        $query = "INSERT INTO fakenames(fullName, gender) VALUES('$fullName', '$gender')";
        $connection = $this->sqlite_con2();
        $connection->exec($query);
    }

    public function createTable()
    {

        $connection = $this->sqlite_con();

        // Create the table
        $connection->exec("
        CREATE TABLE IF NOT EXISTS faker_db.fake_names (
            id INTEGER PRIMARY KEY,
            fullName VARCHAR(40),
            gender VARCHAR(6) CHECK (gender IN ('Female', 'Male'))
        )");

        // Close the database connection
        $connection = null;

        return "Table created successfully!";
    }

    public function updateTable($query)
    {

        $connection = $this->sqlite_con();
        $connection->exec($query);
        $connection = null;

        return "Table updated successfully!";
    }

    private function cleanseItems($persons)
    {
        $data = [];

        foreach ($persons as $person) {
            $data[] = [
                "fullName" => $person['fullName'],
                "gender" => $person['gender']
            ];
        }

        return $data;
    }

    private function sqlite_con()
    {
        try {
            // $databasePath = "C:/Users/eugen/Documents/Workspace/kwayedza/kwayedza_api/app/data/faker.sqlite";
            $databasePath = "C:/Users/eugen/Documents/Workspace/FakeNames/fake_names.sqlite3";

            // Create a new SQLite PDO instance
            $pdo = new PDO("sqlite:{$databasePath}");

            // Set PDO to throw exceptions on errors
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Attach the database
            $pdo->exec("ATTACH DATABASE '{$databasePath}' AS 'fake_names_db'");

            return $pdo;
        } catch (PDOException $e) {
            // Handle the exception if the connection fails
            die("Connection failed: " . $e->getMessage());
        }
    }
}
