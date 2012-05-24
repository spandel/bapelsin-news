<?php
class CMNewsContent extends CObject implements IHasSQL, ArrayAccess, IModule {
	
	public $data;

	public function __construct($id=null) 
	{
		parent::__construct();
		if($id) 
		{
			$this->loadById($id);
		} 
		else 
		{
			$this->data = array();
		}
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
	public function offsetSet($offset, $value) { if (is_null($offset)) { $this->data[] = $value; } else { $this->data[$offset] = $value; }}
	public function offsetExists($offset) { return isset($this->data[$offset]); }
	public function offsetUnset($offset) { unset($this->data[$offset]); }
	public function offsetGet($offset) { return isset($this->data[$offset]) ? $this->data[$offset] : null; }

	public static function SQL($key=null, $args=null) 
	{
		$order_order  = isset($args['order-order']) ? $args['order-order'] : 'ASC';
		$order_by     = isset($args['order-by'])    ? $args['order-by'] : 'id';    
		$queries = array(
			'drop table content'        => "DROP TABLE IF EXISTS Content;",			
			'drop table tags'        	=> "DROP TABLE IF EXISTS Tags;",
			'drop table content2tags'   => "DROP TABLE IF EXISTS Content2Tags;",
			'create table tags'		    => "CREATE TABLE IF NOT EXISTS Tags (id INTEGER PRIMARY KEY, tag TEXT KEY, name TEXT);",
			'create table content2tags'	=> "CREATE TABLE IF NOT EXISTS Content2Tags (idContent INTEGER, idTags INTEGER, created DATETIME default (datetime('now')), PRIMARY KEY(idContent, idTags));",
			'insert into tags'       	=> 'INSERT INTO Tags (tag,name) VALUES (?,?);',
			'insert into content2tags'  => 'INSERT INTO Content2Tags (idContent,idTags) VALUES (?,?);',
			'get tags'  				=> 'SELECT * FROM Tags AS t INNER JOIN Content2Tags AS ct ON t.id=ct.idTags WHERE ct.idContent=?;',
			'get content by tag'		=> "SELECT c.*, u.acronym as owner FROM Content AS c INNER JOIN COntent2Tags AS ct ON c.id=ct.idContent INNER JOIN User as u ON c.idUser=u.id WHERE ct.idTags=? AND deleted IS NULL ORDER BY {$order_by} {$order_order};",
			'create table content'      => "CREATE TABLE IF NOT EXISTS Content (id INTEGER PRIMARY KEY, key TEXT KEY, type TEXT, title TEXT, data TEXT, filter TEXT, idUser INT, image TEXT default NULL,created DATETIME default (datetime('now')), updated DATETIME default NULL, deleted DATETIME default NULL, FOREIGN KEY(idUser) REFERENCES User(id));",
			'insert content'            => 'INSERT INTO Content (key,type,title,data,filter,idUser,image) VALUES (?,?,?,?,?,?,?);',
			'select * by id'            => 'SELECT c.*, u.acronym as owner FROM Content AS c INNER JOIN User as u ON c.idUser=u.id WHERE c.id=? AND deleted IS NULL;',
			'select * by key'           => 'SELECT c.*, u.acronym as owner FROM Content AS c INNER JOIN User as u ON c.idUser=u.id WHERE c.key=? AND deleted IS NULL;',
			'select * by type'          => "SELECT c.*, u.acronym as owner FROM Content AS c INNER JOIN User as u ON c.idUser=u.id WHERE type=? AND deleted IS NULL ORDER BY {$order_by} {$order_order};",
			'select *'                  => 'SELECT c.*, u.acronym as owner FROM Content AS c INNER JOIN User as u ON c.idUser=u.id WHERE deleted IS NULL;',
			'update content'            => "UPDATE Content SET key=?, type=?, title=?, data=?, filter=?, image=?, updated=datetime('now') WHERE id=?;",
			'update content as deleted' => "UPDATE Content SET deleted=datetime('now') WHERE id=?;",
    	 );
		if(!isset($queries[$key])) 
		{
			throw new Exception("No such SQL query, key '$key' was not found.");
		}
		return $queries[$key];
	}
	
	public function init() 
	{
		try 
		{
			$this->db->query(self::SQL('drop table content'));
			$this->db->query(self::SQL('drop table tags'));
			$this->db->query(self::SQL('drop table content2tags'));
			$this->db->query(self::SQL('create table content'));
			$this->db->query(self::SQL('create table tags'));
			$this->db->query(self::SQL('create table content2tags'));
			$this->db->query(self::SQL('insert into tags'),array('skateboarding','About skateboarding.'));
			$this->db->query(self::SQL('insert into tags'),array('web','About web developing.'));
			$this->db->query(self::SQL('insert into tags'),array('design','About design.'));
			$this->db->query(self::SQL('insert into tags'),array('gaming','About gaming.'));
			if(isset($this->config['create_dummy_text']) && $this->config['create_dummy_text'])
				$this->createDummyText();
			return array('success', 'Successfully created the database tables and created some default posts and pages, owned by you.');
		} catch(Exception$e) 
		{			
			die("$e<br/>Failed to open database: " . $this->config['database'][0]['dsn']);
		}
	}
	public function createDummyText()
	{
//1////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		$key='versionskrock';
		$title='Versionskrock';
		$article=<<<EOD
Argh! Du får tillbaka ett dokument från en nyare Adobe Indesign-version än din egen. Finns det något sätt att få upp filen själv eller är det bara att ge upp, öppna plånboken och uppgradera till en nyare version? CAP&Design testar.

Du känner säkert igen situationen. Några veckor efter en stor Adobe-uppgradering så landar en fil hos dig som du inte kan öppna. Indesign tjatar om några insticksprogram som inte fungerar och efter en snabb kontroll får du ett retfullt »Vadå, har du inte uppgraderat än?« som svar. Så vad göra? Ringa din it-avdelning eller chef och äska en ny version, eller finns det något annat knep?

Adobe har numera deklamerat att de kommer att släppa nya uppgraderingar varje år – en stor, funktionstung varannat år och sedan en en liten uppgradering åren däremellan. Den senaste uppdateringen, CS 5.5 är en liten sådan och för dig som arbetar i Indesign finns det egentligen inga kioskvältande nyheter i programmet. Du skulle alltså kunna sitta nöjd och vänta på CS 6.0 som kommer under 2012. Tyvärr så kommer det att bli lite svårt. Trots den modesta uppdateringen kan du inte öppna CS 5.5-filer i CS 5 eller tidigare versioner utan att först konvertera dina dokument.

Adobes lösning
CAP&Design ringde upp Tommi Luhtanen, som är affärsutvecklingschef för kreativ design och webb på Adobe, och ställde frågan: Finns några andra sätt, eller måste man uppgradera?

– Tyvärr finns inga genvägar för att öppna dessa filer, men det räcker med att ha en dator på arbetsplatsen som får den nya installationen, säger Tommi Luhtanen.

Adobe ser nämligen till att alla nya versioner är bakåtkompatibla, det vill säga kan spara i ett äldre format, en version bakåt. Alltså kan Indesign CS 5.5 spara i ett format som Indesign CS 5 kan öppna. CS 5 kan spara så att CS 4 kan öppna, och så vidare ända ner till den första CS-sviten.


Märkligt nog har Adobe inte någon tydligare dialogruta än så här för att tala om att filen är för ny.

Illustrator och Photoshop kan ju
Många invänder och säger att både Photoshop och Illustrator kan öppna nyare filer i äldre versioner av programmen, men att man möjligtvis får ändrade filer om man använt nyare finesser som det gamla programmet inte har. Dock kan man åtminstone öppna dem – varför fungerar det inte i just Indesign?

Illustrator kan spara bakåt, alltså till äldre format ner till version 3, som kom 1990, och Photoshop kan spara läsbara filer till version 6, som kom år 2000. Självklart kommer dessa filer inte att innehålla några av de finesser som de senare versionerna fått.

Adobe menar att Indesign-filerna är annorlunda och långt mer komplexa än Illustrator- och Photoshop-filerna. Indesigns filformat har även genomgått många fundamentala förändringar över åren då programmet gått från att vara ett rent sidombrytningsprogram för trycksaker till ett program där du kan exportera till webb, e-böcker och surfplattor med statiskt material, film och även Flash.


Uh-oh, det börjar osa katt. Det här betyder att du försöker öppna en nyare filtyp i en äldre Indesign-version.

Indesign-utvecklingsteamet har kommit fram till att man endast kan spara en version bakåt via formaten inx (Adobe interchange format) och idml (Indesign markup language). Dessa format har sina begränsningar, till exempel bibehålls inte alltid radbrytningar och avstavningar, så de är mer att betrakta som rena nödlösningar.

Incopy – lösningen?
Adobe Incopy är ett progam för att framställa och redigera text och det är tätt knutet till Indesign. En formgivare kan göra textblock där skribenten kan fylla på med text medan formgivaren fortfarande har dokumentet uppe. Om du arbetar med bara text går det att använda Incopy. Du kan använda en Incopy-version som är en version äldre än din Indesign-version och spara kompatibla filer. Har du Indesign CS 5 kan du spara CS 4-kompatibla Incopy-filer. Men det gäller alltså text, inget annat.


Nödlösningen som funkar. Exportera som inx- eller idml-fil för att skicka till en äldre version av Indesign.

Idml, inx och flera nödlösningar
Har du fått en idml- eller inx-fil av en medarbetare som har en nyare version än vad du har så är det bara att öppna den i ditt Indesign. Eller? Nja, nästan. Du kommer att få ett ganska torftigt dokument med vackra grå plattor i. Det första du får ägna dig åt är helt sonika att länka om alla bilder i dokumentet.

När du sparar om till ett äldre format kommer även fler funktioner att försvinna. Sparar du från CS 5.5 till CS 4 och vidare till CS 3 kommer exempelvis tabellfunktioner att försvinna, och det finns ingen möjlighet att återfå dem. Det gäller även om du skulle öppna CS 3-dokumentet i CS 5.5 igen.

Sista utvägen – kan man kopiera innehållet från en en nyare version av Indesign och klistra in det i en förlegad version? Svaret är även här ett nja. Kopierar du för många objekt på en gång försöker Indesign skydda sig genom att konvertera allt du klistrar in till en stor eps-fil. Kopierar du dokumentet bit för bit kan det fungera, men förvänta dig inte att någon som helst formatering följer med.

Den oundvikliga lösningen
Tyvärr finns det dåligt med nödlösningar när det gäller att få ett fungerande arbetsflöde kring äldre versioner av Indesign. På det sättet som Adobe har byggt Indesign kommer det att fungera dåligt att sitta med en äldre version förutsatt att du får Indesign-filer av frilansare eller partner. Har du kontroll över hela produktionsprocessen kan du självklart använda den version du behagar. 


Från CS 5 och 5.5 heter exportformatet idml, och det kan du öppna i CS 5 och CS 4.

I och med att Indesign hela tiden får fler funktioner, och mer riktar sig mot produktion för surfplattor och e-böcker som även inbegriper interaktivt material, blir det svårt att programmera in bakåtkompatibilitet. Och det innebär sämre lönsamhet för Adobe, om man ska vara krass.

Slutsatsen är helt enkelt att du är tvungen att uppgradera om du tar emot dokument från andra som sitter på senare versioner än du. Du kan också prenumerera eller köpa Indesign av senaste snitt och installera det på endast en dator, bara för att kunna öppna och spara om dokumenten till idml-format som kan öppnas i CS 4 och CS 5.

Alternativet är att be dina kunder eller uppdragsgivare att spara om filerna som idml på en gång. Att nästa version av Indesign skulle ha bättre bakåtkompabilitet inbyggd är föga troligt. Årliga uppdateringar har blivit en tradition som kommer att vara svår att bryta både nu och i framtiden.


Framgång. Att öppna en idml-fil i CS 4 är inga problem, men du kan behöva länka om dina bilder och kolla radbrytningarna.

Skicka kompatibla filer
Sitter du själv på den senaste installationen av Indesign och ska skicka ett dokument? Tänk på detta:

Fråga. Det är enklast att bara kolla vilken version din motpart sitter på. Versionsnumret går att hitta inifrån Indesign genom menyn Indesign > Om Indesign.
Säkra. Skicka både det öppna dokumentet, en idml-fil samt bilder och typsnitt. Det enklaste sättet är att välja Arkiv > Packa. Då får du med filen, länkarna och typsnitten. Allt du behöver göra sedan är att spara en idml-fil genom att välja Exportera > Idml och lägga den i samma mapp. Då har du gjort allt du kan.


Att spara alla CS-versioner går bra, både på Mac och pc, men det kräver gott om hårddiskutrymme.

Tips från Adobe
Det första tipset från Adobe är självklart: uppgradera. De vill sälja licenser och enklast för alla vore förstås om alla unisont kunde gå över till den senaste versionen. Med detta i åtanke kommer här Adobes tips.

Bygg inte ett arbetsflöde som är beroende av att konvertera fram och tillbaka mellan olika versioner.
Om du behöver konvertera dokument ofta rekommenderar Adobe att du uppgraderar alla licenser.
Ha åtminstone en maskin inom er organisation som har alla versioner kvar för konvertering.
Det går att ha alla versioner från CS upp till CS 5.5 på en maskin utan problem. På Windows-maskiner kan du behöva följa vissa instruktioner från adobe.com om du uppgraderar ditt operativsystem. På det viset kan du konvertera ner filer i kedjan.
EOD;
		$image='design01.jpg';
		$idTag=3;
		
		$this->db->query(self::SQL('insert content'), array($key, 'post', $title, $article, 'plain', $this->user['id'],$image));
		$idContent=$this->db->lastInsertId();
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));
		$idTag=2;
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));

