<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

$app->get('/api/gestion/usuarios', function(Request $request, Response $response) {
    $consulta = 'select c.nombre as nombrePC, u.matricula, u.nombre, u.email, u.carrera, u.telefono, u.semestre
                 from Computadora c inner join Usuario u on
                 c.id_usuario = u.matricula;';

    try {
        $db = new DB();
        $db = $db->conectar();
        $ejecutar = $db->query($consulta);

        $usuarios = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        echo json_encode($usuarios);

        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    } catch(PDOException $e) {
        echo '{"error":{"text":'.$e->getMessage().'}}';
    }
});