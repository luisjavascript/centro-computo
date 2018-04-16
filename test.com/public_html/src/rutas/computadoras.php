<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

//obtener todas las computadoras

$app->get('/api/tablero', function(Request $request, Response $response) {
    $consulta = 'select c.estado, c.nombre as nombrePC, u.matricula, u.nombre as nombreUsu 
                 from Computadora c inner join Usuario u 
                 on c.id_usuario = u.matricula UNION 
                 select cv.estado, cv.nombre as nombrePC, null, null from Computadora cv where cv.estado = false;';

    try {
        $db = new DB();
        $db = $db->conectar();
        $ejecutar = $db->query($consulta);

        $computadoras = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        echo json_encode($computadoras);

        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    } catch (PDOException $e) {
        echo '{"error":{"text":'.$e->getMessage().'}}';
    }
});

$app->post('/api/gestion/computadora/agregar', function(Request $request, Response $response) {
    $nombre = $request->getParam('nombre');
    $estado = $request->getParam('estado');

    $consulta = 'insert into Computadora(estado, nombre)
                 values(:estado,:nombre);';

    try {
        // instancia de la bd
        $db = new DB();
        // conexion de la bd
        $db = $db->conectar();

        $stmt = $db->prepare($consulta);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':nombre', $nombre);

        $stmt->execute();

        // echo '{"notice":"pc agregada correctamente"}';

        $registro = 'select * from Computadora c where c.nombre = "'.$nombre.'";';
        $ejecutar = $db->query($registro);
        $computadoras = $ejecutar->fetchAll(PDO::FETCH_OBJ);

        echo json_encode($computadoras);

        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    } catch (PDOException $e) {
        echo '{"error":{"text":'.$e->getMessage().'}}';
    }
});

$app->get('/api/tablero/ocupadas', function(Request $request, Response $response) {
    $consulta = 'select * from Computadora where Computadora.estado = true';

    try {
        $db = new DB();
        $db = $db->conectar();
        $ejecutar = $db->query($consulta);
        $computadoras = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        echo json_encode($computadoras);
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    } catch (PDOException $e) {
        echo '{"error":{"text":'.$e->getMessage().'}}';
    }
});

$app->get('/api/tablero/disponibles', function(Request $request, Response $response) {
    $consulta = 'select * from Computadora where Computadora.estado = false';

    try {
        $db = new DB();
        $db = $db->conectar();
        $ejecutar = $db->query($consulta);
        $computadoras = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        echo json_encode($computadoras);
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    } catch (PDOException $e) {
        echo '{"error":{"text":'.$e->getMessage().'}}';
    }
});


/*
 *
 * RUTAS DE LOS USUARIOS
 *
 *
 * */
$app->get('/api/gestion/usuarios', function(Request $request, Response $response) {
    $consulta = '
                 select c.nombre as nombrePC, u.matricula, u.nombre, u.email, u.carrera, u.telefono, u.semestre
                 from Computadora c inner join Usuario u on
                 c.id_usuario = u.matricula
                 union 
                 select null, us.matricula, us.nombre, us.email, us.carrera, us.telefono, us.semestre from Usuario us where not matricula in 
                 (select id_usuario from Computadora where id_usuario is not null);';

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


$app->post('/api/gestion/usuarios/agregar', function(Request $request, Response $response) {
    $matricula = $request->getParam('matricula');
    $nombre = $request->getParam('nombre');
    $email = $request->getParam('email');
    $carrera = $request->getParam('carrera');
    $telefono = $request->getParam('telefono');
    $semestre = $request->getParam('semestre');

    $consulta = 'insert into Usuario(matricula, nombre, email, carrera, telefono, semestre)
                 values (:matricula, :nombre, :email, :carrera, :telefono, :semestre)';

    try {
        // instancia de la bd
        $db = new DB();
        // conexion de la bd
        $db = $db->conectar();

        $stmt = $db->prepare($consulta);
        $stmt->bindParam(':matricula', $matricula);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':carrera', $carrera);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':semestre', $semestre);

        $stmt->execute();

        echo '{"notice":"usuario agregado correctamente"}';
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    } catch (PDOException $e) {
        echo '{"error":{"text":'.$e->getMessage().'}}';
    }
});

/*
 *
 *
 * RUTAS PARA EL SOFTWARE
 *
 * */

$app->get('/api/gestion/software', function(Request $request, Response $response) {
    $consulta = "select count(*) as numInstala, sw.id_software, sw.nombre, sw.alias, sw.version from 
                 Computadora_Software cs inner join Software sw
                 on cs.id_software = sw.id_software
                 group by 'numInstala', sw.id_software, sw.nombre, sw.alias, sw.version
                 union
                 select null, s.id_software, s.nombre, s.version, s.alias
                 from Software s where not s.id_software in
                 (select id_software from Computadora_Software where id_software is not null);";

    try {
        $db = new DB();
        $db = $db->conectar();
        $ejecutar = $db->query($consulta);

        $software = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        echo json_encode($software);

        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    } catch(PDOException $e) {
        echo '{"error":{"text":'.$e->getMessage().'}}';
    }
});

$app->post('/api/gestion/software/agregar', function(Request $request, Response $response) {
    $nombre = $request->getParam('nombre');
    $alias = $request->getParam('alias');
    $version = $request->getParam('version');

    $consulta = 'insert into Software(nombre, alias, version)
                 values (:nombre, :alias, :version)';

    try {
        // instancia de la bd
        $db = new DB();
        // conexion de la bd
        $db = $db->conectar();

        $stmt = $db->prepare($consulta);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':alias', $alias);
        $stmt->bindParam(':version', $version);

        $stmt->execute();

        echo '{"notice":"software agregado correctamente"}';
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    } catch (PDOException $e) {
        echo '{"error":{"text":'.$e->getMessage().'}}';
    }
});

/*
 *
 * RUTAS PARA COMPUTADORA-SOFTWARE
 *
 *
 * */

$app->post('/api/gestion/computadora/agregar-software', function(Request $request, Response $response) {
    $idComputadora = $request->getParam('idComputadora');
    $idSoftware = $request->getParam('idSoftware');

    $consulta = 'insert into Computadora_Software(id_computadora, id_software)
                 values(:idComputadora,:idSoftware);';

    try {
        // instancia de la bd
        $db = new DB();
        // conexion de la bd
        $db = $db->conectar();

        $stmt = $db->prepare($consulta);
        $stmt->bindParam(':idComputadora', $idComputadora);
        $stmt->bindParam(':idSoftware', $idSoftware);

        $stmt->execute();

        echo '{"notice":"software agregado a la pc correctamente"}';

        //$registro = 'select * from Computadora c where c.nombre = "'.$nombre.'";';
        //$ejecutar = $db->query($registro);
        //$computadoras = $ejecutar->fetchAll(PDO::FETCH_OBJ);

        //echo json_encode($computadoras);

        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    } catch (PDOException $e) {
        echo '{"error":{"text":'.$e->getMessage().'}}';
    }
});

?>