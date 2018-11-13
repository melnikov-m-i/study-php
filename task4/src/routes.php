<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->group('/api', function() {

    $this->get('/products/', function(Request $request, Response $response) {
        $sql = "SELECT * FROM `products`";
        try {
            $db = $this->db;
            $stmt = $db->query($sql);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $db = null;
            return $response->withJson(['success' => true, 'data' => $products], 200);
        } catch (PDOException $e) {
            return $response->withJson(['success' => false, 'error' => ['message' => $e->getMessage()]], 400);
        }
    });

    $this->get('/products/{id:[0-9]+}/', function(Request $request, Response $response) {
        $id = $request->getAttribute('id');
        $sql = "SELECT * FROM `products` WHERE `id`=:id";
        try {
            $db = $this->db;
            $stmt = $db->prepare($sql);
            $stmt->bindParam("id", $id);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            $db = null;

            if ($product === false) {
                return $response->withJson(['success' => false, 'error' => ['message' => 'Нет товара с таким id']], 404);
            } else {
                return $response->withJson(['success' => true, 'data' => $product], 200);
            }
        } catch (PDOException $e) {
            return $response->withJson(['success' => false, 'error' => ['message' => $e->getMessage()]], 400);
        }
    });

    $this->post('/products/', function(Request $request, Response $response) {
        $product = json_decode($request->getBody(), true);

        $safeFields = Products::selfFields();

        $sql = "INSERT INTO `products` (`name`, `description`, `price`) VALUES (:name, :description, :price)";
        try {
            $db = $this->db;
            $stmt = $db->prepare($sql);

            foreach ($safeFields as $field) {
                if(array_key_exists($field, $product)) {
                    $stmt->bindParam(':'.$field, $product[$field]);
                }
            }

            $stmt->execute();
            $product['id'] = $db->lastInsertId();

            if ($stmt->rowCount() == 0) {
                return $response->withJson(['success' => false, 'error' => ['message' => "Не удалось добавить товар"]], 400);
            }

            $db = null;
            return $response->withJson(['success' => true, 'data' => $product], 201);
        } catch (PDOException $e) {
            return $response->withJson(['success' => false, 'error' => ['message' => $e->getMessage()]], 400);
        }
    });

    $this->put('/products/{id:[0-9]+}/', function(Request $request, Response $response) {
        $product = json_decode($request->getBody(), true);
        $id = $request->getAttribute('id');
        $safeFields = Products::selfFields();
        $sql = "UPDATE `products` SET ";
        $param = [];
        try {
            foreach ($safeFields as $field) {
                if(array_key_exists($field, $product)) {
                    $sql .= " `$field`=:$field,";
                    $param[':'.$field] = $product[$field];
                }
            }

            $sql = rtrim($sql, ',');

            if (count($param) == 0) {
                return $response->withJson(['success' => false, 'error' => ['message' => "Не переданы данные для обновления"]], 400);
            }

            $sql .= " WHERE `id`=:id";
            $param[':id'] = $id;

            $db = $this->db;
            $stmt = $db->prepare($sql);
            $stmt->execute($param);

            if ($stmt->rowCount() == 0) {
                return $response->withJson(['success' => false, 'error' => ['message' => "Нет товара с таким id"]], 404);
            }

            $db = null;
            $product['id'] = $id;

            return $response->withJson(['success' => true, 'data' => $product], 200);
        } catch (PDOException $e) {
            return $response->withJson(['success' => false, 'error' => ['message' => $e->getMessage()]], 400);
        }
    });

    $this->delete('/products/{id:[0-9]+}/', function(Request $request, Response $response) {
        $id = $request->getAttribute('id');
        $sql = "DELETE FROM `products` WHERE id=:id";
        try {
            $db = $this->db;
            $stmt = $db->prepare($sql);
            $stmt->bindParam("id", $id);
            $stmt->execute();

            if ($stmt->rowCount() == 0) {
                return $response->withJson(['success' => false, 'error' => ['message' => "Нет товара с таким id"]], 404);
            }

            $db = null;
            return $response->withJson(['success' => true, 'message' => 'Успешное удаление товара с переданным id'], 204);
        } catch (PDOException $e) {
            return $response->withJson(['success' => false, 'error' => ['message' => $e->getMessage()]], 400);
        }
    });
})->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});
