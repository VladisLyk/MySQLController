<?php
require_once "query.php";
class MySQL {
    private $host;
    private $user;
    private $password;
    private $port;
    private $database;
    private $connection;

    function __construct($host, $user, $password, $port, $database)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->port = $port;
        $this->database = $database;

        $this->connection = new mysqli($host, $user, $password, $database, $port);
        $this->connection->set_charset("UTF-8");
        try {
            if($this->connection->connect_errno) {
                throw new Exception("Failed connection to MySQL. Error: " . $this->connection->connect_error);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    private function getConnection(): mysqli
    {
        return $this->connection;
    }

    public function createQuery() {
        return new Query($this->getConnection());
    }
}

function protectedString($connection, $string) {
    return $connection->real_escape_string($string);
}
function buildQuery($connection, $table,...$vars) {
    $count_vars = count($vars);
    $set = "";

    for ($i=0; $i < $count_vars; $i++) { 
            foreach ($vars[$i] as $key => $value) {
                $key = protectedString($connection, array_keys($value)[0]);
                $value = protectedString($connection,$value[array_keys($value)[0]]);

                if($i < $count_vars-1) {
                    $set .= "`" . $key . "` = '" . $value . "', ";   
                } else {
                    $set .= "`" . $key . "` = '" . $value . "'";   
                }
            }
    }

    return $set;
}
