<?php



class dbConnection {
    
    private $conn;
    private $table;
    private $query;
    private $expired_on;
    public $errs;
    public $result;

    public function __construct(array $cfg)
    {
        $this->conn = new PDO($cfg['pdostring'], $cfg['username'], $cfg['password']);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->table = $cfg['table'];
    }
    
    public function dbWrite(array $item){
        $this->expired_on = date_timestamp_get(date_add(date_create(),
                            date_interval_create_from_date_string($item['expired_on'].' minutes')));
        $this->query = "INSERT INTO {$this->table} (claimed_link, redirect_link, password, expired_on)
                        VALUES ('{$item['claimdedLink']}', 
                                '{$item['redirectLink']}', 
                                '{$item['password']}', 
                                '{$this->expired_on}')";
        $this->conn->exec($this->query);
    }

    public function dbRead($item){
        $this->query = "SELECT * FROM {$this->table} WHERE (redirect_link = '{$item['claimedLink']}')";
        try{
            if($this->result = $this->conn->query($this->query)->fetchAll(PDO::FETCH_ASSOC)){
                $this->result = $this->result[0];
            } else {
                return ['error' => 'There is no such link in database!'];
            }
        }
        catch (PDOException $e){
            return ['error' => $e->getMessage()];
        }
        $this->errs = $this->validate($item);
        if ($this->errs === false){
            return $this->result;
        } else{
            return $this->errs;
        }
    }

    private function validate($item){
        if ((isset($item['password']))&&($this->result['password'] != $item['password'])) {
            return ['error' => 'Bad password!'];
        }
        elseif (date_timestamp_get(date_create()) >= $this->result['expired_on']){
            return ['error' => 'Usage time expired!'];
        } else {
            return false;
        }
    }
}