<?php
class CFormThreadContent extends CForm {

	public function __construct($type) 
	{
		parent::__construct();
		
		
		
		
		$this->addElement(new CFormElementHidden('id', array('value'=>null)))
    	     ->addElement(new CFormElementText('title', array('value'=>'')))
    	     ->addElement(new CFormElementHidden('key', array('value'=>rand(3,99999))))
    	     ->addElement(new CFormElementTextarea('data', array('label'=>'Text:', 'value'=>'')))
    	     ->addElement(new CFormElementHidden('image', array('value'=>'noimage')))
    	     ->addElement(new CFormElementHidden($type, array('value'=>$type)))
    	     ->addElement(new CFormElementHidden('type', array('value'=>'thread')))
    	     //->addElement(new CFormElementText('filter', array('value'=>$content['filter'])))
    	     
    	     ->addElement(new CFormElementSelect('filter', array('value'=>'bbcode','options'=>array('plain'=>'no','bbcode'=>'yes','htmlpurify'=>'no'))))
    	     ->addElement(new CFormElementSubmit('Spara', 'doSaveThread'));

        $this->setValidation('title', array('not_empty'))
       	     ->setValidation('key', array('not_empty'));
    }
}
