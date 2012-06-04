<div id='page'>
<h3>Kommentarer</h3>

<?php if($isLoggedIn)
{
	echo "<p>Inloggad som: ".$userName."</p>";
	echo $form->getHTML();
}
	else
		echo"<p>Du måste vara inloggad för att kommentera.</p>";
		
	$post='post';
	if(isset($isThread))
		$post='showThread';
?>

<?php foreach($entries as $val):?>
<?php
$reply="comment";
if(!isset($val['replies']))
	$val['replies']=array();
	
?>

<div id='comment'>

	<a name='<?=$val['id']?>'><h4><?=$val['poet']?></h4></a>
	<p id='comment-post'><?=nl2br($val['entry'])?></p>
	<p id='comment-time'><?=$val['created']?></p>
	<p style='padding-top:1em; padding-bottom:0;'>
	<?php if($isLoggedIn):?>	
	<a href='<?=create_url("news/".$post."/".$val['asCommentTo'].'/'.$val['id']."#".$val['id'])?>'>Reply</a>
	<?php endif;?>
	</p>
	<?php
	if($replyTo!=null && $replyTo==$val['id'])
	echo $replyForm->getHTML();	
	
	?>
	<?php foreach($val['replies'] as $reply) :?>
	<div id='reply'>
		<a name='<?=$reply['id']?>'><h4><?=$reply['poet']?></h4></a>
		<p id='comment-post'><?=nl2br($reply['entry'])?></p>
		<p id='comment-time'><?=$reply['created']?></p>
		
	</div>
	<?php endforeach;?>
</div>

<?php endforeach;?>
</div>

