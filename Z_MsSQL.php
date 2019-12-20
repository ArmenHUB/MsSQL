<?php/** * @author     Armen Manukyan * @brief      Class designed for work with MsSQL */namespace core\db;error_reporting(E_ALL);require_once "engine/config/db.php";class Z_MsSQL{    private $connection;    private $dsn;    private $last_error;    /**     * Z_MsSQL constructor.     * @brief connect to MsSQL     */    function __construct()    {        $this->last_error = 0;        try {            $this->connection = new PDO($this->getDSN(), DB_USER, DB_PASS, [PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION]);        } catch (PDOException $e) {            $this->last_error = "Error connecting to SQL Server:" . $e->getMessage();        }        return $this->connection;    }    /**     * Return DSN, DSN contains the mssql driver name     * @return string     */    private function getDSN(){        //other driver PDO_DBLIB        // $this->dsn = "mssql:host=" . DB_HOST . ";dbname=" . DB_NAME . "";        // driver PDO_SQLSRV        $this->dsn = "sqlsrv:Server=" . DB_HOST . ";Database=" . DB_NAME . "";        return $this->dsn;    }    /**     *  Z_MsSQL destructor.     * @brief disconnect from Z_MsSQL     */    function __destruct()    {        $this->connection=null;    }    /**     * For no-DML query (not INSERT, UPDATE or DELETE)     * @param   $query   String  Query string     * @return  array  sql answer     */    protected function queryNoDML(string $query): array    {        $result = $this->connection->prepare($query);        $result->execute();        $answer = [];        if ($result) {            if ($result->fetchColumn() > 0) {                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {                    $answer[] = $row;                }            }            return $answer;        }        return $answer;    }    /**     * For DML query (not INSERT, UPDATE or DELETE)     * @param $query      String  Query string     * @return bool|mssql_result     */    protected function queryDML(string $query)    {        $result = $this->connection->prepare($query);        $result->execute();        if ($result->rowCount() > 0) {            return $result->rowCount();        }        return false;    }    /**     * Return last error     * @return string     */    protected function lastError()    {        return $this->last_error;    }    /**     * Return last inserted id from MySQL     * @return  int|bool     */    protected function lastID()    {        return $this->connection->lastInsertId();    }    /**     * Return current date and time from MySQL     * @return  bool|mysqli_result     */    protected function getNow()    {        return $this->queryNoDML("SELECT NOW() AS 'time';")[0]['time'];    }}