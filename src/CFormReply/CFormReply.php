<?php
class CFormReply extends CForm {

	private $gb;

	public function __construct($gb=null) 
	{
		parent::__construct();
		
		$this->gb = $gb;
		$this->addElement(new CFormElementHidden('Alias', array('value'=>$gb->userName)))
    	     ->addElement(new CFormElementTextarea('Kommentar', array()))    	     
    	     ->addElement(new CFormElementSubmit('Lägg till', 'doAdd'))
    	     ->addElement(new CFormElementHidden('id', array('value'=>$gb->id)))
    	     ->addElement(new CFormElementHidden('asReplyTo', array('value'=>$gb->asReplyTo)));
    	     //->addElement(new CFormElementSubmit('clear poems', 'doClear'));

        $this->setValidation('Alias', array('not_empty'))
       	     ->setValidation('Kommentar', array('not_empty'));
    }
}