//2////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		$key='bind-dina-egna-böcker';
		$title='Bind dina egna böcker';
		$article= <<<EOD
Karen Lewis visar hur du gör din egen promo materialet står ut med några kostnadseffektiva bindande och sömnad tekniker


I dagens kreativ miljö, där reklammaterial rutinmässigt skickas i form av en digital fil via e-post, kan en handgjord egenreklam boken gör en kostnadseffektiv, personlig och unikt alternativ för att hjälpa dig att stå ut. I den här guiden kommer jag förklara hur du skapar en handgjord fall inbunden bok i några få enkla steg. Vi täcker några viktiga tips för att inrätta uppslag och täcker mallar i InDesign, liksom de nödvändiga verktygen och tekniker du behöver för att binda dina egna böcker.

Om du behöver några bokbindning material är en bra källa Shepherds Falkiners. Du hittar också en illustrerad diagram över sömnadsteknik vi använde för att göra detta projekt i supportfiler att hjälpa dig när du går.
 


01 Först, ställ in din bokens sida mall i InDesign genom att skapa ett nytt dokument. Sidstorleken, kommer kolumner och marginaler beror på vad du föredrar, men kom ihåg att lägga till en extra 10mm på insidan marginalen (för att rymma stygn) och en 3 mm utfall om du tänker skriva ut till kanten på sidan. Se också till att de motstående sidorna rutan ikryssad så att du kan se din design som ett uppslag.
 


