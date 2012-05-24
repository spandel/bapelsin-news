<h1>My Guestbook</h1>
<p>Hello there dear friend. Got something to say? Why don't you share it!</p>

<?=$form->getHTML()?>

<h2>Current messages</h2>

<?php foreach($entries as $val):?>
<div id='comment'>
	<h4><?=$val['poet']?></h4>
	<p id='comment-post'><?=nl2br($val['entry'])?></p>
	<p id='comment-time'><?=$val['created']?></p>
</div>
<?php endforeach;?>
