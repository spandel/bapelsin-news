<img class='img-page' src='<?=create_url('site/data/img/'.$contents['image']);?>'/>
<div id='page'><h1><?=$contents['title']?></h1>
<p><?php
if($contents['isPlus']!=null && !$isLoggedIn)
	echo substr($contents->getFilteredData(),0,500)."...";
else
	echo $contents->getFilteredData();
?></p>
<?php
if($contents['isPlus']!=null && !$isLoggedIn)
	echo"<h3>Detta är en plus-artikel!</h3><p>Vill du fortsätta läsa måste du vara inloggad.</p>
<p><a href='".create_url('user/login')."'>Logga in</a> <a href='".create_url('user/create')."'>Skapa konto</a> </p>
";
?>
<p><em>Taggar: <?php
$i=0;
foreach($contents['tags'] as $v)
{
	if($i>0)
		echo", ";
	echo "<a href=".create_url("news/".$v['tag']).">".$v['tag']."</a>";
	$i++;
}
?></em></p>

</div>
<br/>

