<?php
class Query {
    private static $connection;
    function __construct($connection) {
        $this->connection = $connection;
    }
    public function tableExists($tableName) {
        $query = $this->connection->query("SHOW TABLES LIKE '{$this->connection->real_escape_string($tableName)}'");
        $rows = $query->num_rows;

        return ($rows > 0);
    }
    public function columnExists($column, $table) {
        try {
            if(!$this->tableExists($table)) {
                throw new Exception("columnExists() -> TABLE NOT FOUND!");
            }

            $table = protectedString($this->connection, $table);
            $column = protectedString($this->connection, $column);

            $query = $this->connection->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '{$table}' AND COLUMN_NAME = '{$column}'");

            return $query->num_rows > 0;
        } catch(Exception $e) {
            echo $e->getMessage();
        }

    }

    public function lineExists($table, $param) {
        try {
            if(!$this->tableExists($table)) {
                throw new Exception("lineExists() -> TABLE NOT FOUND!");    
            }
            if(!is_array($param) || !(count($param) == 1)) {
                throw new Exception("lineExists() -> CHECK PARAMS");       
            }

            $table = protectedString($this->connection, $table);
            $id = protectedString($this->connection, array_keys($param)[0]);
            $value  = protectedString($this->connection, $param[array_keys($param)[0]]);

            $query = $this->connection->query("SELECT * FROM `{$table}` WHERE `{$id}` = '{$value}'");
            $result = true;

            if($query->fetch_assoc() == null) {
                $result = false;
            }

            return $result;
            
        } catch(Exception $e) {
            echo $e->getMessage();
        }

    }
    

    public function select($id, $table) {

        try {
            if($table == null || !$this->tableExists($table)) {
                throw new Exception("select() -> TABLE NOT FOUND!");
            }
            $table = protectedString($this->connection, $table);
            $id = protectedString($this->connection, $id);

            return $this->connection->query("SELECT * FROM `{$table}` WHERE `id` = '{$id}'")->fetch_assoc();
        } catch(Exception $e) {
            echo $e->getMessage();
        }

    }

    public function update($table,$id,...$vars) {
        try {
            if($table == null || !$this->tableExists($table)) {
                throw new Exception("update() -> TABLE NOT FOUND!");
            }
            foreach ($vars as $key => $value) {
                if(!is_array($value) || !(count($value) > 0) || !$this->columnExists(array_keys($value)[0], $table)) {
                    throw new Exception("update() -> CHECK UPDATE PARAMS!");
                }
            }
            if(!is_array($id) || count($id) > 1 || !$this->columnExists(array_keys($id)[0], $table)) {
                throw new Exception("update() -> CHECK ID PARAMS!");
            }

            $table = protectedString($this->connection, $table);
            $update = buildQuery($this->connection, $table, $vars);
            
            $where_id = protectedString($this->connection, array_keys($id)[0]);
            $where_value = protectedString($this->connection, $id[array_keys($id)[0]]);

            if(!$this->lineExists($table, $id)) {
                throw new Exception("update() -> LINE DOES NOT EXISTS!");
            }

            return $this->connection->query("UPDATE `{$table}` SET {$update} WHERE `{$where_id}` = '{$where_value}'");
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
    
}


?>
