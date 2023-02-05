<?php

namespace App\Models;

use \PDO;

class Db
{
    public function connect()
    {
        $db_url = 'sqlite:' . dirname(dirname(dirname(__FILE__))) . '/db.sqlite';
        $pdo = new PDO($db_url, "", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        return $pdo;
    }

    public function setup_db()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS messages (
      id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
      from_user_id INTEGER NOT NULL,
      to_user_id INTEGER NOT NULL,
      message TEXT NOT NULL,
      read_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL
     )';

        $pdo = $this->connect();
        $stat = $pdo->prepare($sql);
        assert($stat);
        $res = $stat->execute([]);
        assert($res);

        $sql = 'CREATE TABLE IF NOT EXISTS users (
      id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
      email varchar(255) NOT NULL,
      name varchar(255) NOT NULL,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL
     )';

        $pdo = $this->connect();
        $stat = $pdo->prepare($sql);
        assert($stat);
        $res = $stat->execute([]);
        assert($res);

        $sql = "INSERT INTO users (email, name) VALUES ('arslan@chat.com','arslan')";

        $pdo = $this->connect();
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = "INSERT INTO users (email, name) VALUES ('abid@chat.com','abid')";

        $pdo = $this->connect();
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = "INSERT INTO messages (from_user_id, to_user_id,message) VALUES (2,1,'first message')";

        $pdo = $this->connect();
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = "INSERT INTO messages (from_user_id, to_user_id,message) VALUES (2,1,'second message')";

        $pdo = $this->connect();
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = "INSERT INTO messages (from_user_id, to_user_id,message) VALUES (1,2,'third message')";

        $pdo = $this->connect();
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }

}