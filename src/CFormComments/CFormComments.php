<?php
class CFormComments extends CForm {

	private $gb;

	public function __construct($gb=null) 
	{
		parent::__construct();
		
		$this->gb = $gb;
		$this->addElement(new CFormElementText('Alias', array()))
    	     ->addElement(new CFormElementTextarea('Kommentar', array()))    	     
    	     ->addElement(new CFormElementSubmit('Lägg till', 'doAdd'));
    	     //->addElement(new CFormElementSubmit('clear poems', 'doClear'));

        $this->setValidation('Alias', array('not_empty'))
       	     ->setValidation('Kommentar', array('not_empty'));
    }
}
