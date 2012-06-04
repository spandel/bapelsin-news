<?php
class CCNews extends CObject implements IController{
	
	private $gbModel;
	
	
	public function __construct()
	{
		parent::__construct();
		$this->gbModel=new CMNewsComments();
	}
	public function init()
	{
		$content =new CMNewsContent();
		$content->init();
		$cmoment =new CMNewsComments();
		$cmoment->init();
		$this->redirectToController();
	}
	public function createThread($type=null)
	{
		$content=new CMNewsContent();
		if($this->user->isAuthenticated())
		{			
			$form = new CFormThreadContent($type);
		
			$form->form['action']=$this->request->createUrl('news/handler');
			$status=$form->check();
		
			if($status===false)
			{
				$this->session->addMessage('notice', 'The form is incomplete.');
				$this->redirectToController('createThread/'.$type);
			}
			else if(isset($_POST['doSaveThread']))
			{
				$this->doSave($form, $content);
				$this->redirectToController("forum");
			
			}
		//echo$content['image'];
			$title = isset($id) ? 'Redigera' : 'Skapa';
			
			$this->views->setTitle("Skapa tråd : ".$type);
			$this->views->addInclude(__DIR__. '/newThread.tpl.php',array('content'=>$content, 'form'=>$form, 'type'=>$type));
		}
		else
		{
			$this->redirectToController('forum');
		}
		$this->addFooter();
	}
	public function showThread($id=null,$replyTo=null)
	{
		$replyForm=null;
		
		
		if($replyTo!=null)
		{
			$content = new CMNewsContent($id);
			$commentsModel=new CMNewsComments();
			$commentsModel->id=$content['id'];
			$commentsModel->userName=$this->user['acronym'];
			$commentsModel->asReplyTo=$replyTo;
			$replyForm=new CFormReply($commentsModel);
			$replyForm->form['action']=$this->request->createUrl('news/handler/thread');
		}
		else if($id!=null)
		{
			$content = new CMNewsContent($id);
			
			$commentsModel=new CMNewsComments();
			$commentsModel->id=$content['id'];
			$commentsModel->userName=$this->user['acronym'];
			
		}
		if($id!=null)
		{						
			$form=new CFormComments($commentsModel);		
			$form->form['action']=$this->request->createUrl('news/handler/thread');
		
			$content['tags']=$content->listAllTags(array('id'=>$content['id']));
			$this->views->setTitle(htmlEnt($content['title']));
			$tag=0;
			foreach($content['tags'] as $v)
			{
				$tag=$v['tag'];
				break;
			}

				$tag=$this->tagToInt($tag);
			$isLoggedIn=$this->user->isAuthenticated();
			$contents=$content->listAllByTag(array('tag'=>$tag, 'order-by'=>'created', 'order-order'=>'DESC'));
			
			$this->views->addInclude(__DIR__. '/featured.tpl.php',array('val'=>next($contents)),'triptych-first');		
			$this->views->addInclude(__DIR__. '/featured.tpl.php',array('val'=>next($contents)),'triptych-middle');
			$this->views->addInclude(__DIR__. '/featured.tpl.php',array('val'=>next($contents)),'triptych-last');
			$this->views->addInclude(__DIR__ . '/thread.tpl.php', array('contents' => $content,'isLoggedIn'=>$isLoggedIn),'primary');
			$this->views->addInclude(__DIR__ . '/comments.tpl.php', array(
				'entries'=>$commentsModel->getEntries($content['id']), 
				'form'=>$form,
				'isThread'=>true,
				'replyTo'=>$replyTo,
				'replyForm'=>$replyForm,
				'userName'=>$this->user['name'],
				'isLoggedIn'=>$isLoggedIn,
				'formAction'=>$this->request->createUrl('news/handler')
			),'primary');  
		}
		$this->addFooter();
	}
	
	
	public function addFooter()
	{
		$a=array();
		$a[]=$this->request->createUrl('news/skateboarding');
		$a[]=$this->request->createUrl('news/web');
		$a[]=$this->request->createUrl('news/design');
		$a[]=$this->request->createUrl('news/gaming');
		$feet['foot1']=<<<EOD
<p>Taggar</p>


<a href="{$a[0]}">Skateboarding</a></br>
<a href="{$a[1]}">Web</a></br>
<a href="{$a[2]}">Design</a></br>
<a href="{$a[3]}">Gaming</a></br> 


EOD;
		$a=array();
		$a[]=$this->request->createUrl('news/forum');
		$a[]=$this->request->createUrl('news/about');
		$a[]=$this->request->createUrl('news/contact');

		$feet['foot2']=<<<EOD
<p>Annat</p>


<a href="{$a[0]}">Forum</a></br>
<a href="{$a[1]}">Om oss</a></br>
<a href="{$a[2]}">Kontakt</a></br>
</p>


EOD;
		$feet['foot3']=<<<EOD
<p>Daniel Spandel</p>	
<a href="http://www.student.bth.se/~dasp11">Webbsida</a></br>
<a href="mailto:superflugan@hotmail.com">Email</a></br>
<a href="http://twitter.com/spandel">Twitter</a></br>
<a href="http://www.student.bth.se/cv_daniel_spandel.pdf">CV</a></br>


EOD;
		$feet['foot4']=<<<EOD
<p>Hur är sidan gjord?</p>	

<a href="http://www.student.bth.se/~dasp11/phpmvc/bapelsin">Bapelsin</a></br>
<a href="http://htmlpurifier.org/">htmlpurifier</a></br>
<a href="http://lesscss.org/">LESS</a></br>
<a href="http://semantic.gs/">Semantic.gs</a></br>


EOD;
		
		
		$this->views->addString($feet['foot1'],array(),'footer-column-one');
		$this->views->addString($feet['foot2'],array(),'footer-column-two');	
		$this->views->addString($feet['foot3'],array(),'footer-column-three');	
		$this->views->addString($feet['foot4'],array(),'footer-column-four');	
	}
	public function index()
	{
		$content = new CMNewsContent();
		$contents=$content->listAll(array('type'=>'post','order-by'=>'created', 'order-order'=>'DESC'));
		
		foreach($contents as &$val)
		{
			$val['tags']=$content->listAllTags(array('id'=>$val['id']));
		}
		
		$this->views->setTitle("index");
		if(isset($contents[1]))
			$this->views->addInclude(__DIR__. '/featured.tpl.php',array('val'=>$contents[1]),'featured-first');		
		if(isset($contents[2]))
			$this->views->addInclude(__DIR__. '/featured.tpl.php',array('val'=>$contents[2]),'featured-middle');
		if(isset($contents[3]))
			$this->views->addInclude(__DIR__. '/featured.tpl.php',array('val'=>$contents[3]),'featured-last');
		
		$search="skateboarding";
		$skateFeeds=$this->getTwitter($search);
		$search="webdevelopment";
		$webFeeds=$this->getTwitter($search);
		$search="graphicdesign";
		$designFeeds=$this->getTwitter($search);
		$search="videogames";
		$gameFeeds=$this->getTwitter($search);
		
		$arrFeeds[]=$skateFeeds[0];
		$arrFeeds[]=$skateFeeds[1];
		$arrFeeds[]=$webFeeds[0];
		$arrFeeds[]=$webFeeds[1];
		$arrFeeds[]=$designFeeds[0];
		$arrFeeds[]=$designFeeds[1];
		$arrFeeds[]=$gameFeeds[0];
		$arrFeeds[]=$gameFeeds[1];
		
		//$arrFeeds=array_merge($skateFeeds, $webFeeds, $designFeeds, $gameFeeds);
		
		$arrFeeds=$this->sort_by_key($arrFeeds, 'date');

		$this->views->addInclude(__DIR__. '/twitter.tpl.php',array('feed'=>$arrFeeds, 'search'=>"All feeds @ twitter"),'sidebar');	
		$this->addFooter();
		$this->views->addInclude(__DIR__. '/blog.tpl.php',array('contents'=>$contents),'primary');
	}
	private function sort_by_key ($arr,$key) 
	{ 
		global $key2sort; 
		$key2sort = $key; 
		uasort($arr, array($this,'sbk')); 
		return ($arr); 
	} 
	private function sbk ($b, $a) 
	{
		global $key2sort; 
		return (strcasecmp ($a[$key2sort],$b[$key2sort]));
	} 
	public function post($id=null, $replyTo=null)
	{
		$replyForm=null;
		if($replyTo!=null)
		{
			$content = new CMNewsContent($id);
			$commentsModel=new CMNewsComments();
			$commentsModel->id=$content['id'];
			$commentsModel->userName=$this->user['acronym'];
			$commentsModel->asReplyTo=$replyTo;
			$replyForm=new CFormReply($commentsModel);
			$replyForm->form['action']=$this->request->createUrl('news/handler');
		}
		else if($id!=null)
		{
			$content = new CMNewsContent($id);
			
			$commentsModel=new CMNewsComments();
			$commentsModel->id=$content['id'];
			$commentsModel->userName=$this->user['acronym'];
			
		}
		if($id!=null)
		{						
			$form=new CFormComments($commentsModel);		
			$form->form['action']=$this->request->createUrl('news/handler');
		
			$content['tags']=$content->listAllTags(array('id'=>$content['id']));
			$this->views->setTitle(htmlEnt($content['title']));
			$tag=0;
			foreach($content['tags'] as $v)
			{
				$tag=$v['tag'];
				break;
			}

				$tag=$this->tagToInt($tag);
			$isLoggedIn=$this->user->isAuthenticated();
			$contents=$content->listAllByTag(array('tag'=>$tag, 'order-by'=>'created', 'order-order'=>'DESC'));
			
			$this->views->addInclude(__DIR__. '/featured.tpl.php',array('val'=>next($contents)),'triptych-first');		
			$this->views->addInclude(__DIR__. '/featured.tpl.php',array('val'=>next($contents)),'triptych-middle');
			$this->views->addInclude(__DIR__. '/featured.tpl.php',array('val'=>next($contents)),'triptych-last');
			$this->views->addInclude(__DIR__ . '/page.tpl.php', array('contents' => $content,'isLoggedIn'=>$isLoggedIn),'primary');
			$this->views->addInclude(__DIR__ . '/comments.tpl.php', array(
				'entries'=>$commentsModel->getEntries($content['id']), 
				'form'=>$form,
				'replyTo'=>$replyTo,
				'replyForm'=>$replyForm,
				'userName'=>$this->user['name'],
				'isLoggedIn'=>$isLoggedIn,
				'formAction'=>$this->request->createUrl('news/handler')
			),'primary');  
		}
		$this->addFooter();
	}
	private function getTwitter(&$search)
	{		
		$url = "http://search.twitter.com/search.rss?q=%23".$search;
		//$url = "http://www.aftonbladet.se/rss.xml";
		$xml = simplexml_load_file($url);
		
		$arrFeeds = array();
		$i=0;
		$xml=$xml->channel;
		
		foreach($xml->item as $item)
		{
			//echo"<pre>".print_r($item,true)."</pre>";
			$itemRSS=array();
			//echo "Titel:".$item->title;
			//$itemRSS['title']  = $item->title;
			$itemRSS['desc']   = $item->description;				
			$itemRSS['author'] = $item->author;					
			$itemRSS['date']   = $item->pubDate;	
				
			$itemRSS['author']=strstr($itemRSS['author'],'@',true);
			$itemRSS['link']="http://www.twitter.com/".$itemRSS['author'];
			
			array_push($arrFeeds, $itemRSS);
			
			if($i>=10)
				break;
			
			$i++;
 		}
 		$search='#'.$search." @ twitter";
		
		/*$doc = new DOMDocument();
		$doc->load("http://search.twitter.com/search.rss?q=%23".$search);
		
		
		$arrFeeds = array();
		$i=0;
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
			
			if($i>=10)
				break;
			
			$i++;
    	}
    	
    	foreach($doc->getElementsByTagName('channel') as $node)
		{
			$search=$node->getElementsByTagName('title')->item(0)->nodeValue;
			$search=strstr($search,' - ', true);
			$search=$search." @ twitter";
			break;
		}
    	*/
    	return $arrFeeds;
	}
	public function design()
	{
		$content = new CMNewsContent();
		$this->views->setTitle("Design");
		$contents=$content->listAllByTag(array('tag'=>3, 'order-by'=>'created', 'order-order'=>'DESC'));

		foreach($contents as &$val)
		{
			$val['tags']=$content->listAllTags(array('id'=>$val['id']));
		}
		
		$search="graphicdesign";
		$arrFeeds=$this->getTwitter($search);
		$this->views->addInclude(__DIR__. '/twitter.tpl.php',array('feed'=>$arrFeeds, 'search'=>$search),'sidebar');
		$this->views->addInclude(__DIR__. '/blog.tpl.php',array('contents'=>$contents),'primary');
		$this->addFooter();
	}
	public function web()
	{
		$content = new CMNewsContent();
		$this->views->setTitle("Web developing");
		$contents=$content->listAllByTag(array('tag'=>2, 'order-by'=>'created', 'order-order'=>'DESC'));

		foreach($contents as &$val)
		{
			$val['tags']=$content->listAllTags(array('id'=>$val['id']));
		}
		$search="webdevelopment";
		$arrFeeds=$this->getTwitter($search);
		$this->views->addInclude(__DIR__. '/twitter.tpl.php',array('feed'=>$arrFeeds, 'search'=>$search),'sidebar');		
		$this->views->addInclude(__DIR__. '/blog.tpl.php',array('contents'=>$contents),'primary');
		$this->addFooter();
	}
	public function gaming()
	{
		$content = new CMNewsContent();
		$this->views->setTitle("Gaming");
		$contents=$content->listAllByTag(array('tag'=>4, 'order-by'=>'created', 'order-order'=>'DESC'));

		foreach($contents as &$val)
		{
			$val['tags']=$content->listAllTags(array('id'=>$val['id']));
		}
		$search="videogames";
		$arrFeeds=$this->getTwitter($search);
		$this->views->addInclude(__DIR__. '/twitter.tpl.php',array('feed'=>$arrFeeds, 'search'=>$search),'sidebar');
		
		$this->views->addInclude(__DIR__. '/blog.tpl.php',array('contents'=>$contents),'primary');
		$this->addFooter();		
	}
	public function skateboarding()
	{
		$content = new CMNewsContent();
		
		$contents=$content->listAllByTag(array('tag'=>1, 'order-by'=>'created', 'order-order'=>'DESC'));

		foreach($contents as &$val)
		{
			$val['tags']=$content->listAllTags(array('id'=>$val['id']));
		}
		$this->views->setTitle("Skateboarding");
		$search="skateboarding";
		$arrFeeds=$this->getTwitter($search);
		$this->views->addInclude(__DIR__. '/twitter.tpl.php',array('feed'=>$arrFeeds, 'search'=>$search),'sidebar');
		
		$this->views->addInclude(__DIR__. '/blog.tpl.php',array('contents'=>$contents),'primary');
		$this->addFooter();
	}	
	public function about()
	{
		$content = new CMNewsContent();
		$content['title']="Om oss";
		$content['data']="Detta är en livsstils-webb-tidning för människor.

För människor som tycker att design är viktigt. 
För människor som trivs på webben och gärna vill vara en del av den. 
För människor som gillar att koppla av en stund framför ett riktigt bra spel.
För människor som gärna är ute och sätter en 360 flip eller glider runt på sin longboard. 

Detta är en sida för alla oss brödrostar därute.";
		$this->views->setTitle("Om oss");
		$this->views->addInclude(__DIR__. '/primary.tpl.php',array('contents'=>$content),'primary');
		$this->addFooter();
	}
	public function forum()
	{
		$content = new CMNewsContent();
		$this->views->setTitle("Toaster - Forum");
		$contents=$content->listAll(array('type'=>'thread','order-by'=>'title', 'order-order'=>'DESC'));
		$contentArranged=array();
		$contentArranged['skateboarding']=array();
		$contentArranged['web']=array();
		$contentArranged['design']=array();
		$contentArranged['gaming']=array();
		
		foreach($contents as &$val)
		{			
			$val['tags']=$content->listAllTags(array('id'=>$val['id']));
		//	echo "<pre>".print_r($val['tags'],true)."</pre>";
			if($val['tags'][0]['id']==1)
				$contentArranged['skateboarding'][]=$val;
			else if($val['tags'][0]['id']==2)
				$contentArranged['web'][]=$val;
			else if($val['tags'][0]['id']==3)
				$contentArranged['design'][]=$val;
			else if($val['tags'][0]['id']==4)
				$contentArranged['gaming'][]=$val;			
		}
		$this->views->addInclude(__DIR__. '/forum.tpl.php',array('content'=>$contentArranged, 'isLoggedIn'=>$this->user->isAuthenticated()),'primary');
		$this->addFooter();
	}
	public function contact()
	{
		$content = new CMNewsContent();
		$content['title']="Kontakt";
		$content['filter']="htmlpurify";
		$content['data']="<h3>Email</h3><a href='mailto:dasp11@student.bth.se'>dasp11@student.bth.se</a>";
		$this->views->setTitle("Om oss");
		$this->views->addInclude(__DIR__. '/primary.tpl.php',array('contents'=>$content),'primary');
		$this->addFooter();
	}
	public function authoring()
	{
		$content = new CMNewsContent();
		$this->views->setTitle("Hantera artiklar");
		if($this->user->isAuthenticated() && $this->user->isAdministrator())
		{
			$contents=$content->listAll(array('type'=>'post','order-by'=>'title', 'order-order'=>'DESC'));
			$this->views->addInclude(__DIR__. '/authoring.tpl.php',array('content'=>$contents),'primary');
		}
		else
			$this->views->addString("<h1>You shall not pass!</h1><p>You do NOT have permission to view this page!</p>");
		$this->addFooter();
	}
	public function makeUserRegular($id)
	{
		$users = new CMUser();
		$users->modifyGroups(2,$id);
		$this->redirectToController('users');
	}
	public function makeUserAdmin($id)
	{
		$users = new CMUser();
		$users->modifyGroups(1,$id);
		$this->redirectToController('users');
	}
	public function users()
	{
		$users = new CMUser();
		$this->views->setTitle("Hantera användare");
		if($this->user->isAuthenticated() && $this->user->isAdministrator())
		{
			$contents=$users->getUsers();
			$this->views->addInclude(__DIR__. '/users.tpl.php',array('users'=>$contents),'primary');
		}
		else
			$this->views->addString("<h1>You shall not pass!</h1><p>You do NOT have permission to view this page!</p>");
		$this->addFooter();
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
		$this->addFooter();
	}
	public function handler($isThread=null)
	{		
		if(isset($_POST['doAdd']))
		{			
			$entry=strip_tags($_POST['Kommentar']);
			$poet=strip_tags($_POST['Alias']);
			$asCommentTo=$_POST['id'];
			$asReplyTo=null;
			if(isset($_POST['asReplyTo']))
				$asReplyTo=$_POST['asReplyTo'];
			if($entry!="" && $poet!="")
				$this->gbModel->addNewEntry($asCommentTo, $asReplyTo, $entry, $poet);	
			
			if($isThread==null)
				$this->redirectToController('post/'.$asCommentTo."#".$this->db->lastInsertID());
			else
				$this->redirectToController('showThread/'.$asCommentTo."#".$this->db->lastInsertID());
		}
		else if(isset($_POST['doClear']))
		{
			$this->gbModel->emptyEntries();
		}
		else if(isset($_POST['doCreate']))
		{
			$this->gbModel->init();
		}
		else if(isset($_POST['doSaveThread']))
		{		 	
			$this->createThread();
		}
		else if(isset($_POST['doSave']))
		{		 	
			$this->edit();
		}
		else if(isset($_POST['doRemove']))
		{				
			$this->remove();
		}		
		
		//header('Location: '.$this->request->createUrl('guestbook'));
		//$this->redirectToController();
	}
	
