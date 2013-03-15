<?php

/**
 * Table for Correspondent
 */
class VideoVoices_Model_Correspondent_Table extends Zend_Db_Table_Abstract
{
	/**
	 * Name of the table
	 * @var	string
	 */
	protected $_name = 'correspondent';

	/**
	 * Name of the primary key column
	 * @var	string
	 */
	protected $_primary = 'id';


	public function getCorrespondents($startAt=1 , $perPage){
		
		$table = new self;
		$select = $table->select();
		$select->order('id DESC');
		$select->limitPage($startAt, $perPage);
		
		
		$result = $table->fetchAll($select);
		$returnValue = $result->toArray();
		
//		var_dump($returnValue);
//		die;

		return $returnValue;
	}
	
	public function getCorrespondentMin(){
		$table = new self;
		$select = $table->select();
		$select->from($table, array('id','name') );
		$select->order('id DESC');
		
		$result = $table->fetchAll($select);
		$returnValue = $result->toArray();
		
		return $returnValue;
	}
	

	
//NEED TO ADDRESS A WAY TO GET CALLBACK FROM THESE:

	public function addNewCorrespondent($data){
		$table = new self;
		$table->insert($data);
		
		return true;
	}

	public function updateCorrespondent($id , $data){
			$table = new self;
			$where = $table->getAdapter()->quoteInto('id = ?', $id);
			$table->update($data, $where);
			
			//$update = 
			//$result = $table->fetchAll($update);
			//$returnValue = $result->toArray();
			
			//var_dump($returnValue);
			//die;
	
			return true;//$returnValue;
	}
	
	public function deleteCorrespondent($id ){
		$table = new self;
		
		$where = $table->getAdapter()->quoteInto('id = ?',$id);
		$table->delete($where);
		
		//$result = $table->fetchAll();
		//$returnValue = $result->toArray();
		
		return true;//$returnValue;
	}
	
	
}
	