02 När du är redo att skriva ut, exportera dokumentet som en PDF och enligt mönster och blödningar, markera Skärmärken, och använda Document Bleed inställningar. Sidorna måste skrivas dubbelsidig - antingen via skrivarinställningarna eller genom att manuellt skriva alla udda sidor, kommer alla jämnar. Experimentera med papper för att se vilket som fungerar bäst. Med sidorna i rätt ordning, rita en linje på den övre sidan för att markera övre och nedre mellan skärmärken och använd en skalpell för att skära genom den inre sidan grödan markerar närmast ryggraden. Beroende på antalet sidor och papper du använder, kan du skära igenom flera sidor åt gången. Om du har massor av sidor, dela dem i sektioner och markera toppen av varje avsnitt.
 


03 Rita en linje från toppen till botten av sidan, 5 mm in från ryggen - det är där sidorna ska sys. Längs den linjen, markera en punkt 10 mm från toppen och en 10 mm från botten, sedan lägga några jämnt fördelade punkter i mellan dessa. Jag skapade tre hål åtskilda med ca 5cm, men antalet hål du behöver kommer att variera beroende på höjden på din sida. Peka på toppen av sidorna mot en plan yta för att säkerställa att de är i jämnhöjd, lägga dem platt och tränger igenom punkterna noga, med hjälp av en bokbindning syl. Håll sylen så rakt som möjligt när tränger igenom sidorna för att göra det lättare att sy.


04 att sy sidorna, använd en bokbindning nål och lintråd, fyra gånger på sidan höjden. Trä nålen, piercing tråden 1cm från slutet och dra hårt i stället för att knyta en knut. Starta i mitten hålet, tryck nålen upp genom baksidan av dina sidor, vilket ger en 10 cm lös tråd kvar. Gå runt ryggraden och tillbaka upp genom samma hål, håll den lösa tråden för att hålla sidorna ordentligt. Gå till nästa hål i endera riktningen, trä din nål ner, runt och tillbaka upp genom, sedan vidare till nästa hål. Upprepa denna process till slutet, då väven tråden tillbaka utmed längden av centrum, säkerställer den löper längs ryggradens längd på båda sidor. Vid mitten hålet, upprepa processen längs den andra halvan av boken. Avsluta tillbaka på mitten hålet och fästa med lös tråd. Skär bort eventuell kvarvarande tråd om 1 cm från knuten.
 


05 Du är nu redo att limma ryggraden. Placera din bok mellan två rena skivor - eller ett par tunga böcker - att pressa dina sidor platta, och se till att ryggraden sticker ut lite. Med en pensel, klistra in ryggraden med ett tunt lager av PVA lim (förtunnad med en skvätt vatten) och låt den stå 20 minuter för att torka. Se till att limmet inte samlar in omkring kanten av sidorna, eftersom det kan hindra dem att öppna ordentligt. När ryggraden är torr, ta bort det från din DIY bookpress och använda skalpell för att trimma bort överflödigt papper utanför skärmärken.
EOD;
		$image='design02.jpg';
		$idTag=3;
		
		$this->db->query(self::SQL('insert content'), array($key, 'post', $title, $article, 'plain', $this->user['id'],$image));
		$idContent=$this->db->lastInsertId();
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));
		
//3////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		$key='inspirationsgalleri';
		$title='Inspirationsgalleri';
		$article=<<<EOD
Inspiration nivåer få lite låg? Oroa dig inte - här är Jim McCauley med tio fler bitar av arbete för att fylla på din tank.

Förra veckan var lite ljus på gallerier, främst för att jag tillbringade ungefär en och en halv dag i möten och en hel del av resten av veckan kompensera för all den tid jag hade tillbringat i möten. Du vet hur det är, med lite tur den här veckan borde vara lite mer gallerytastic. Hjälp till att göra det så genom att twittra oss något bra!		
EOD;
		$image='design03.jpg';
		$idTag=3;
		
		$this->db->query(self::SQL('insert content'), array($key, 'post', $title, $article, 'plain', $this->user['id'],$image));
		$idContent=$this->db->lastInsertId();
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));
		$idTag=2;
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));
		
//4////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		$key='sehsucht-inspirerar-pa-och-av-scenen-pa-OFFF-2012';
		$title='Sehsucht inspirerar på och av scenen på OFFF 2012';
		$article=<<<EOD
Vi träffade upp med filmproduktionsbolag efter deras föredrag vid OFFF 2012

Sehsucht är en ganska stor sak. Filmproduktion Bolagets stor-break projektet - "Sounds of Summer" för Mercedes-Benz - revolutionerat sättet efter produktionsbolag som drivs och hur annonser i myndigheter tänkt om deras kreativa alternativ (och är fortfarande en av de mest belönade reklamfilmer för 2000-talet) .

Här är en snabb försmak av vår intervju med den ständigt lysande Sehschut på OFFF 2012 - kommer du att kunna läsa hela intervjun i Computer Arts.

Om hur de fungerar ...
Mate Steinforth, creative director och regissör: Vi är ett filmproduktionsbolag i Tyskland i Hamburg och Berlin. Vi är en hyper-produktionsbolag, vilket innebär att vi gör allt i egen regi. Fördelen för kunderna är att vi har ett team av personal där människor känner varandra - och det är där att synergieffekter börjar hända. Om du bemanna ditt företag endast frilansare, når du inte den punkten ...

