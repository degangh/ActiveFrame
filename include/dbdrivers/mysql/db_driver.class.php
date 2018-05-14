<?php
/**
 * @author Jacky Jiang
 * @version 0.1.1
 * Only support UTF-8
 */


if(!defined('IN_APP')) {
	exit('Access Denied');
}
include APP_ROOT.'include/CActiveRecord.class.php';
include APP_ROOT.'exceptions/CDBException.php';
class dbstuff {
	var $querynum = 0;
	var $link;
	var $query;
	
	private $dbhost;
	private $dbuser;
	private $dbpw;
	private $dbname;
	private $pconnect;
	
	function connect($dbhost, $dbuser, $dbpw, $dbname = '', $pconnect = 1, $halt = TRUE) {
		$this->dbhost=$dbhost;
		$this->dbuser=$dbuser;
		$this->dbpw=$dbpw;
		$this->dbname=$dbname;
		$this->pconnect=$pconnect;
		if($pconnect) {
			if(!$this->link = mysqli_connect("p:".$dbhost, $dbuser, $dbpw)) {
				$halt && $this->halt('Can not connect to MySQL server aha');
			}
		} else {
			if(!$this->link = mysqli_connect("p:".$dbhost, $dbuser, $dbpw)) {
				$halt && $this->halt('Can not connect to MySQL server ooloo');
			}
		}
		if($this->version() > '4.1') {
			$dbcharset='utf8';
			if($dbcharset) {
				@mysqli_query("SET character_set_connection=$dbcharset, character_set_results=$dbcharset, character_set_client=binary", $this->link);
			}
			if($this->version() > '5.0.1') {
				@mysqli_query("SET sql_mode=''", $this->link);
			}
		}

		if($dbname) {
			mysqli_select_db($this->link, $dbname);
		}
	}

	function select_db($dbname) {
		return mysqli_select_db($this->link, $dbname);
	}

	function fetch_array($query, $result_type = MYSQL_ASSOC) {
		return mysqli_fetch_assoc($query);
	}
	
	function real_escape_string($str)
	{
		return mysqli_real_escape_string($this->link, (string)$str);
	}
	
	function query($sql, $params=null, $type = '') 
	{
		global $APP_ENV;
		//mysql_unbuffered_query followed by mysqli_use_result
		//unbuffered
		$func = $type == 'UNBUFFERED' && @function_exists('mysqli_unbuffered_query') ? 'mysql_unbuffered_query' : 'mysqli_query';
		if(!empty($params) && is_array($params)) {
			foreach($params as $k=>$v)
			{
				if($v===NULL) $params[$k]='NULL';
				else $params[$k]='\''.mysqli_real_escape_string($this->link, (string)$v).'\'';
			}	
			$sql=str_replace(array_keys($params),array_values($params),$sql);
		}
		if(!($query = $func($this->link, $sql))) {
			if(in_array($this->errno(), array(2006, 2013)) && substr($type, 0, 5) != 'RETRY') {
				$this->close();
				$this->connect($this->dbhost,$this->dbuser,$this->dbpw, $this->dbname, $this->pconnect);
				$query = $this->query($sql, $params, 'RETRY'.$type);
			} elseif($type != 'SILENT' && substr($type, 5) != 'SILENT') {
				$this->halt('MySQL Query Error', $sql);
			}
		}

		$this->querynum++;
		if($APP_ENV['debug']) 
		{
			$APP_ENV['debugInfo']['curSql']=$sql;
			$APP_ENV['debugInfo']['sqlStack'][]=$sql;
		}
		$this->query=$query;
		$this->error();
		return $query;
	}

	function affected_rows() {
		return mysqli_affected_rows($this->link);
	}

	function error() {
		return (($this->link) ? mysqli_error($this->link) : mysqli_error());
	}

	function errno() {
		return intval(($this->link) ? mysqli_errno($this->link) : mysqli_errno());
	}

	function result($query, $row) {
		$query = @mysqli_result($query, $row);
		return $query;
	}

