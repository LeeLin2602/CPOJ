<?php

function checkString($strings, $standard){
   if(preg_match($standard, $strings)) {
      return true;
   } else {
      return false;
   }
}

class MySQL
{
	protected $sql_connection;

	function __construct() {
    	$this->sql_connection = new mysqli('localhost', 'database', 'passwd', 'oj');
    	if ($this->sql_connection->connect_errno) {
    		throw new Exception("Database connection failed.");
		}
  	}

  	function __destruct() {
  		$this->sql_connection->close();
  	}

    public function update($table, $keys, $values, $where_clause = "") {
        if(count($keys) != count($values)){
          throw new Exception("Length of keys not equals to that of values. ". count($keys). ",". count($values));
        }
        $mysql_code = "UPDATE $table SET ";
        for ($i=0 ; $i<count($keys) ; $i++ ) {
          $key = $keys[$i];
          $value = $values[$i];
          $mysql_code .= $key . "=";
          if(is_string($value)){
           $mysql_code .= "'" . ($value) . "',";
          } else {
           $mysql_code .= strval($value) . ",";
          }
        }
        $mysql_code = substr($mysql_code, 0, -1) . " ";
        if($where_clause != ""){
          $mysql_code .= "WHERE " . $where_clause;
        }
        $mysql_code .= ";";
        if (!$this->sql_connection->query($mysql_code)) {
          throw new Exception("Updating failed.\n". $mysql_code);
        }
    }

    public function insert($table, $keys, $values) {
        if(count($keys) != count($values)){
        	throw new Exception("Length of keys not equals to that of values. ". count($keys). ",". count($values));
        }
        $mysql_code = "INSERT INTO $table (";
        foreach($keys as $key){
        	$mysql_code .= $key . ",";
        }

        $mysql_code = substr($mysql_code, 0, -1) . ") VALUES (";
        foreach($values as $value){
          if(is_string($value)){
            $value = mb_convert_encoding($value, "UTF-8");
        	 $mysql_code .= "'" . ($value) . "',";
          } else {
           $mysql_code .= strval($value) . ",";
          }
        }
        $mysql_code = substr($mysql_code, 0, -1) . ");";
        
	try { $this->sql_connection->query($mysql_code);}
	catch(Exception $e){
              die($e ."\nInserting failed.". $mysql_code);
    	}
    }
    public function delete($table,$where_clause){
        $mysql_code = "DELETE FROM $table WHERE " . $where_clause . ";";
        if($result = $this->sql_connection->query($mysql_code)){
          return $result;
        } else {
          throw new Exception("Delete failed." . $mysql_code);
        }
    }
    public function query($code){
      $code = mb_convert_encoding($code, "UTF-8");
      print($code);
    	print($this ->sql_connection -> query($code));
    }
    public function select($table, $where_clause = "", $order = "", $limit = -1, $offset = 0) {
      try {
        $mysql_code = "SELECT *  FROM $table ";
        if($where_clause != ""){
          $mysql_code .= "WHERE " . $where_clause;
        }
        if($order != ""){
          $mysql_code .= " ORDER BY " . $order;
        }
        if($limit != -1){
          $mysql_code .= " LIMIT " . strval($limit);
        }
        if($offset != 0){
          $mysql_code .= " OFFSET " . strval($offset);
        }
        $mysql_code .= ";";

        if($result = $this->sql_connection->query($mysql_code)){
          return $result;
        } else {
          throw new Exception("Selecting failed.");
        }
      } catch(Exception $e){
        die('Caught exception:' . $e . "\n$mysql_code");
      }
    }

}
?>
