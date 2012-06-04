<a href="<?=create_url("news/post/".$val['key'])?>">

<div id='img-div'>
<?php
$img='design04.png';

$img=$val['image'];
$page=create_url('site/data/img/'.$img);
$x=20;
$y=0;
?>
	<img class='img-featured' src=<?=create_url('site/data/featured.image.php?url='.$page.'&x='.$x.'&y='.$y)?>/>
</div>
<div id='bg-div'></div>
<div id='text-featured'>
	<h2>
		<?php
			if(strlen($val['title'])>20)
				echo substr($val['title'],0,20)."...";
			else
				echo $val['title'];
		?>
	</h2>
	<p id='post-posted'>
		<em>Skrivet <?=$val['created']?> av <?=$val['owner']?></em>
	</p>
	<p id='post-content'>
		<?=substr(filter_data($val['data'],$val['filter']),0,80)?>...
	</p>
</div>
</a>




