<?php
class CCNews extends CObject implements IController{
	
	private $gbModel;
	
	
	public function __construct()
	{
		parent::__construct();
		$this->gbModel=new CMGuestbook();
	}
	public function init()
	{
		$content =new CMNewsContent();
		$content->init();
		$this->redirectToController();
	}
	public function index()
	{
		$content = new CMNewsContent();
		$contents=$content->listAll(array('type'=>'post','order-by'=>'title', 'order-order'=>'DESC'));
		
		foreach($contents as &$val)
		{
			$val['tags']=$content->listAllTags(array('id'=>$val['id']));
		}
		
		$this->views->setTitle("Design");
		if(isset($contents[1]))
			$this->views->addInclude(__DIR__. '/featured.tpl.php',array('val'=>$contents[1]),'featured-first');		
		if(isset($contents[2]))
			$this->views->addInclude(__DIR__. '/featured.tpl.php',array('val'=>$contents[2]),'featured-middle');
		if(isset($contents[3]))
			$this->views->addInclude(__DIR__. '/featured.tpl.php',array('val'=>$contents[3]),'featured-last');
		$this->views->addInclude(__DIR__. '/blog.tpl.php',array('contents'=>$contents),'primary');
	}
	public function post($id=null)
	{
		if($id!=null)
		{
			
			$content = new CMNewsContent($id);
			$contents=$content->listAll(array('type'=>'post','order-by'=>'title', 'order-order'=>'DESC'));
			$commentsModel=new CMNewsComments();
			$form=new CFormComments($commentsModel);		
			$form->form['action']=$this->request->createUrl('guestbook/handler');
		
			$content['tags']=$content->listAllTags(array('id'=>$content['id']));
			$this->views->setTitle(htmlEnt($content['title']));
			$this->views->addInclude(__DIR__. '/featured.tpl.php',array('val'=>$contents[1]),'triptych-first');		
			$this->views->addInclude(__DIR__. '/featured.tpl.php',array('val'=>$contents[2]),'triptych-middle');
			$this->views->addInclude(__DIR__. '/featured.tpl.php',array('val'=>$contents[3]),'triptych-last');
			$this->views->addInclude(__DIR__ . '/page.tpl.php', array('contents' => $content),'primary');
			$this->views->addInclude(__DIR__ . '/comments.tpl.php', array(
				'entries'=>$commentsModel->getEntries(), 
				'form'=>$form,
				'formAction'=>$this->request->createUrl('guestbook/handler')
			),'primary');  
		}
	}
	public function getTwitter(&$search)
	{
		$doc = new DOMDocument();
		$doc->load("http://search.twitter.com/search.rss?q=%23".$search);
		$arrFeeds = array();
		foreach ($doc->getElementsByTagName('item') as $node) 
		{
			//$g = $node->children("http://base.google.com/ns/1.0"); 
			$itemRSS = array ( 
				'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
				'desc' 	=> $node->getElementsByTagName('description')->item(0)->nodeValue,				
				'author'=> $node->getElementsByTagName('author')->item(0)->nodeValue,
				
				'date' 	=> $node->getElementsByTagName('pubDate')->item(0)->nodeValue
				);
			$itemRSS['author']=strstr($itemRSS['author'],'@',true);
			$itemRSS['link']="http://www.twitter.com/".$itemRSS['author'];
			array_push($arrFeeds, $itemRSS);
    	}
    	
    	foreach($doc->getElementsByTagName('channel') as $node)
		{
			$search=$node->getElementsByTagName('title')->item(0)->nodeValue;
			$search=strstr($search,' - ', true);
			$search=$search." @ twitter";
			break;
		}
    	
    	return $arrFeeds;
	}
	public function design()
	{
		$content = new CMNewsContent();
		$this->views->setTitle("Design");
		$contents=$content->listAllByTag(array('tag'=>3, 'order-by'=>'title', 'order-order'=>'DESC'));

		foreach($contents as &$val)
		{
			$val['tags']=$content->listAllTags(array('id'=>$val['id']));
		}
		
		$search="graphicdesign";
		$arrFeeds=$this->getTwitter($search);
		$this->views->addInclude(__DIR__. '/twitter.tpl.php',array('feed'=>$arrFeeds, 'search'=>$search),'sidebar');
		$this->views->addInclude(__DIR__. '/blog.tpl.php',array('contents'=>$contents),'primary');
	}
	public function web()
	{
		$content = new CMNewsContent();
		$this->views->setTitle("Web developing");
		$contents=$content->listAllByTag(array('tag'=>2, 'order-by'=>'title', 'order-order'=>'DESC'));

		foreach($contents as &$val)
		{
			$val['tags']=$content->listAllTags(array('id'=>$val['id']));
		}
		$search="webdevelopment";
		$arrFeeds=$this->getTwitter($search);
		$this->views->addInclude(__DIR__. '/twitter.tpl.php',array('feed'=>$arrFeeds, 'search'=>$search),'sidebar');
		
		$this->views->addInclude(__DIR__. '/blog.tpl.php',array('contents'=>$contents),'primary');	
	}
	public function gaming()
	{
		$content = new CMNewsContent();
		$this->views->setTitle("Gaming");
		$contents=$content->listAllByTag(array('tag'=>4, 'order-by'=>'title', 'order-order'=>'DESC'));

		foreach($contents as &$val)
		{
			$val['tags']=$content->listAllTags(array('id'=>$val['id']));
		}
		$search="videogames";
		$arrFeeds=$this->getTwitter($search);
		$this->views->addInclude(__DIR__. '/twitter.tpl.php',array('feed'=>$arrFeeds, 'search'=>$search),'sidebar');
		
		$this->views->addInclude(__DIR__. '/blog.tpl.php',array('contents'=>$contents),'primary');		
	}
	public function skateboarding()
	{
		$content = new CMNewsContent();
		
		$contents=$content->listAllByTag(array('tag'=>1, 'order-by'=>'title', 'order-order'=>'DESC'));

		foreach($contents as &$val)
		{
			$val['tags']=$content->listAllTags(array('id'=>$val['id']));
		}
		$this->views->setTitle("Skateboarding");
		$search="skateboarding";
		$arrFeeds=$this->getTwitter($search);
		$this->views->addInclude(__DIR__. '/twitter.tpl.php',array('feed'=>$arrFeeds, 'search'=>$search),'sidebar');
		
		$this->views->addInclude(__DIR__. '/blog.tpl.php',array('contents'=>$contents),'primary');		
		
	}
	public function guestbook()
	{
		$gbModel=new CMGuestbook();
		$form=new CFormGuestbook($gbModel);
		
		$this->views->setTitle("My guestbook");     
		$form->form['action']=$this->request->createUrl('my/handler');
		$this->views->addInclude(__DIR__ . '/gb.tpl.php', array(
			'entries'=>$gbModel->getEntries(), 
			'form'=>$form,
			'formAction'=>$this->request->createUrl('guestbook/handler')
			));  
	}
	public function handler()
	{
		if(isset($_POST['doAdd']))
		{			
			$entry=strip_tags($_POST['poem']);
			$poet=strip_tags($_POST['poet']);
			if($entry!="" && $poet!="")
				$this->gbModel->addNewEntry($entry, $poet);			
		}
		else if(isset($_POST['doClear']))
		{
			$this->gbModel->emptyEntries();
		}
		else if(isset($_POST['doCreate']))
		{
			$this->gbModel->init();
		}
		//header('Location: '.$this->request->createUrl('guestbook'));
		$this->redirectToController('guestbook');
	}
	
}


