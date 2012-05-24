
<h3>Kommentarer</h3>
<?=$form->getHTML()?>
<?php foreach($entries as $val):?>
<div id='comment'>
	<h4><?=$val['poet']?></h4>
	<p id='comment-post'><?=nl2br($val['entry'])?></p>
	<p id='comment-time'><?=$val['created']?></p>
</div>

<?php endforeach;?>
</div>