Det finns en riktigt intressant studie. Den här killen gjorde en studie av Broadway pjäser i det 20th århundradet. De såg på alla spelar mellan en viss tidsperiod - de tittade på sin framgång genom sålda biljetter, och de jämförde det med hur många i laget kände varandra. Vad de upptäckt är om folk inte känner varandra i laget att det var ganska misslyckat. Men ju mer de kände varandra, desto mer framgångsrik blev det - upp till en viss nivå, och sedan somnade igen.

Om du alltid bara samma lag det blir lite tråkigt och du börjar upprepa sig själv. Det är därför vi har en riktigt stark personal på basen, kanske 80 procent, och sedan vid behov hyr vi frilansare.

På kreativitet ...
Hans Schultheiss, creative director och regissör: Vi arbetar med byråer som kommer till oss med idéer och be oss att förverkliga dem. Det låter som en nackdel, men vi förvandla det till en fördel och samarbeta med dem.

Vi presenterar oss som en kreativ enhet - konsten avdelning består av designers med olika metoder: Vi har illustratörer, rörelse designers, formgivare - och med denna enhet försöker vi att visualisera idén tillsammans med byrån. Detta hjälper oss att delta i att finna idén, inte bara i den kreativa processen att visualisera det, men den växer hela idén, som sker mer och mer. De byråer som detta tillvägagångssätt. Det är därför de kommer till oss.

Då drabbades av "fyra" klockan problem "...
MS: Vi har en låda med godis och du bara åka dit och få din Sugar Rush. Som egentligen är en riktigt dålig idé. Det finns en annan studie som visar denna sak kallas neurocentricity. För några år sedan trodde att din hjärna utvecklas tills du var ca 20 - och då stannade och nervceller dör bara. Men det är inte sant. Faktiskt din nervceller håller regenererande. Studien visade att om man motionerar oftare att det är bättre för dig, så du definitivt bör ta en promenad. Jag tar en promenad.

HS: faktiskt för mig 4 'klockan problem uppstår vid 8 "o" klocka eller senare, så jag är oftast hemma ändå.

På stampning ...
MS: Jag tycker det är en riktigt dåligt för alla inblandade. Det är dåligt för compny, är det dåligt för kunden och det är dåligt för projektet. Det är dåligt för kunden eftersom företaget måste subventionera platser och när du tror att det alltid finns tre produktionsbolag pitching, betyder det att du har en galen mängd overhead. Jag tror att vi behöver en medvetenhet i vår bransch som pitching är en dålig sak för alla inblandade. Hela animation och 3D design är i ganska dåligt skick i den meningen. Jag tror att vi måste öka och förena och säga att vi inte kommer att göra platser.

På att arbeta med de bästa ...
Stephan Wever, chef för Art Department: Vi hittade ett sätt att arbeta med riktigt bra människor i LA. Det är viktigt skott att arbeta med de bästa människor du kan få. Med Lamborghini [Sehsucht senaste "bil porn" introducerar Lamborghinis nya supersportbil, Aventador] Vi listat ut varför de bara gav oss sex veckor - eftersom de vanligtvis inte avslutar bilen i tid, som kom ut på skjutvallen dagen .. .


Regisserad av Ole Peters arbetade Sehsucht med Philipp och Keuntje att producera denna senaste verk för Lamborghini ...

... Vi hade bilen på vägen - och traction control gick inte bort. Så vi hade killen från The Fast and the Furious försöker driva bilen, men bilen höll korrigera det så att han inte kunde faktiskt skjut den. I slutet i fem minuter teknikerna fick det av och vi lyckades skjuta alla helikopter skott. Vi hade helikopterpilot från Top Gun och han spikade varje skott, det var verkligen otroligt. Det är därför om du har problem och du har goda människor och arbeta tillsammans som ett team, det fungerar riktigt bra.
EOD;
		$image='design04.png';
		$idTag=3;
		
		$this->db->query(self::SQL('insert content'), array($key, 'post', $title, $article, 'plain', $this->user['id'],$image));
		$idContent=$this->db->lastInsertId();
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));

//5////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		$key='ateruppfinna-iden-med-upper-first';
		$title='Återuppfinna idén med Upper First';
		$article=<<<EOD
Det är den sista dagen i OFFF här i den vackra staden Barcelona, ​​så när man umgås med produktion kollektiv - och medskapare av OFFF 2012: s sista titlar - Upper First? Vi drog upp en bänk med laget för att prata avslutande titlar och varför ofullkomliga är perfekt i 3D

Computer Arts: Vad är översta handlar om?
Erik Arheden: översta är en produktion kollektiv av rörelse designers, 3D och VFX artister, regissörer och producenter. Vi arbetar på ett mycket samarbete sätt, där så många av oss som möjligt väger in tidigt under processen. Ibland är allt för oss, andra gånger kan det vara bara två-tre personer. Termen "bikupa sinne" har varit till nytta för att beskriva oss i många situationer.

CA: Vilken typ av arbete gör du?
EA: Mixed-media skulle vara ett tråkigt men korrekt sätt att uttrycka det. Vi vill hitta nya uttryck på gamla teman. Det är svårt, om inte omöjligt, att komma fram till något som har / aldrig / gjorts tidigare i någon form. Så vi försöker återuppfinna idén genom uttrycksfulla bilder, ofta blandar live-action med high-end 3D. Om vi ​​kan kasta lite smuts och grus i mixen vi gärna göra det. Imperfect är perfekt i 3D. Ibland kan vi snubbla över något som är verkligt original. Sen sitter vi ner till "fika" och fira.

CA: Vad får ni på väg?
EA: Bra arbete och träning. Antingen om det är en verkligt bra idé eller om någon annan ger fantastiskt arbete som vi tenderar att bli eldas upp. Även det faktum att vi aktivt försöker upprätthålla ett liv utanför arbetet, men svårt ibland, vare sig det beach volley, korsa passform utbildning, kör, kaffe, vänner och familj.

CA: Hur håller sig fräscha?
EA: De bästa idéerna kan komma från de mest oväntade ställen, så ett sätt att bara hålla öppet. En kosmopolitisk sinne och öppenhet, som Ulrich Beck skulle säga. Haha nej, inte riktigt - ja ja, men i en inte så pretentiöst sätt. Att försöka se saker från någon annans perspektiv, att gå utanför sina egna förutfattade meningar hjälper oss att upptäcka nya saker. Eftersom vi arbetar på ett integrerat sätt, en person smak eller känslor skiljer sig från nästa. Det tvingar oss att ompröva vår egen smak nästan varje dag. Det kan vara frustrerande men är verkligen hjälpsam. Man tittar på alla typer av design, mode, film, böcker ... En idé kan komma från var som helst.

CA: Vad har varit din favorit projekt att arbeta på vid övre första så här långt?
EA: Titeln för OFFF 2012 Barcelona. Hector Ayuso kontaktade oss och frågade om vi skulle överväga att göra det i samarbete med Brosmind. Den korta var "göra något, gör det fantastiskt!" Det har varit vår favorit projektet eftersom det inte fanns några andra gränser än budget. Vi var tvungna att komma fram till något som skulle wow den kreativa gemenskap vår tid, utan pengar för att göra det. Det har varit oerhört utmanande och motiverande. Vi träffade Bros De hade en kärna av en idé som vi tolkat översta stil ...

