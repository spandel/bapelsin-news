<?php
//Set error reporting
error_reporting(-1);
ini_set('display_errors',1);

//turn on/off debugging
$bap->config['debug']['display-bapelsin'] = false;
$bap->config['debug']['db-num-queries']=false;
$bap->config['debug']['db-queries']=false;
$bap->config['debug']['timer']=true;

//set database(s)
$bap->config['database'][0]['dsn']='sqlite:'.BAPELSIN_SITE_PATH.'/data/.ht.sqlite';

//sets a name for the session
$bap->config['session_name']=preg_replace('/[:\.\/-_]/','',$_SERVER["SERVER_NAME"]);
$bap->config['session_key']='lydia';

//set your timezone
$bap->config['timezone']='Europe/Stockholm';

//set character encoding
$bap->config['character_encoding']='UTF-8';

//set hashing algorithm. choose from: plain, md5salt, md5, sha1salt, sha1.
$bap->config['hashing_algorithm']="sha1salt";

//set if creating new users is allowed
$bap->config['create_new_users'] = true;

//set language
$bap->config['language']='en';

//set base url. if null, default base_url will be used.
$bap->config['base_url']=null;

//set controllers. 
$bap->config['controllers'] = 
array(
		'acp'		=> array('enabled' => true,'class' => 'CCAdminControlPanel'),
		'blog'		=> array('enabled' => true,'class' => 'CCBlog'),
		'content'	=> array('enabled' => true,'class' => 'CCContent'),
		'developer'	=> array('enabled' => true,'class' => 'CCDeveloper'),
		'guestbook'	=> array('enabled' => true,'class' => 'CCGuestbook'),
		'index'		=> array('enabled' => true,'class' => 'CCIndex'),	
		'modules'	=> array('enabled' => true,'class' => 'CCModules'),
		'page'		=> array('enabled' => true,'class' => 'CCPage'),		
		'themes'	=> array('enabled' => true,'class' => 'CCTheme'),
		'user'		=> array('enabled' => true,'class' => 'CCUser'),
		'news'		=> array('enabled' => true, 'class'=> 'CCNews'),
);
/*

$login=login_menu();
$bap->data['header']="Bapelsin.";
$bap->data['slogan']="The smart framework.";
$bap->data['favicon']="bapelsin.png";
$bap->data['logo']="bapelsin.png";
$bap->data['logo_width']=110;
$bap->data['logo_height']=110;
$bap->data['footer']=<<<EOD
<p>Bapelsin : a framework by Daniel Spandel.</p>

<p>
Here's Daniels 
<a href="http://www.student.bth.se/~dasp11">page at BTH</a>, 
<a href="mailto:superflugan@hotmail.com">email</a>, 
<a href="http://twitter.com/spandel">twitter</a>, 
<a href="http://www.facebook.com/daniel.spandel">facebook</a>, 
<a href="http://www.student.bth.se/cv_daniel_spandel.pdf">CV</a>
</p>

EOD;
//$bap->data['footer']="Vadå fult? Det är ju du som inte har någon smak!<br/><br/>$dispTime";
*/
$adj=array(
	'bonkers',
	'smart',
	'fun',
	'ugly',
	'mosaicish',
	'friendly',
	'contemporary',
	'old',
	'dark',
	'light',
	'beautiful',
	'good-looking',
	'wonderful',
	'great',
	'hysterical',
	'weird',
	'strange',
	'funny',
	'yellow',
	'lovely',
	'hated',
	'loved',
	'multi-dimensional',
	'multi-functional',
	'dynamic',
	);

//$bap->data['slogan']="The {$bap->data['slogan-adjektivs'][array_rand($bap->data['slogan-adjektivs'])]} framework.";

$foot=<<<EOD
<p>Bapelsin : a framework by Daniel Spandel.</p>

<p>
Here's Daniels 
<a href="http://www.student.bth.se/~dasp11">page at BTH</a>, 
<a href="mailto:superflugan@hotmail.com">email</a>, 
<a href="http://twitter.com/spandel">twitter</a>, 
<a href="http://www.facebook.com/daniel.spandel">facebook</a>, 
<a href="http://www.student.bth.se/cv_daniel_spandel.pdf">CV</a>
</p>

EOD;

$foot="";



//
$bap->config['create_dummy_text']=true;

//site_url... should add function for this in functions.php
$bap->config['datafolder']=BAPELSIN_SITE_PATH.'/data/';


//set what theme to use.
$bap->config['theme'] = array(
	'name'=> 'grid', 
	'path'=>'site/themes/mytheme',
	'parent'=>'theme/grid',
	'stylesheet'=>'style.css',
	'template_file'=>'default.tpl.php',
	'regions'=>array('navbar','flash','featured-first','featured-middle','featured-last',
					'primary','sidebar','triptych-first','triptych-middle','triptych-last',
					'footer-column-one','footer-column-two','footer-column-three','footer-column-four',
					'footer',
	),
	'data'=>array(
		'header'=>'toaster.',
		'slogan'=>"",
		'favicon'=>'toaster-logo.png',
		'logo'=>'toaster-logo.png',
		'logo_width'=>100,
		'logo_height'=>'',
		'footer'=>$foot,
	),
	'menu_to_region'=>array(
		'news'=>'navbar',
	),
);

$bap->config['routing']=array(
	'home' =>array('enabled'=>true, 'url'=>'index/index'),
);

$bap->config['menus']=array(
	'navbar'=>array(
		'home'      => array('label'=>'Home','url'=>'index'),
		'modules'   => array('label'=>'Modules','url'=>'modules'),
		'content'   => array('label'=>'Content','url'=>'content'),
		'guestbook' => array('label'=>'Guestbook','url'=>'guestbook'),
		'blog'      => array('label'=>'Blog','url'=>'blog'),
	),
	'news'=>array(
		''			=> array('label'=>'HOME','url'=>'news'),
		'design'	=> array('label'=>'DESIGN','url'=>'news/design'),
		'web'		=> array('label'=>'WEB','url'=>'news/web'),
		'gaming'	=> array('label'=>'GAMING','url'=>'news/gaming'),
		'skateboarding'	=> array('label'=>'SKATEBOARDING','url'=>'news/skateboarding'),
		'guestbook' => array('label'=>'FORUM','url'=>'news/forum'),
		'about' => array('label'=>'ABOUT','url'=>'news/about'),
	),
);

$bap->config['show_login_menu']=true;
$bap->config['login_menu']=array(	
	"login"			=> array("src"=>"user/login",'label'=>"Logga in"),
	"acp"			=> array("src"=>"news/authoring",'label'=>"Artiklar"),
	"logout"		=> array("src"=>"user/logout",'label'=>"Logga ut"),
	"profile"		=> array('src'=>"user/profile", 'label'=>"__profile"),
	"show_gravatar"	=> true,
	);

/**
* What type of urls should be used?
* 
* default      = 0      => index.php/controller/method/arg1/arg2/arg3
* clean        = 1      => controller/method/arg1/arg2/arg3
* querystring  = 2      => index.php?q=controller/method/arg1/arg2/arg3
*/
$bap->config['url_type'] = 1;

