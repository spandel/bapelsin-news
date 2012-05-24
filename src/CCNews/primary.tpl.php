<div id='blog-image'>
	<img src='http://www.student.bth.se/~dasp11/phpmvc/projekt/Bapelsin/site/data/img/game05.png'/>
</div>
<div id='post'>
<h1><a href="<?=create_url("news/post/".$val['id'])?>"><?=$val['title']?></a></h1>
<p id='post-posted'><em>Posted on <?=$val['created']?> by <?=$val['owner']?></em></p>
<p id='post-content'>
<?=filter_data($val['data'],$val['filter'])?>
</p>
</div>


