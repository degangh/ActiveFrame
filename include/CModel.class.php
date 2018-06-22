<?php
/**
 * @author Nicolas Huang
 * @version 1.0.1
 */
abstract class CModel 
{
	protected $data = array();
	protected $fillable = array();
	protected $table;
	protected $pk;
	

	public function __construct($id = null)
	{
		if (!$this->table) $this->table= camelCaseToUnderscore($this->getClassName(get_class(($this))));
		$this->init();

		if ($id) $this->find($id);
	}

	public function __get($name)
	{
		if (key_exists($name, $this->data)) return $this->data[$name];
		else throw new Exception("Bazinga");
	}

	public function __set($name, $value)
	{
		if (key_exists($name, $this->data)) $this->data[$name] = $value;
	}

	private function init()
	{
		$s = new CSearcher($this->table);
		$s->fetchResult(1);
		$tableSet = $s->fields();
		
		foreach ($tableSet[$this->table]['collist'] as $key => $value) $this->data[$key] = null;

		$this->pk = $tableSet[$this->table]['primarykeyName'];

	}

	public function save()
	{
		if ($this->data[$this->pk] == null)
		{
			//new
			$s = new CActiveRecord($this->table);

			foreach ($this->data as $k => $v )	if ($k != $this->pk && $v != null) $s[$k] = $v;

			$s->save();

			$this->data[$this->pk] = $s->insert_id();
		}
		else
		{
			//update
			$s = new CSearcher($this->table);
			$s[$this->pk] = $this->data[$this->pk];
			$r = $s->fetchResult();
			$r = $r[0];

			foreach ($this->data as $k => $v )	if ($k != $this->pk) $r[$k] = $v;

			$r->save();

		}
	}

	public function create($dataSet)
	{

	}

	private function find($id)
	{
		$s = new CSearcher($this->table);
		$s[$this->pk] = $id;
		$r = $s->fetchResult();

		$r = $r[0];

		if (count($r) < 1) throw new Exception("Retrive record error");

		foreach ($r as $k => $v) $this->data[$k] = $v;
	}

	public function getClassName($full_name) {
        $path = explode('\\',$full_name);
        return array_pop($path);
    }

}





?>