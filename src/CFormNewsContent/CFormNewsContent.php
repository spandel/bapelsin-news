<?php
class CFormNewsContent extends CForm {

	private $content;

	public function __construct($content) 
	{
		parent::__construct();
		$this->content = $content;
		$save = isset($content['id']) ? 'save' : 'create';
		
		$checked=array();
		$checked['skateboarding']=null;
		$checked['web']=null;
		$checked['design']=null;
		$checked['gaming']=null;
		$checked['isPlus']=null;
		if($content['isPlus']!=null)
			$checked['isPlus']="checked";
		
		foreach($content['tags'] as $v)
		{
			if($v['tag']=='skateboarding')
				$checked['skateboarding']="checked";
			if($v['tag']=='web')
				$checked['web']="checked";
			if($v['tag']=='design')
				$checked['design']="checked";
			if($v['tag']=='gaming')
				$checked['gaming']="checked";
		}
		
		$this->addElement(new CFormElementHidden('id', array('value'=>$content['id'])))
    	     ->addElement(new CFormElementText('title', array('value'=>$content['title'])))
    	     ->addElement(new CFormElementText('key', array('value'=>$content['key'])))
    	     ->addElement(new CFormElementTextarea('data', array('label'=>'Content:', 'value'=>$content['data'])))
    	     ->addElement(new CFormElementText('image', array('value'=>$content['image'])))
    	     ->addElement(new CFormCheckbox('skateboarding', array('value'=>'on','label'=>'skateboarding','checked'=>$checked['skateboarding'])))
    	     ->addElement(new CFormCheckbox('web', array('value'=>'on','label'=>'web','checked'=>$checked['web'])))
    	     ->addElement(new CFormCheckbox('design', array('value'=>'on','label'=>'design','checked'=>$checked['design'])))
    	     ->addElement(new CFormCheckbox('gaming', array('value'=>'on','label'=>'gaming','checked'=>$checked['gaming'])))
    	     ->addElement(new CFormCheckbox('isPlus', array('value'=>'on','label'=>'isPlus','checked'=>$checked['isPlus'])))
    	     ->addElement(new CFormElementSelect('type', array('value'=>$content['type'],'options'=>array('post'=>'yes','page'=>'no'))))
    	     //->addElement(new CFormElementText('filter', array('value'=>$content['filter'])))
    	     
    	     ->addElement(new CFormElementSelect('filter', array('value'=>$content['filter'],'options'=>array('plain'=>'no','bbcode'=>'no','htmlpurify'=>'no'))))
    	     ->addElement(new CFormElementSubmit($save, 'doSave'))
    	     ->addElement(new CFormElementSubmit('remove', 'doRemove'));

        $this->setValidation('title', array('not_empty'))
       	     ->setValidation('key', array('not_empty'));
    }
}
