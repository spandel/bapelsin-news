<?php if(isset($contents['image'])) :?>
	<img class='img-page' src='<?=create_url('site/data/img/'.$contents['image']);?>'/>
<?php endif;?>

<div id='page'><h1><?=$contents['title']?></h1>
<p><?=$contents->getFilteredData()?></p>
</div>
<br/>

