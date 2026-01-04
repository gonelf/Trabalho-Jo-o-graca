<?php
/**
 * Classe de Conexão à Base de Dados
 * Usa PDO para comunicação com MySQL
 */

class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $charset = DB_CHARSET;
    private $conn = null;

    /**
     * Estabelece conexão com a base de dados
     * @return PDO|null
     */
    public function getConnection() {
        if ($this->conn === null) {
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];

                $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            } catch(PDOException $e) {
                error_log("Connection error: " . $e->getMessage());
                throw new Exception("Erro de conexão à base de dados");
            }
        }

        return $this->conn;
    }

    /**
     * Fecha a conexão
     */
    public function closeConnection() {
        $this->conn = null;
    }

    /**
     * Inicia uma transação
     */
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }

    /**
     * Confirma uma transação
     */
    public function commit() {
        return $this->conn->commit();
    }

    /**
     * Reverte uma transação
     */
    public function rollback() {
        return $this->conn->rollback();
    }
}
