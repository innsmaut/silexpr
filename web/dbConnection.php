<?php



class dbConnection {
    
    private $cfg;
    private $conn;
    private $table;
    private $query;
    public $errs;
    public $result;

    public function __construct()
    {
            $this->cfg = require (__DIR__.'\config.php');
            $this->cfg = $this->cfg['db'];
            $this->conn = new PDO($this->cfg['pdostring'], $this->cfg['username'], $this->cfg['password']);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->table = $this->cfg['table'];
    }
    
    public function dbWrite(array $item){
        $this->query = "INSERT INTO {$this->table} (claimed_link, redirect_link, password, expired_on)
                        VALUES ('{$item['claimedLink']}', 
                                '{$item['redirectLink']}', 
                                '{$item['password']}', 
                                '{$item['expiredOn']}')";
        $this->conn->exec($this->query);
    }

    public function dbRead($item){
        $this->query = "SELECT * FROM {$this->table}";
        if (isset($item['claimedLink'])){
            $this->query .= " WHERE (redirect_link = '{$item['claimedLink']}')";
        }
        try{
            $this->result = $this->conn->query($this->query)->fetchAll(PDO::FETCH_ASSOC);
            if($this->result){
                foreach ($this->result as $res => $entry){
                    $this->errs = $this->validate($item, $entry);
                    if ($this->errs !== 'validated'){
                        unset($this->result[$res]);
                    }
                }
                if ($this->result !== []){
                    return $this->result;
                } else {
                    return $this->errs;
                }
            } else {
                return ['error' => 'There is no appropriate link in database!'];
            }
        }
        catch (PDOException $e){
            return ['error' => $e->getMessage()];
        }
    }

    private function validate($item, $entry){
        if ((isset($item['password']))&&(isset($entry['password']))&&($entry['password'] != $item['password'])) {
            return ['error' => 'Bad password!'];
        }
        elseif (($entry['expired_on'] !== '0')&&(date_timestamp_get(date_create()) >= $entry['expired_on'])){
            return ['error' => 'Usage time expired at '.date_timestamp_set(date_create(), $entry['expired_on'])
                    ->format("Y-m-d H:i:s").'!'];
        } else {
            return 'validated';
        }
    }
}