	public function create()
	{
		$this->edit();
	}
	public function remove($id=null)
	{
		$content=new CMNewsContent(null);
		//$form = new CFormNewsContent($content);
		//$content['id']    = $form['id']['value'];
		//$this->instance()->AAAAAAAAAAAAAAAAAAAAAAAAAAAHHHHHHHGHGHGHGHGHGHG=$content['id'];
		if($this->user->isAuthenticated() && $this->user->isAdministrator())
		{
			if($id!=null)
				$content->remove($id);
			else
				$content->remove($_POST['id']);
		}
		$this->redirectToController('authoring');
	}
	public function edit($id=null)
	{		
		$content=new CMNewsContent($id);
		if($this->user->isAuthenticated() && $this->user->isAdministrator())
		{
			$content['tags']=$content->listAllTags(array('id'=>$content['id']));
			
			$form = new CFormNewsContent($content);
		
			$form->form['action']=$this->request->createUrl('news/handler');
			$status=$form->check();
		
			if($status===false)
			{
				$this->session->addMessage('notice', 'The form is incomplete.');
				$this->redirectToController('edit');
			}
			else if(isset($_POST['doSave']))
			{
				$this->doSave($form, $content);
				$this->redirectToController("edit/{$content['id']}");
			
			}
		//echo$content['image'];
			$title = isset($id) ? 'Redigera' : 'Skapa';
			$id = isset($id) ? ": {$id}" : $id;
			$this->views->setTitle("$title artikel $id");
			$this->views->addInclude(__DIR__. '/edit.tpl.php',array('content'=>$content, 'form'=>$form));
		}
		else
		{
			$this->redirectToController('authoring');
		}
		$this->addFooter();
	}
	private function doSave($form, $content) 
    {        	
    	if(!$this->user->isAuthenticated() || $this->user->isAdministrator())
    		$this->redirectToController('authoring');
    	unset($content['tags']);
    /*	if(isset($content['tags']))	
    	{
    		 $text=print_r($content['tags'],true);
    		 $this->session->addMessage('notice',$text);
    	}*/
    	//$content['tags']  = tagsToInt($form);
    	$content['tags']=$this->tagsToInt($form);
    	//$content['tags'][]=1;
    	//die("blabla");
    	//print_r($content['tags'],true);
    	
    	$content['id']    = $form['id']['value'];
    	$content['title'] = $form['title']['value'];
    	$content['key']   = $form['key']['value'];
    	$content['data']  = $form['data']['value'];
    	if(isset($_POST['isPlus']))
    	$content['isPlus']= 1;
    	$content['image'] = $form['image']['value'];
    	$content['type']  = $form['type']['value'];
    	$content['filter']= $form['filter']['value'];
    	return $content->save();
    }
    private static function tagsToInt($form)
    {    	
    	$arrInt=array();
    
    	if(isset($_POST['skateboarding']))
    		$arrInt[]=1;
    	if(isset($_POST['web']))
    		$arrInt[]=2;
    	if(isset($_POST['design']))
    		$arrInt[]=3;
    	if(isset($_POST['gaming']))
    		$arrInt[]=4;
    	//$this->config['test']=$arrInt;
    	
    	return $arrInt;
    }
    private static function tagToInt($tag)
    {
    	$arr=array();
    	$arr['skateboarding']=1;
    	$arr['web']=2;
    	$arr['design']=3;
    	$arr['gaming']=4;
    	
    	return $arr[$tag];
    }
    private static function intToTag($int)
    {
    	$arr=array();
    	$arr[1]='skateboarding';
    	$arr[2]='web';
    	$arr[3]='design';
    	$arr[4]='gaming';
    	
    	return $arr[$tag];
    }
	
}