Sekund för att att vi skulle säga MTV: s Breakfast Club av liknande skäl. Den korta var verkligen öppen och resultatet måste vara "konstig och inspirerande". Resultatet blev en levande och färgstark samling av filmer som fortfarande gör oss le.


CA: Vad kommer upp i framtiden för övre första?
EA: Vi vet inte. Förhoppningsvis mer oväntat arbete.

CA: Vem har du mest vill se på OFFF?
EA: Matt Lambert, eftersom hans verk är verkligen intressant och skiljer sig från vår. Andra skulle vara ilovedust och Radical Friend, men det kommer att bli ett gäng storheter!

CA: Hur viktigt är händelser som OFFF till Upper First?
EA: Det finns många nivåer av betydelse i händelser som OFFF. En är att fira vikten av kreativa industrier och artister. Merparten av tiden vi alla instängda i våra respektive kreativa universum. Att komma ut och träffa människor med liknande intressen och övertalning, och kunna prata ansikte mot ansikte är en riktig boost. Ovanpå att det tillåter oss att både se och synas.

CA: Om du kunde vara något djur i världen, vad skulle du vara?
EA: Enhörning eller fjäril. Vad? Kan vi säga mauve?

CA: Allt annat du vill tillägga?
EA: Ricky Bruch forever!
EOD;
		$image='design05.png';
		$idTag=3;
		
		$this->db->query(self::SQL('insert content'), array($key, 'post', $title, $article, 'plain', $this->user['id'],$image));
		$idContent=$this->db->lastInsertId();
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));		
		
		
//1////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		$key='bild-visar-forandrad-wii-u-kontroll';
		$title='Bild visar förändrad Wii U-kontroll';
		$article=<<<EOD
En testare på Traveller's Tales har twittrat ut en bild som visar en aningens förändrad Wii U-kontroll. 

Nintendo fanboys alert: look what we have at work! #BoysAndTheirToys 

Den största förändringen är att den numera har riktiga analogspakar istället för circle pads som den hade när den visades på E3 förra året. 

Just avsaknandet av analogspakar var något som jag oroat mig för och hoppats att de skulle ändra på, vilket det verkar som att de nu gjort. För även om circle pads fungerar bra till 3DS så är ändå riktiga analogspakar att föredra. Skönt att se att de nu bytts ut till ordentliga grejer. För övrigt kan man se några andra mindre förändringar såsom att några av knapparna bytt plats. 

Snart är det E3 och nu stiger hypemätaren för varje dag som går. 

Ovan kan du se en jämförelse där den förändrade modellen syns högst upp och den ursprungliga längst ner. 		
EOD;
		$image='game01.jpg';
		$idTag=4;
		
		$this->db->query(self::SQL('insert content'), array($key, 'post', $title, $article, 'plain', $this->user['id'],$image));
		$idContent=$this->db->lastInsertId();
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	
		
		
//2////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		$key='du-kommer-att-vara-hjälten-i-elder-scrolls-online';
		$title='Du kommer att vara hjälten i Elder Scrolls Online';
		$article=<<<EOD
Du kommer att vara hjälten i Elder Scrolls Online

Bethesda vill inte att fokus ska försvinna från känslan att vara hjälte när de nu utvecklar Elder Scrolls Online. Därför kommer huvudstoryn att vara 100 procent solospelande. Det säger game director Matt Firor i en intervju med Gameinformer. 

"In the Elder Scrolls games you're always the hero, whether you want to be or not. You go out there and you kill the dragons; You kill Mehrunes Dagon in Oblivion; in Morrowind, you're up there fighting the Tribunal - those are huge, global, epic things that you don't want to stand in line to do in an MMO. The last thing you want to do is have the final confrontation with Mehrunes Dagon as he's stomping across the Imperial City, and you see like 15 guys behind you waiting to kill him because they're on the same quest". 

"And we have a whole part of the game that is 100 per cent solo,which is the main story, where the world focuses on you - you are the hero, everything you do is solo and the world reacts to you that way."

Och kanske är det lite spännande med ett MMO som inte går samma väg som alla andra. Det ska bli intressant att se hur de sedan implementerar känslan av onlinespel tillsammans med ensamspelandet.		
EOD;
		$image='game02.jpg';
		$idTag=4;
		
		$this->db->query(self::SQL('insert content'), array($key, 'post', $title, $article, 'plain', $this->user['id'],$image));
		$idContent=$this->db->lastInsertId();
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	
		$idTag=2;
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	
		
//3////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		$key='blizzard-diablo-iii-ar-sakert';
		$title='Blizzard: Diablo III är säkert';
		$article=<<<EOD
Ni behöver inte oroa er för att få era konton hackade om ni är försiktiga med era kontouppgifter och använder Blizzards säkerhetssystem Authenticator. Det meddelar Blizzard som nu kommenterar de uppgifter som florerat om hackade konton. 

"We'd like to take a moment to address the recent reports that suggested that Battle.net and Diablo III may have been compromised. Historically, the release of a new game -- such as a World of Warcraft expansion -- will result in an increase in reports of individual account compromises, and that's exactly what we're seeing now with Diablo III. We know how frustrating it can be to become the victim of account theft, and as always, we're dedicated to doing everything we can to help our players keep their Battle.net accounts safe -- and we appreciate everyone who's doing their part to help protect their accounts as well."

Enligt Blizzard har alltså inte Battle.net blivit hackat på någtot vis utan det beror istället på att spelare varit oaktsamma med sina uppgifter. Men många är ändå oroade vilket märks i Blizzards forum där diskussionerna fortsätter. Vi får hoppas att Blizzard ändå har rätt och som sagt, var aktsamma med era inloggningsuppgifter.		
EOD;
		$image='game03.jpg';
		$idTag=4;
		
		$this->db->query(self::SQL('insert content'), array($key, 'post', $title, $article, 'plain', $this->user['id'],$image));
		$idContent=$this->db->lastInsertId();
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	
		$idTag=2;
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	
		

//4////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		$key='brothers-in-arms-furious-4-i-trubbel';
		$title='Brothers in Arms: Furious 4 i trubbel';
		$article=<<<EOD
Just nu går det rykten om att Gearbox tokiga andra världskrigsskjutare Brothers in Arms: Furious 4 inte längre är i produktion. 

Tydligen skall spelets utgivare, Ubisoft, nyligen ha lagt ner varumärksnamnsregistreringen för spelet något som har fått folk att spekulera att utvecklingen av spelet har hamnat i trubbel. Spelet var först planerat att släppas nu under sommaren men då man inte hört eller sett något från spelet så känns det datumet inte särskilt realistiskt just nu. Att spelets officiella Facebook-sida inte har uppdaterats på cirka nio månader bådar ju inte gott heller.
EOD;
		$image='game04.jpeg';
		$idTag=4;
		
		$this->db->query(self::SQL('insert content'), array($key, 'post', $title, $article, 'plain', $this->user['id'],$image));
		$idContent=$this->db->lastInsertId();
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	
		//$idTag=2;
		//$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	
		
		
