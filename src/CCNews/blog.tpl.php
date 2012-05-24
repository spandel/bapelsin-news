
<?php foreach($contents as $val) :?>
<div id='blog-image'>
	<a href="<?=create_url("news/post/".$val['id'])?>">	
	<?php
		$img='design04.png';
		$img=$val['image'];
		$page=create_url('site/data/img/'.$img);
	?>
	
	
		<img src='<?=$page?>'/>
	</a>
</div>
<div id='post'>
	<h1>
		<a href="<?=create_url("news/post/".$val['id'])?>">
			<?=$val['title']?>
		</a>
	</h1>
	
	<p id='post-posted'>
		<em>Posted on <?=$val['created']?> by <?=$val['owner']?></em>
	</p>
	
	<p id='post-content'>
	
		<?php
		/*
		if(strlen($val['title'])>20)
				echo substr($val['title'],0,20)."...";
			else
				echo $val['title'];
		*/
		$dots='';
		if(strlen(filter_data($val['data'],$val['filter']))>500)
				$dots="...";
			
		echo substr(filter_data($val['data'],$val['filter']),0,500);
		echo $dots;
		?>
		<a href="<?=create_url("news/post/".$val['id'])?>">	Läs mer</a>
	</p>
	
	<em>tags: <?php
$i=0;
foreach($val['tags'] as $v)
{
	if($i>0)
		echo", ";
	echo "<a href=".create_url("news/".$v['tag']).">".$v['tag']."</a>";
	$i++;
}
?>
	</em>
</div>
<?php endforeach; ?>


