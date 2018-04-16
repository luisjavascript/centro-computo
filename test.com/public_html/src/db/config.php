<?php

    class DB {
        private $host = 'localhost';
        private $usuario = 'root';
        private $password = 'root';
        private $base = 'computerCenter';

        // conectar a la base de datos
        public function conectar() {
            $con_mysql = "mysql:host=$this->host;dbname=$this->base";
            $con_DB = new PDO($con_mysql, $this->usuario, $this->password);
            $con_DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $con_DB->exec("set names utf8");

            return $con_DB;
        }
    }

?>