//5////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		$key='sly-cooper-thieves-in-time-till-playStation-vita';
		$title='Sly Cooper: Thieves in Time till PlayStation Vita';
		$article=<<<EOD
Som bekant så kommer det snart ett nytt spel i en av de bättre spelserien från PlayStation 2-tiden i form av Sly Cooper: Thieves in Time. Spelet utvecklas för PlayStation 3 men under natten till i dag så bekräftade Sony att man även kommer släppa spelet till PlayStation Vita. 

Ok and now for another piece of really big news; a lot of people have been asking me about the PlayStation Vita, they feel that Sly would be a great game to take on the go. And I completely agree! SO that being said I can now announce that Sly Cooper: Thieves in Time will be available soon on BOTH the PlayStation 3 and the PlayStation Vita!

Vita-versionen är i princip identisk till PlayStation 3-motsvarigheten plus att det innehåller några PlayStation Vita-specifika funktioner. Sparfilen kommer också fungera mellan de två olika versionerna vilket ju är trevligt.
EOD;
		$image='game05.jpg';
		$idTag=4;
		
		$this->db->query(self::SQL('insert content'), array($key, 'post', $title, $article, 'plain', $this->user['id'],$image));
		$idContent=$this->db->lastInsertId();
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	
		//$idTag=2;
		//$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	
		
		
//1////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		$key='resultat-betongcupen-gustavsberg';
		$title='Resultat: Betongcupen - Gustavsberg';
		$article=<<<EOD
Betongcupen: Gustavsberg:
1. Oskar Rozenberg Hallberg 
2. Mika Edin, Stockholm 
3. David Stenström, Stockholm 
4. Bjarne Kjötta, Norge/Malmö 
5. Matt Marek, Stockholm, USA 
6. Simon Karlsson, Stockholm 
 
Get Set Go Girl Jam:
1. Emma Fastesson Lindgren, Malmö 
2. Sara Meurle, Blentrap 
3. Indra Trabolb, Gävle 
 
Roofys Rippin Helmet Award:
Indra Trabolb, Gävle

Följ gärna Betongcupen på facebook.
http://facebook.com/betongcupen
EOD;
		$image='skate01.jpg';
		$idTag=1;
		
		$this->db->query(self::SQL('insert content'), array($key, 'post', $title, $article, 'plain', $this->user['id'],$image));
		$idContent=$this->db->lastInsertId();
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	
		//$idTag=2;
		//$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));
		
		
//2////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		$key='dags-for-forsta-stoppet-av-red-bull-manny-mania';
		$title='Dags för första stoppet av Red Bull Manny Mania';
		$article=<<<EOD
Red Bull Manny Mania är en ”manual” skatetävling och arrangeras i Sverige för tredje året i rad. Det unika konceptet för tävlingen är utvecklat av Red Bull USA tillsammans med Joey Brezinski. Kvalturnén i år innefattar 4st stopp runt om i Sverige. Finalen kommer att hållas i Malmö dit 4st personer från varje kval bjuds in och har chans att tävla om en plats i den internationella Red Bull Manny Mania finalen i New York.
EOD;
		$image='skate02.jpg';
		$idTag=1;
		
		$this->db->query(self::SQL('insert content'), array($key, 'post', $title, $article, 'plain', $this->user['id'],$image));
		$idContent=$this->db->lastInsertId();
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	
		//$idTag=2;
		//$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	
		
		
//3////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		$key='anmal-dig-till-lisebergs-classics';
		$title='Anmäl dig till Lisebergs Classics';
		$article=<<<EOD
31:a maj till 2:a juni blir det dags för den största skateboardtävlingen i Sveriges historia då Tacky tillsammans med Liseberg, Comviq, Returpack och Junkyard arrangerar Liseberg Classics. Anmäl dig till torsdagens kval för upp till 100 åkare här. Betalning till kontoi förväg eller på plats. Observera att inträde till Liseberg ingår i tävlingsavgiften.

Var och när:
Liseberg, Göteborg. Torsdag 31:a maj till lördag 2:a juni
 
Skate på Liseberg arrangeras tillsammans med Liseberg, Comviq, Returpack och Junkyard  och kommer bli en utav de mest omtalade och största skateboardtävlingarna i Sveriges historia. Med läget inne på Liseberg och framför den stora scenen kommer vi att kunna skapa en plats som blir helt unik. 3 dagar fullspäckade av skateboardupplevelser, berg & dalbaneåkande samt 3 nätter av hej vilt festande. I sommar kommer Göteborg få sig en gigantisk kyss av skateboard!

Tävlingen - Liseberg Classics: 
För att ge lokala och nationella skateboardåkare en möjlighet att vara med på detta så kommer det på torsdagen att köras ett förkval. Där får 100 deltagare slåss om 10 platser in till det stora internationella kvalet som startar på fredagen. I lördagens final kommer sedan ca 30 000:- i prispengar stå på spel.
 
Anmälan:
Till torsdagens kval anmäler du dig i formuläret som du hittar längst ner i denna artikel. 100 åkare i detta kval!
Anmälningsavgift 100:- betalas in till:
Handelsbanken Clearing-nr: 6102  Kontonummer: 728 045 192  (Innehavare Mark Pulman)
OBS! Skriv ditt namn vid banköverföringen!
Anmälan är inte fullständig förrän betalning kommit in.
OBS! Det kommer även gå bra att betala på plats. Inträde till Liseberg ingår i tävlingsavgiften!
 
Tävlingen kommer att köras i jam-format i kvalet och semifinal medans finalen körs i form av enskilda åk. 

Huvudtävlingen är en inbjudningstävling där 30 skatare kommer göra upp tillsammans med de 10 bästa från det nationella kvalet på torsdagen.
EOD;
		$image='skate03.jpg';
		$idTag=1;
		
		$this->db->query(self::SQL('insert content'), array($key, 'post', $title, $article, 'plain', $this->user['id'],$image));
		$idContent=$this->db->lastInsertId();
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	
		//$idTag=2;
		//$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));
		
		
//4////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		$key='inte-mycket-återstår-innan-hoganasparken-star-klar';
		$title='Inte mycket återstår innan Höganäsparken står klar';
		$article=<<<EOD
Betongparken i Höganäs är där Tacky Skatecamps - Camp Junkyard kommer att äga rum i sommar står snart helt klart. Nu återstår i princip bara asfalteringen av flatdelen av parken. Tänk dock på att parken inte är öppen för åkning ännu!
EOD;
		$image='skate04.jpg';
		$idTag=1;
		
		$this->db->query(self::SQL('insert content'), array($key, 'post', $title, $article, 'plain', $this->user['id'],$image));
		$idContent=$this->db->lastInsertId();
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	
		//$idTag=2;
		//$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	
		
		
