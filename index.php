
<?php

require_once __DIR__.'/vendor/autoload.php';


use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => getenv('DB_CONNECTION'),
    'host' => getenv('DB_HOST'),
    'port' => getenv('DB_PORT'),
    'database' => getenv('DB_DATABASE'),
    'username' => getenv('DB_USERNAME'),
    'password' => getenv('DB_PASSWORD'),
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app = new \Slim\App(new \Slim\Psr7\Factory\ResponseFactory());

// Получение коллекции ресурсов
$app->get('/cities', function (Request $request, Response $response) {
    $cities = \App\Models\City::all();

    $data = [];
    foreach ($cities as $city) {
        $data[] = [
            'id' => $city->id,
            'name' => $city->name,
        ];
    }

    return $response->withJson(['cities' => $data]);
});

// Создание ресурса
$app->post('/cities', function (Request $request, Response $response) {
    $data = $request->getParsedBody();

    $city = new \App\Models\City();
    $city->name = $data['name'];
    $city->save();

    return $response->withJson(['id' => $city->id]);
});

// Обновление ресурса
$app->put('/cities/{id}', function (Request $request, Response $response, $args) {
    $id = $args['id'];
    $data = $request->getParsedBody();

    $city = \App\Models\City::find($id);
    $city->name = $data['name'];
    $city->save();
    $city->name = $data['name'];
$city->save();

    return $response->withJson(['id' => $city->id]);
});


// Удаление ресурса
$app->delete('/cities/{id}', function (Request $request, Response $response, $args) {
    $id = $args['id'];

    $city = \App\Models\City::find($id);
    $city->delete();

    return $response->withJson(['success' => true]);
});

