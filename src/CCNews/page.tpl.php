<img class='img-page' src='<?=create_url('site/data/img/'.$contents['image']);?>'/>
<div id='page'><h1><?=$contents['title']?></h1>
<p><?=$contents->getFilteredData()?></p>
<p><em>tags: <?php
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
<div id='page'>
