<?php
class CMNewsComments extends CObject implements IHasSQL, IModule
{
	
	public function __construct()
	{
		parent::__construct();
	}
	public static function SQL($key=null)
	{
		$queries = array(
			'drop table guestbook'    => "DROP TABLE IF EXISTS Guestbook;",
  			'create table guestbook'  => "CREATE TABLE Guestbook (id INTEGER PRIMARY KEY, entry TEXT, poet TEXT,created DATETIME default (datetime('now')), asCommentTo INTEGER, asReplyToComment INTEGER);",
  			'insert into guestbook'   => 'INSERT INTO Guestbook (asCommentTo, asReplyToComment ,entry, poet) VALUES (?,?,?,?);',
  			'select * from guestbook' => 'SELECT * FROM Guestbook ORDER BY id ASC;',
  			'select comments from guestbook'=> 'SELECT * FROM Guestbook WHERE asReplyToComment IS NULL ORDER BY id ASC;',
  			'select with id from guestbook' => 'SELECT * FROM Guestbook WHERE asCommentTo=? ORDER BY id ASC;',
  			'select comment with id from guestbook' => 'SELECT * FROM Guestbook WHERE asCommentTo=? AND asReplyToComment IS NULL ORDER BY id ASC;',
  			'select replies to comment'=> 'SELECT * FROM Guestbook WHERE asReplyToComment=? ORDER BY id ASC;',
  			'delete from guestbook'   => 'DELETE FROM Guestbook;',
  			);
  		if(!isset($queries[$key])) 
  		{
  			throw new Exception("No such SQL query, key '$key' was not found.");
  		}
  		return $queries[$key];
	}
	public function manage($action=null)
	{
		switch($action)
		{
		case 'install':
			return $this->init();
			break;
		default:
			throw new Exception('Unsupported action for this module.');
			break;
		}
	}
  	public function init() 
  	{
  		try {
  			$this->db->query(self::SQL("drop table guestbook"));
			$this->db->query(self::SQL("create table guestbook"));
			if(isset($this->config['create_dummy_text']) && $this->config['create_dummy_text'])
				$this->createDummyText();
			return array('success', 'Created table.');
		} 
		catch(Exception$e) 
		{
			die("Failed to open database: " . $this->config['database'][0]['dsn'] . "</br>" . $e);
		}
  	}
  	public function createDummyText()
  	{
  		$entry="Violer är blå och havet är djupt,\ntacka vet jag en pripps och en spark bakut";
  		$poet='Plato';
  		$this->db->query(self::SQL("insert into guestbook"),array(1,null,$entry,$poet));
  		$entry="Rosor är röda och så är du,\nvad gör väl det om vi alla ska dö?";
  		$poet='Magnus';
  		$this->db->query(self::SQL("insert into guestbook"),array(1,1,$entry,$poet));
  		$entry="En bil att köra,\nfinns det nåt annat jag kan göra,\ndet tror jag ";
  		$poet='Ali';
  		$this->db->query(self::SQL("insert into guestbook"),array(1,null,$entry,$poet));
  	}
  	public function addNewEntry($asCommentTo, $asReplyTo,$entry, $poet) 
  	{
  		$this->db->query(self::SQL("insert into guestbook"),array($asCommentTo, $asReplyTo,$entry,$poet));
		$this->session->addMessage('success', 'Kommentar tillagd.');
		if($this->db->rowCount() != 1) 
		{
			die('Failed to insert new guestbook item into database.');
		}
  	}
  	public function emptyEntries()
	{		
		$this->db->query(self::SQL("delete from guestbook"));
		$this->session->addMessage('error', 'BURN ALL THE POETRY!!!');
	}
  	public function getEntries($id=null)
	{
		$res=array();		
		if($id!=null)
		{
			try{
				$res=$this->db->select(self::SQL('select comment with id from guestbook'),array(intval($id)));	
				
				foreach($res as &$val)
				{
					//echo "-->".$val['id'];
					$val['replies']=$this->db->select(self::SQL('select replies to comment'),array(intval($val['id'])));	
					//echo "<pre>".print_r($val['replies'],true)."</pre>";
					foreach($val['replies'] as $r)
					{
						//echo $r['poet'];
					}
				}
				return $res;
			}
			catch(Exception $e)
			{
				echo("Could not select from db<br>".$e);
			}
		}
		
		try 
		{
			$res=$this->db->select(self::SQL('select from guestbook'));	
		} 
		catch(Exception $e) 
		{
			echo("Could not select from db<br>".$e);
		}
		return $res;
	}
}
