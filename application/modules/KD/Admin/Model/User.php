<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Admin_Model_User extends KD_Admin_Model_User_Collection
{
    protected $_name = 'admin_user';
	protected $_pk = 'user_id';
	protected $_tableField = array();

    public function __construct()
    {
        $this->_init('admin/user','user_id');
    }
    
    public function load($id, $field=null)
    {
        // If id is not set that show 404 page not found page
        if (is_null($id)) {
            //return $this->noRoutePage();
        }
//        parent::load($id,$field);
//        //$model = new User_Model_DbTable_User();
//        //$model->getAlbum($id);
//        //$row = $this->fetchRow('id = ' . $id);
//        echo '<pre>';
//        print_r($model);exit();
        return parent::load($id,$field);
    }
    public function loadAll()
    {
        return parent::loadAll();
    }
    
    public function getUserDetail($id)
    {
        if((int)$id>0)
        {
            return parent::getUserDetail($id);
        }
        else
        {
            KD::throwException('Empty identifier field name');
        }
    }
	public function insert(array $dataPost)
    {
		foreach($_tableField as $cnt=>$column)
		{
			if(isset($dataPost[$column]))
			{
				$data[$column] = $dataPost[$column];
			}
			else
			{
				$data[$column] = '';
			}
		}
        return parent::insert($data);
    }

}