//5////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		$key='girls-on-board-varens-ultimata-tjejevent';
		$title='Girls on Board - Vårens ultimata tjejevent';
		$article=<<<EOD
Missa inte vårens ultimata tjejevent nu på lördag. Girls On Board är en helkväll för chicas som diggar brädor och kläder. Junkyard bjuder denna kväll in DIG, leverantörer, skate-, snow- & longboardåkare, dj's, storbloggare och annat soft folk. Kvällen avslutas med en grym efterfest för både boys & girls, se till att boka biljetter och rabatterat boende redan idag!
 
Var och när:
Lördagen den 25:e maj.
Junkyard, Överby, Trollhättan
Scandic Swania, Trollhättan
 
Till vår markbutik i Trollhättan har vi denna kväll bjudit in DIG, leverantörer, skate-, snow- & longboardåkare, dj's, storbloggare och annat soft folk. 

På schemat denna kväll står mingel, longboard-demo, skateuppvisningar, bbq, filmvisning, häng med professionella åkare och bloggerskor, shopping, chans att vinna longboard, möjlighet att få kvällens makeup fixad och en hel massa annat kul!

Inte nog med det så får alla som handlar 20% rabatt på hela köpet! De 50 första personerna som handlar över 500kr tilldelas en goodiebag fullproppad med göttigheter från våra leverantörer på plats.

Aktiviteter:
- Mingel och gött häng
- Tävling
- 20% rabatt på alla köp
- Longboard demo
- Skateuppvisning
- BBQ
- Nikita make up corner
- Filmvisning
- Dj

Åkare:
- Tove Holmgren (snowboard)
- Cecilia Larsen (snowboard)
- LGC (longboard)
- No Limit (skateboard)

Leverantörer på plats:
- Nikita
- Roxy
- Femipleasure
- Eivy
- Svea
- Somewear

Bloggerskor:
- Caty & Keela

Efteråt bär det av till Scandic Hotell Swania där en hård efterfest bränns av, där är även boys varmt välkomna:
junkyard.ly/GirlsOnBoardAfterparty

Scandic Swania erbjuder i samband med eventet dubbelrum för endast 750kr (!!!). Ange rabattkoden "Junkyard" i kassan när du gör din bokning:
junkyard.ly/ScandicSwania

Håll dig uppdaterad på eventets Facebooksida för att inte missa några grymma nyheter!
junkyard.ly/GirlsOnBoardFB

Börja tagga och dra med dig brudarna så ses vi i Trollhättan den 25e maj!
 
Relaterat:
Bildserie: Tacky firar internationella kvinnodagen med massa brädrippande tjejer 

Vårens grymmaste tjejevent avslutas med en episk afton på Trollhättans tyngsta dansgolv på Scandic Swania. Alla boys and girls över 18 år är välkomna!

Förköpsbiljetter hittar du i vår markbutik i Trollhättan för endast 80 riksdaler. Biljetterna släpps den 4/5, men sitt inte och vänta på bättre väder - de lär ta slut snabbt!

Portarna öppnar kl 21 och det är magiskt bra priser i baren ända fram till kl 22.
EOD;
		$image='skate05.jpg';
		$idTag=1;
		
		$this->db->query(self::SQL('insert content'), array($key, 'post', $title, $article, 'plain', $this->user['id'],$image));
		$idContent=$this->db->lastInsertId();
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	
		//$idTag=2;
		//$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	
		
		
//1////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		$key='chrome-nu-storre-an-internet-explorer';
		$title='Chrome nu större än Internet Explorer';
		$article=<<<EOD
För första gången någonsin har nu Googles webbläsare Chrome varit större än Microsofts Internet Explorer en hel vecka. 

Tidigare i år var Chrome största webbläsaren under en dag, en söndag, men när veckan började igen på måndagen så gick IE-användandet upp. Något som antagligen tyder på att många kör Chrome hemma medan IE fortfarande dominerar på en hel del arbetsplatser. 

Efter Chrome och Internet Explorer hittar vi i tur och ordning Firefox, Safari och Opera i Statcounters statistik som är inhämtad globalt från över tre miljoner webbplatser som sammanlagt har över 15 miljarder besök varje månad.
EOD;
		$image='web01.jpg';
		$idTag=2;
		
		$this->db->query(self::SQL('insert content'), array($key, 'post', $title, $article, 'plain', $this->user['id'],$image));
		$idContent=$this->db->lastInsertId();
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	
		//$idTag=3;
		//$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));
		
//2////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		$key='microsofts-socl-lamnar-beta-stadiet';
		$title='Microsofts So.cl lämnar beta-stadiet';
		$article=<<<EOD
I slutet av förra året så släppte Microsoft en sociala medie-tjänst som man kallar för So.cl i beta-version och bjöd in elever från tre amerikanska universitet som beta-användare. Nu har Microsoft dock tagit So.cl ur beta-stadiet och alla intresserade kan ansluta sig. 

Microsoft påpekar att man primärt än så länge vill att So.cl ska vara en plattform för studenter och på sajten kan man koppla ihop sig med sina vänner och därefter starta ämnes-baserade trådar till vilka man kan bjuda in sina kontakter för dikussion om dessa. Det finns även en videochatt med vilken man kan videochatta med en eller flera kontakter. 

So.cl gick initialt under namnet Tulalip men bytte till So.cl efter det att Micrsoft kommit över den domänen. Vill du testa själv så hoppa vidare på länken nedan och anslut dig via Facebook Connect eller ditt Window Live-konto.
EOD;
		$image='web02.jpg';
		$idTag=2;
		
		$this->db->query(self::SQL('insert content'), array($key, 'post', $title, $article, 'plain', $this->user['id'],$image));
		$idContent=$this->db->lastInsertId();
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	
		//$idTag=3;
		//$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));
		
		
		
//3////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		$key='mark-zuckerberg-gifter-sig';
		$title='Mark Zuckerberg gifter sig';
		$article=<<<EOD
I veckan fick Mark Zuckerberg in Facebook på börsen och i går kunde han lägga till en ny händelse på sin timeline då han gifte sig med flickvännen sedan länge: Priscilla Chan. 

100 gäster hade blivit bjudna hem till Mark och Priscilla för att delta i firandet av hennes examen - trodde det. Istället var det giftemål som stod på agendan och Priscilla fick en ring på sitt finger. 
EOD;
		$image='web03.jpg';
		$idTag=2;
		
		$this->db->query(self::SQL('insert content'), array($key, 'post', $title, $article, 'plain', $this->user['id'],$image));
		$idContent=$this->db->lastInsertId();
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	
		//$idTag=3;
		//$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	
		
//4////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		$key='stad-i-wales-blir-wikipedia-kompatibel';
		$title='Stad i Wales blir Wikipedia-kompatibel';
		$article=<<<EOD
