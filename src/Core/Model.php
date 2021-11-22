<?php
namespace App\Core;

use Exception;

class Model{
    protected $pdo;
    public function __construct(&$pdo){
    $this->pdo = $pdo;
    }
    public function getConnection():\PDO{
        return $this->pdo;
    }
    protected function getFields():array{
        return [];
    }
  
    final private function getTableName():string{
        $fullName = static::class;
        preg_match('|^.*\\\((?:[A-Z][a-z]+)+)Model$|',$fullName,$matches);
        return substr(strtolower(preg_replace('|[A-Z]|','_$0',$matches[1] ?? '')),1);
    }
     
  
    public function getAll():array {
        $tableName= $this->getTableName();
        $sql = "select * from `". $tableName."`";
       
        $stmt= $this->pdo->prepare($sql);
        $res= $stmt->execute();
        $result=[];
        if($res){
            $result = $stmt->fetchAll(\PDO::FETCH_OBJ);
        }
        return $result;
    }
    public function getById(int $id){
        $tableName= $this->getTableName();
        $sql = "select * from `".  $tableName ."` where ".$tableName. "_id = ?";
        $stmt= $this->pdo->prepare($sql);
        $res= $stmt->execute([$id]);
        if($res){
            $table = $stmt->fetch(\PDO::FETCH_OBJ);
        }
        return $table;
    }
    final private function isFieldValueValid(string $fieldName, $fieldValue) :bool{
       $fields= $this->getFields();
       $supportedFieldNames = array_keys($fields);

        if(!in_array($fieldName, $supportedFieldNames)) return false;

       return $fields[$fieldName]->isValid($fieldValue);
    }

    public function getAllByFieldName(string $fieldName ,$value){
        if(!$this->isFieldValueValid($fieldName,$value)){
            throw new Exception('Invalid field name or value: '.$fieldName);
        }
        $tableName= $this->getTableName();
        $sql = "SELECT * from `".  $tableName ."` where ".$fieldName. " = ?";
        
        $stmt= $this->pdo->prepare($sql);
       
        $res= $stmt->execute([$value]);
        $items=[];
        if($res){
            $items = $stmt->fetchAll(\PDO::FETCH_OBJ);
        }
        return $items;
    }

    public function getByFieldName(string $fieldName, $value){
        if(!$this->isFieldValueValid($fieldName, $value)){
            throw new Exception('Invalid field name or value: '.$fieldName);
        }
        $tableName= $this->getTableName();
        $sql = "select * from `".  $tableName ."` where ".$fieldName. " = ? limit 1";
       
        $stmt= $this->pdo->prepare($sql);
        $res= $stmt->execute([$value]);
        $item=null;
        if($res){
            $item = $stmt->fetch(\PDO::FETCH_OBJ);
        }
       
        return $item;
    }
    
    final private function checkFieldList(array $data){
        $fields=$this->getFields();
        //check keys
        $supportedFieldNames = array_keys($fields);
        $requestedFieldNames = array_keys($data);
        //check values
        foreach($requestedFieldNames as $requestedFieldName){
            if(!in_array($requestedFieldName, $supportedFieldNames)){
                throw new \Exception("Field ". $requestedFieldName .' is not supported');
            }

            if(!$fields[$requestedFieldName]->isEditable()){
                throw new \Exception("Field ". $requestedFieldName .' is not editable');
            }
           
            if(!$fields[$requestedFieldName]->isValid($data[$requestedFieldName])){
             
               
                throw new \Exception("The value for the field ". $requestedFieldName .' is not valid');
            }
        }
    }
    final public function editById(int $id, array $data){
        $this->checkFieldList($data);
        

        //update
        $tableName=$this->getTableName();
        $editList=[];
        $values=[];
        foreach($data as $fieldName =>$value){
            $editList[]= "{$fieldName} = ?";
            $values[]=$value;
        }
        $editString = implode(' , ', $editList);
        $values[]= $id;
        $sql= "update {$tableName} set {$editString} where {$tableName}_id = ?";
        $stmt = $this->pdo->prepare($sql);
     
        $stmt->execute($values);
        return $stmt->rowCount();
    }
    final public function add(array $data){
       
       $this->checkFieldList($data);
        

        //insert into
       
        $tableName=$this->getTableName();
      
      
        $sqlFieldNames = implode(', ', array_keys($data));
        $questionMarks = substr(str_repeat('?,',count($data)),0,-1);
        $sql = "insert into {$tableName}($sqlFieldNames) VALUES ($questionMarks)";
       
        $stmt= $this->pdo->prepare($sql);
      
        //execute prepare stmt
      
        $res= $stmt->execute(array_values($data));
     
        if(!$res){
            return false;
        }
        return $this->pdo->lastInsertId();
    }
    public function deleteById(int $id){
        $tableName= $this->getTableName();
        $sql = "delete from ".  $tableName ." where ".$tableName. "_id = ?";
        $stmt= $this->pdo->prepare($sql);
       return $stmt->execute([$id]);
    }
}