	function num_rows($query=NULL) {
		if(!isset($query)) $query=$this->query;
		$query = mysqli_num_rows($query);
		return $query;
	}

	function num_fields($query) {
		return mysqli_num_fields($query);
	}

	function free_result($query) {
		return mysqli_free_result($query);
	}

	function insert_id() {
		return ($id = mysqli_insert_id($this->link)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
	}

	function fetch_row($query) {
		$query = mysqli_fetch_row($query);
		return $query;
	}

	function fetch_fields($query) {
		return mysqli_fetch_field($query);
	}

	function version() {
		return mysqli_get_server_info($this->link);
	}

	function close() {
		return mysqli_close($this->link);
	}

	function halt($message = '', $sql = '') 
	{
		global $APP_ENV;
		define('CACHE_FORBIDDEN', TRUE);
		throw new CDBException($message.":\n<br/>".$this->error(),$this->errno(),$sql);
	}
	
	/*
	$table: table name should insert records into
	$record: array contains name- value pair
	
	*/
	
	function insert($table,$record)
	{
		$sql="INSERT INTO $table (".implode(', ', array_keys($record)).") VALUES ('".implode('\', \'', array_values($record))."')";
		return $this->query($sql);
	}
	
		/*
	$table: table name should insert records into
	$record: array contains name- value pair
	
	*/
	
	function update($table,$record,$where)
	{
		foreach($values as $key => $val)
		{
			$valstr[] = $key." = ".$val;
		}		
		$sql="UPDATE $table SET ".implode(', ', $valstr)." WHERE $where;";
		return $this->query($sql);
	}
	
	
	function fetchResultBySQL($sql,$params=null,$arg1=null,$arg2=null)
	{
		$limtText='';
		if(isset($arg2)) $limtText=" LIMIT {$arg1},{$arg2}";	
		elseif (isset($arg1)) $limtText=" LIMIT {$arg1}";
		$sql.=$limtText;
		$query=$this->query($sql,$params);
		if($query===false) return false;
		while($row=mysqli_fetch_assoc($query)) $records[]=$row;
		if($records==false) $records=array();
		return $records;
	}
	
	
	/*
	Get the Collection object from a table 
	Only works on table has a primary key with the name id
	
	*/
	function oQuery($sql)
	{
		$r=$this->query($sql);
		$table=mysqli_field_table($r,0);
		return new CCollection($table,$r);
	}
	
	function fetchSmartRecord($query)
	{
		if($query===false) return false;
		$table=mysqli_field_table($query,0);
		$row=mysqli_fetch_assoc ($query);
		if($row===false) return false;
		return new CSmartRecord($table,$row);
	}
	function fetchSmartRecordById($table,$id)
	{
		$query=$this->query("select * from $table where id='$id' limit 1");
		if($query===false) return false;
		$table=mysqli_field_table($query,0);
		$row=mysqli_fetch_assoc ($query);
		if($row===false) return false;
		return new CSmartRecord($table,$row);
	}
	
	function activeRecordQuery($sql)
	{
		$query=$this->query($sql);
		if($query===false) return false;
		$num=mysqli_num_fields($query);
		$tableSet=array();
		
		for($i=0;$i<$num;$i++)
		{
			mysqli_field_seek ($query,$i);
			$obj=mysqli_fetch_field($query,$i);
			$tableSet[$obj->table]['collist'][$obj->name]=array(
													'default' => $obj->def,
													'primary_key' => $obj->primary_key);
		}
		foreach($tableSet as $key => $value)
		{
			foreach($value['collist'] as $k => $v)
			{
				if($v['primary_key']==1)
				{
					$tableSet[$key]['primarykeyName']=$k;
				}
				
			}
			if(!isset($tableSet[$key]['primarykeyName'])) throw new RuntimeException('Can\'t load data set without primary key from table:'.$key);
		}
		$records=array();
		while($row=mysqli_fetch_assoc($query)) $records[]=new CActiveRecord($tableSet,$row);
		return $records;
	}
}

?>