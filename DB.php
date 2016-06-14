<?php
/*
 *  +----------------------------------------------------------------------
 *  | The author ( longmsdu )
 *  +----------------------------------------------------------------------
 *  | Licensed ( http://www.long.m/ )
 *  +----------------------------------------------------------------------
 *  | Author: longmsdu <770777657@qq.com>
 *  +----------------------------------------------------------------------
 */
/**
 * class:db
 *
 * @author longmsdu
 */
class DB {
    private $pdo=null;
    private $config=[
        'host'=>'127.0.0.1',
        'name'=>'test',
        'pre'=>'',
        'user'=>'root',
        'pass'=>'123456',
        'charset'=>'utf8',
        'debug'=>true
    ];

    public function __construct(){
        try{
            $this->pdo=new \PDO('mysql:host='.$this->config['host'].';dbname='.$this->config['name'],$this->config['user'],$this->config['pass']);
            $this->pdo->exec('SET NAMES \''.$this->config['charset'].'\';');
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }catch (\PDOException $e){
            return false;
        }
    }

    public function select($table, $data, $condition) {
        if (null == $table || null == $data || null == $condition) {
            return false;
        }
        $sql = "SELECT ".implode(',', $data)." FROM ".$table." WHERE ".implode(' AND ', $condition);
        $statement = $this->pdo->query($sql);

        return $statement->fetchAll();
    }

    public function insert($table, $data) {
        if (null == $table || null == $data) {
            return false;
        }
        $sql = "INSERT INTO ".$table." (".implode(',', array_keys($data)).") "."VALUES "." (".implode(',', array_values($data)).")";

        return $this->pdo->exec($sql);

    }

    public function update($table, $data, $condition) {
        if (null == $table || null == $data || null == $condition) {
            return false;
        }
        $sql = "UPDATE ".$table." SET ".implode(' AND ', $data)." WHERE ".implode(' AND ', $condition);

        return $this->pdo->exec($sql);
    }

    public function delete($table, $condition) {
        if (null == $table || null == $condition) {
            return false;
        }
        $sql = "DELETE FROM ".$table." WHERE ".implode(' AND ', $condition);

        return $this->pdo->exec($sql);
    }
}
