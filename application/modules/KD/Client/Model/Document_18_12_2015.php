<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Client_Model_Document extends KD_Client_Model_Document_Collection
{
    protected $_name = 'document';
	
	const MAX_DIMENSJON_Y = 230; // Piksler
	const MAX_DIMENSJON_X = 226; // Piksler

	protected $_tableField = array('document_patientID'=>false,'document_userID'=>false,'document_deptID'=>false,'document_type'=>false,'document_filename'=>false,'document_mimetype'=>false,'document_filecontent'=>false,'document_filesize'=>false,'document_imagewidth'=>false,'document_imageheight'=>false,'date_of_creation'=>false,'date_of_modification'=>false,'created_by'=>false,'modified_by'=>false);
    public function __construct()
    {
        $this->_init('client/document','document_id');
    }
 
    public function load($id, $field=null, $_tableField=array())
    {
        // If id is not set that show 404 page not found page
        if (is_null($id)) {
            //return $this->noRoutePage();
        }

        return parent::load($id,$field,$this->_tableField);
    }  
	
	public function getDocument($documentId) { 
        if ($documentId >0)
		{
        	$result = $this->load($documentId);
			/*if(in_array($result['document_mimetype'],array('image/png','image/jpg','image/jpeg','image/gif')))
			{
				return '<img src= "data:'.$result['document_mimetype'].';base64,'.base64_encode($result['document_filecontent']).'" />';
			}*/
			return $result;//['document_mimetype'].';base64,'.$result['document_filecontent'];
		}
		return false;
    }
	
    public function loadAll($_tableField=array())
    {
        return parent::loadAll($this->_tableField);
    } 
	public function checkDocument($patientId = 0,$document_type='')
	{
		if($patientId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('d'=>'document'),array('document_id','document_patientID','document_userID','document_deptID','document_type','document_filename','document_mimetype','document_filesize','date_of_creation','created_by'))
						->where($adapter->quoteInto('document_patientID = ?', $patientId))
						->where($adapter->quoteInto('document_type = ?', $document_type))
						->order(array('document_id ASC'))
						->setIntegrityCheck(false);
			if($document_type!='')
			{
				$select = $select->where($adapter->quoteInto('document_type = ?', $document_type));
			}
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$this->_tableField);
		}
		return array();
	}
	public function createDokument($file, $deptId, $patientId, $buttonType,$check=false) {
		if (isset($patientId) && $patientId>0) 
		{
			
			
			$valid_exts = array("jpg","jpeg","gif","png");
			$fileExtension = explode(".",strtolower($file['name']));
			$ext = end($fileExtension);
			$data = array();
			$isImage = false;
			// Check is valid extension then recreate image.
			if(in_array($ext,$valid_exts) && $check)
			{
				$isImage = true;
				$file = $this->generate_resized_image($file);
				$data['document_imagewidth'] = $file['width'];
				$data['document_imageheight'] = $file['height'];
				
				$fileContent = file_get_contents($file['name']);
			}
			else
			{
				if(in_array($buttonType,array('user_image','client_image','dept_image')))
				{
					return 'Invalid Type of File';
				}
				$fileContent = file_get_contents($file['tmp_name']);
			}
			// Les inn filen
			if(true)
			{
				if (is_file($file["tmp_name"])) {
					$mimeType = $file['type'];
								 
					//$fileContent = mysql_real_escape_string($fileContent);
					if($mimeType=='' || $mimeType=='application/x-unknown' )
					{
						$mimeType = $file['type'];
						if($mimeType=='' || $mimeType=='application/x-unknown' )
						{
							$type = '';
							$type = substr($file['tmp_name'],strrpos($file['tmp_name'],'.')+1);
							$mimeType = 'application/'.$type;
						}
					}

					
					$data['document_filesize'] = $file['size'];
					$data['document_mimetype'] = $mimeType;
					$data['document_filename'] = $file['name'];
					$data['document_filecontent'] = $fileContent;
					
					if($file['size']>0 && $file['type']!='') 
					{
						/*$arrayDocument = array();
						if($check)
						{
							$arrayDocument = $this->checkDocument($patientId,$buttonType);
						}
					    if(count($arrayDocument)>0)
						{
							$arrayDocument = $arrayDocument[0];
							$documentFlag = $this->update($data,'document_id',$arrayDocument['document_id']);
							$documentFlag = $arrayDocument['document_id'];
							//return $documentFlag;
						}
						else*/
						{
							$data['document_patientID'] = $patientId;
							$data['document_deptID'] = $deptId;
							$data['document_type'] = $buttonType;
							$documentFlag = $this->insert($data);
						}
							// Slett behandlet fil og returner ID
							unlink($file["tmp_name"]);
							return $documentFlag;
					}
				}
				else 
				{
					return 'Filen ble ikke lastet opp';
				}
			}
			else
			{
				return $file['errormsg'];
			}
		}
		else
		{
			return 'error';
		}
	}
	
	public function generate_resized_image(Array $file){
        $dir = "/tmp/"; // Directory to save resized image. (Include a trailing slash - /)
        // Collect the post variables.
        $postvars = array(
            "name"    => trim($file["name"]),
            "tmp_name"    => $file["tmp_name"],
            "size"    => (int)$file["size"],
			"type"    => $file["type"]
        );
        // Array of valid extensions.
        $valid_exts = array("jpg","jpeg","gif","png");
        // Select the extension from the file.
        $fildeler = explode(".",strtolower($postvars['name']));
        $ext = end($fildeler);
        // Check is valid extension.
        if(in_array($ext,$valid_exts)){
            if($ext == "jpg" || $ext == "jpeg"){
                $image = imagecreatefromjpeg($postvars["tmp_name"]);
            }
            else if($ext == "gif"){
                $image = imagecreatefromgif($postvars["tmp_name"]);
            }
            else if($ext == "png"){
                $image = imagecreatefrompng($postvars["tmp_name"]);
            }
            // Grab the width and height of the image.
            list($width,$height) = getimagesize($postvars["tmp_name"]);
            // If the max width input is greater than max height we base the new image off of that, otherwise we
            // use the max height input.
            // We get the other dimension by multiplying the quotient of the new width or height divided by
            // the old width or height.
            if($width > self::MAX_DIMENSJON_X){
                $newwidth = self::MAX_DIMENSJON_X;
                $newheight = ($newwidth / $width) * $height;
				if($newheight > self::MAX_DIMENSJON_Y){
					$newwidth = (self::MAX_DIMENSJON_Y/$newheight ) * $newwidth;
					$newheight = self::MAX_DIMENSJON_Y;
				}
            } 
            else {
                $newwidth = $width;
                $newheight = $height;
				if($height > self::MAX_DIMENSJON_Y){
					$newwidth = (self::MAX_DIMENSJON_Y/$newheight ) * $newwidth;
					$newheight = self::MAX_DIMENSJON_Y;
				}
            }

            // Create temporary image file.
            $tmp = imagecreatetruecolor($newwidth,$newheight);
            // Copy the image to one with the new width and height.
            imagecopyresampled($tmp,$image,0,0,0,0,$newwidth,$newheight,$width,$height);
            // Create random 4 digit number for filename.
            $rand = rand(1000,9999);
            $filename = $rand.$postvars["name"];
            // Create image file with 100% quality.
            imagejpeg($tmp,$filename,100);
            imagedestroy($image);
			//unlink(APPLICATION_PATH.'/'.$filename);
            imagedestroy($tmp);
            
            return array(
				
                'width' => $newwidth,
                'height' => $newheight,
                'name' => $filename,
				'tmp_name' => $postvars['tmp_name'],
				'size' => $postvars['size'],
				'type' => $postvars['type']
            );
        } 
    }
	
	public function loadList($patientId = 0,$document_type='')
	{
		if($patientId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('d'=>'document'),array('document_id','document_patientID','document_userID','document_deptID','document_type','document_filename','document_mimetype','document_imagewidth','document_imageheight','document_filesize','date_of_creation','created_by'))
						->where($adapter->quoteInto('document_patientID = ?', $patientId))
						->order(array('document_id ASC'))
						->setIntegrityCheck(false);
			if($document_type!='')
			{
				$select = $select->where($adapter->quoteInto('document_type = ?', $document_type));
			}
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$this->_tableField);
		}
		return array();
	}
    public function loadPageData($patientId, $page=1)
    {
		$select = $this->loadList($patientId);
		$result = $this->loadPage($this->_tableField, $select, $page);
		return $result;
    }
	
	public function update(array $dataPost, $key='document_id',$val='')
    {
	  if($val!='')
	  {
		$format = KD::getModel('core/format');
		$ende = KD::getModel('core/endecrypt');//print_r($dataPost);
		foreach($this->_tableField as $column=>$encrypt)
		{
			switch($column)
			{
				case 'date_of_modification':
					$data[$column] = date("Y-m-d H:i:s");
				break;
				case 'modified_by':
					$data[$column] = $_SESSION['Acl']['userID'];
				break;
				default:
					if(isset($dataPost[$column]) && $dataPost[$column]!='')
					{
						if($encrypt)
						{
							$data[$column] = $ende->getEnc($dataPost[$column]);
						}
						else
						{
							$data[$column] = $dataPost[$column];
						}
					}
				break;
				
			}
		}
		$adapter = $this->_getResource()->getAdapter();
		$where = $adapter->quoteInto(' '.$key .' = ? ', $val);
		//print_r($data);exit();
        return parent::update($data,$where);
	  }
	  else
	  {
	  	return 0;
	  }
    }
	
	public function insert(array $dataPost)
    {
		$ende = KD::getModel('core/endecrypt');
		$format = KD::getModel('core/format');
		//print_r($dataPost);exit();
		foreach($this->_tableField as $column=>$encrypt)
		{
			switch($column)
			{
				case 'date_of_creation':
					$data[$column] = date("Y-m-d H:i:s");
				break;
				case 'document_userID':
				case 'created_by':
					$data[$column] = $_SESSION['Acl']['userID'];
				break;
				default:
					if(isset($dataPost[$column]) && $dataPost[$column]!='')
					{
						if($encrypt)
						{
							$data[$column] = $ende->getEnc($dataPost[$column]);
						}
						else
						{
							$data[$column] = $dataPost[$column];
						}
					}
				break;
			}
		}
		//print_r($data);exit();
        return parent::insert($data);
    }
}