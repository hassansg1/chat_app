<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use App\Models\Db;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    // setup database tables
    $app->get('/db_setup', function (Request $request, Response $response) {
        $db = new Db();
        $db->setup_db();

        echo 'DB tables are now setup.';
        return $response->withStatus(200);
    });

    // to get all messages sent to a user
    $app->get('/messages/[{to_user_id}]', function ($request, $response, $args) {
        $sql = "SELECT messages.*,from_user.name as message_from FROM messages
inner join users as from_user on from_user.id = messages.from_user_id
where to_user_id=" . $args['to_user_id'];
        try {
            $db = new Db();
            $conn = $db->connect();
            $stmt = $conn->query($sql);
            $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;

            $response->getBody()->write(json_encode($customers));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } catch (PDOException $e) {
            $error = array(
                "message" => $e->getMessage()
            );

            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(500);
        }
    });

    //refresh messages, get messages in last 20 seconds
    $app->get('/getNewMessages/[{to_user_id}]', function ($request, $response, $args) {
        $sql = "SELECT messages.*,from_user.name as message_from FROM messages
inner join users as from_user on from_user.id = messages.from_user_id
where messages.created_at < CURRENT_TIMESTAMP
and messages.created_at  > now() - interval 20 second
AND to_user_id=" . $args['to_user_id'];
        try {
            $db = new Db();
            $conn = $db->connect();
            $stmt = $conn->query($sql);
            $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;

            $response->getBody()->write(json_encode($customers));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } catch (PDOException $e) {
            $error = array(
                "message" => $e->getMessage()
            );

            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(500);
        }
    });

    //send message and save to db
    $app->post('/messages/add', function (Request $request, Response $response, array $args) {
        $data = $request->getParsedBody();
        $message = $data["message"];
        $to_user_id = $data["to_user_id"];
        $from_user_id = $data["from_user_id"];

        $sql = "INSERT INTO messages (message, to_user_id, from_user_id) VALUES (:message, :to_user_id, :from_user_id)";

        try {
            $db = new Db();
            $conn = $db->connect();

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':message', $message);
            $stmt->bindParam(':to_user_id', $to_user_id);
            $stmt->bindParam(':from_user_id', $from_user_id);

            $result = $stmt->execute();

            $response->getBody()->write(json_encode($result));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } catch (PDOException $e) {
            $error = array(
                "message" => $e->getMessage()
            );

            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(500);
        }
    });
};
