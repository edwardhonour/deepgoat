<?php
//-------------------------------------------------------------------
// Main database controller 
//-------------------------------------------------------------------
class XRDB {

	protected $dbh;
	protected $db;
	
	function connect() {
		$cs=file_get_contents("/var/www/vault/cs.json");
		$c=json_decode($cs,true);
		$this->dbh = new PDO($c['cs'], $c['un'], $c['pwd']);
		$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	
		return $this->dbh;
	}
	
	function query($table,$where="",$order="") {
		$db=$this->connect();
		$output=array();
		if ($table=="") {
			return $output;
		} else {
	
			$sql = "SELECT * from " . $table . " where 1 = 1 ";
			if ($where != "") $sql .= " and " . $where;
			if ($order != "") $sql .= " order by " . $order;
			$stmt = $db->prepare($sql);
			$stmt->execute();
			$output = array();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);	
			foreach ($results as $result) {
				$r = array();
				foreach ($result as $name => $value) {
				if ($name!="json") {
					$r[$name]=$value;
				} else {
					if ($value!="") {
						$resultJSON=json_decode($value,true);
						foreach ($resultJSON as $nameJSON => $valueJSON) $r[$nameJSON]=$valueJSON;					
					}
				}
			}
			array_push($output,$r);	
			
			}
			return $output;	
		}
	}
	
	function sql($s="") {
		$db=$this->connect();
		$output=array();
		if ($s=="") {
			return $output;
		} else {
			$stmt = $db->prepare($s);
			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);	
			return $results;	
		}
	}

	function sql0($s="") {
		$db=$this->connect();
		$output=array();
		if ($s=="") {
			return '0';
		} else {
			$stmt = $db->prepare($s);
			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);	
			return $results[0];	
		}
	}

	function sqlC($s="") {
		$db=$this->connect();
		$output=array();
		if ($s=="") {
			return 0;
		} else {
			$stmt = $db->prepare($s);
			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);	
			return $results[0]['c'];	
		}
	}
	
	function execute($s) {
		$db=$this->connect();
		$stmt = $db->prepare($s);
		$stmt->execute();
	}	

	function update($s,$p) {
		$db=$this->connect();
		$stmt = $db->prepare($s);
		$stmt->bindParam(1, $p);		
		$stmt->execute();
	}	
	
	function isTableColumn($name,$columns) {
		$result=false;
		foreach ($columns as $column) {
			if ($name==$column['Field']) {
				$result=true;
			}
		}
		return $result;
	}
	
	function post($POST) {	
	print_r($POST);
        if (isset($POST['TABLE_NAME'])) $POST['table_name']=$POST['TABLE_NAME'];
		$db=$this->connect();
		$output=array();
		if (!isset($POST['action'])) $POST['action']="insert";
		if (!isset($POST['id'])) $POST['id']="";
		if (isset($POST['ID'])) $POST['id']=$POST['ID'];
		if (!isset($POST['table_name'])) 
		{
			$output['result']='Failed';
        } else
		{
		$sql = "SHOW COLUMNS FROM " . $POST['table_name'];
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);	

		if ($POST['action']!="delete") {
				// If there is not 'id' value, record is inserted
				if ($POST['id']==""||$POST['id']=="0") {
					//-- all tables have id and create_date as the minimum columns
					$sql = "insert into " . $POST['table_name'] . " (create_timestamp) values (now())";
					echo $sql;
					$stmt = $db->prepare($sql);
					$stmt->execute();				
					//-- put the id in $_POST['id'] so it can be used to process the rest of the columns
					$POST['id'] = $db->lastInsertId();
					$output['result']="insert";
				} else {
					$output['result']="update";					
				}

				$json=array();	

				foreach ($POST as $name => $value) {
					if ($name!="id"&&$name!="create_date"&&$name!="table_name"&&$name!="action") {
						//-- if column is in the table update it, otherwise add it to the $json array.
						if ($this->isTableColumn($name,$columns)) {
							if ($name=="event_date"||$name=="target_start_date"||$name=="target_end_date") {
									$sql = "update " . $POST['table_name'] . " set " . $name . " = STR_TO_DATE(?, '%m/%d/%Y') where id = ?";	
									$stmt = $db->prepare($sql);
									$stmt->bindParam(1, $value);
									$stmt->bindParam(2, $POST['id']);
									$stmt->execute();
							} else {
								if ($value=="now()") {
									$sql = "update " . $POST['table_name'] . " set " . $name . " = now() where id = ?";
									$stmt = $db->prepare($sql);
									$stmt->bindParam(1, $POST['id']);
									$stmt->execute();
								} else {
										$sql = "update " . $POST['table_name'] . " set " . $name . " = ? where id = " . $POST['id'];;
										$stmt = $db->prepare($sql);
										$stmt->bindParam(1, $value);
										try {
										$stmt->execute();
}
catch(Exception $e) {
  echo 'Message: ' .$e->getMessage();
}

							    }
							}
					

						} else $json[$name] = $value;
					}
				}	
			} 
			else {
//				$sql = "delete from " . $POST['table_name'] . " where id = ?";
//				$stmt = $db->prepare($sql);
//				$stmt->bindParam(1, $POST['id']);
//				$stmt->execute();
//				$output['result']="update";
			}	
		}
		return $POST['id'];
	}
}