Staden Monmouth i Wales blir världens första "Wikipedia-stad" vilket innebär att invånare och besökare i staden ska kunna få information om det mesta av intresse som staden har att erbjuda. 

Konkret innebär det att Monmoth förbinder sig att ha gratis WiFi för alla i hela staden, den första staden i Wales om erbjuder detta, samt att man sätter upp QR-koder på sevärdheter och liknande på platser runt om i staden. Runt 1 000 QR-koder finns än så länge uppsatta och dessa leder till Wikipedia-sidor som är skrivna på besökarens språk. 

Under "Läs mer" finns en kortare film som beskriver Monmouthpedia lite närmare.
EOD;
		$image='web04.jpg';
		$idTag=2;
		
		$this->db->query(self::SQL('insert content'), array($key, 'post', $title, $article, 'plain', $this->user['id'],$image));
		$idContent=$this->db->lastInsertId();
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	
		$idTag=3;
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	


//5////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		$key='hitta-designer-till-din-app-pa-meer-li';
		$title='Hitta designer till din app på Meer.li';
		$article=<<<EOD
Meer.li är en nystartad community för designers som vill visa upp sina färdigheter när det kommer till att designa mobila appar och där besökare kan kolla in deras verk. 

Varje designer har möjlighet att lägga upp sina verk med en kort projektbeskrivning och skulle man vara intresserad av att anställa eller hyra in designern för ett apprelaterat jobb så finns det kontaktmöjligheter i anslutning till de aktuella design-förslagen. 

Meer.li är utvecklad av elever på svenska Hyper Island och rullar än så länge i beta-version.
EOD;
		$image='web05.jpg';
		$idTag=2;
		
		$this->db->query(self::SQL('insert content'), array($key, 'post', $title, $article, 'plain', $this->user['id'],$image));
		$idContent=$this->db->lastInsertId();
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	
		$idTag=3;
		$this->db->query(self::SQL('insert into content2tags'),array($idContent,$idTag));	
		

		
		/* tags
		1 skateboarding 
		2 web 
		3 design 
		4 gaming*/
		
		/* 
		$this->db->query(self::SQL('insert content'), array('hello-world', 'post', 'Hello World', 'This is a demo post. tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco', 'plain', $this->user['id'],'game03.jpg'));
		$firstpostID=$this->db->lastInsertId();
		$this->db->query(self::SQL('insert into content2tags'),array(1,1));
		$this->db->query(self::SQL('insert content'), array('hello-world2', 'post', 'Hello World2', 'This is a demo post. Again. tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco', 'plain', $this->user['id'],'game03.jpg'));
		$this->db->query(self::SQL('insert into content2tags'),array(2,2));
		$this->db->query(self::SQL('insert content'), array('hello-world4', 'post', 'Hello World4', '<strong>Lorem ipsum dolor sit amet,</strong> html purify consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum', 'htmlpurify', $this->user['id'],'game03.jpg'));
		$this->db->query(self::SQL('insert into content2tags'),array(3,3));
		$this->db->query(self::SQL('insert content'), array('hello-world3', 'post', 'Hello World3', 'This is a demo post. tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco', 'plain', $this->user['id'],'game03.jpg'));
		$this->db->query(self::SQL('insert into content2tags'),array(4,4));
		$this->db->query(self::SQL('insert content'), array('hello-world5', 'post', 'Hello World5', '[b]Lorem ipsum[/b] bbcode dolor sit amet, [i]consectetur adipisicing elit,[/i] sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum', 'bbcode', $this->user['id'],'game03.jpg'));
		$this->db->query(self::SQL('insert into content2tags'),array(5,1));
		$this->db->query(self::SQL('insert into content2tags'),array(1,2));
		$this->db->query(self::SQL('insert into content2tags'),array(2,3));
		*/
	}
  
	public function save() 
	{
		if(!isset($this['image']))
			$this['image']=null;
		
		$msg = null;
		if($this['id']) 
		{
			$this->db->query(self::SQL('update content'), array($this['key'], $this['type'], $this['title'], $this['data'], $this['filter'], $this['id'], $this['image']));
			$msg = 'update';
		} else 
		{
			$this->db->query(self::SQL('insert content'), array($this['key'], $this['type'], $this['title'], $this['data'], $this['filter'], $this->user['id'], $this['image']));
			$this['id'] = $this->db->lastInsertId();
			$msg = 'created';
		}
		$rowcount = $this->db->rowCount();
		if($rowcount) 
		{
			$this->session->addMessage('success', "Successfully {$msg} content '{$this['key']}'.");
		} else 
		{
			$this->session->addMessage('error', "Failed to {$msg} content '{$this['key']}'.");
		}
		return $rowcount === 1;
	}
    
	public function loadById($id) 
	{
		$res = $this->db->select(self::SQL('select * by id'), array($id));
		if(empty($res)) 
		{
			$this->session->addMessage('error', "Failed to load content with id '$id'.");
			return false;
		} 
		else 
		{
			$this->data = $res[0];
		}
		return true;
	}
	public function remove($id)
	{
		$this->db->query(self::SQL('update content as deleted'), array($id));
		$this->session->addMessage('success', "Successfully removed content '{$id}'.");
	}
	public function listAllTags($args=null)
	{
		try 
		{
			if(isset($args)) 
			{
				return $this->db->select(self::SQL('get tags', $args), array($args['id']));
			} 
			else 
			{
				return $this->db->select(self::SQL('select *', $args));
			}
		} catch(Exception $e) 
		{
			echo $e;
			return null;
		}
	} 
	public function listAllByTag($args=null) 
	{
		try 
		{
			if(isset($args)) 
			{
				return $this->db->select(self::SQL('get content by tag', $args), array($args['tag']));
			} 
			else 
			{
				return $this->db->select(self::SQL('select *', $args));
			}
		} catch(Exception $e) 
		{
			echo $e;
			return null;
		}
	}  
	public function listAll($args=null) 
	{
		try 
		{
			if(isset($args) && isset($args['type'])) 
			{
				return $this->db->select(self::SQL('select * by type', $args), array($args['type']));
			} 
			else 
			{
				return $this->db->select(self::SQL('select *', $args));
			}
		} catch(Exception $e) 
		{
			echo $e;
			return null;
		}
	}  
	public static function filter($data, $filter)
	{
		switch($filter)
		{/*
		case 'php':
			$data=nl2br(makeClickable(eval('?>'.$data)));
			break;
		case 'html':
			$data=nl2br(makeClickable($data));
			break;*/
		case 'htmlpurify':
			$data=nl2br(CHTMLPurifier::purify($data));
			break;
		case 'bbcode':
			$data=nl2br(bbcode2html(htmlent($data)));
			break;
		case 'plain':
		default:
			$data=nl2br(makeClickable(htmlent($data)));
		}
		return $data;
	}
	public function getFilteredData()
	{
		return $this->filter($this['data'],$this['filter']);
	}
}
