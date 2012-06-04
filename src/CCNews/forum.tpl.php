<div id='page'>
<h1>Forum</h1>
<p>Have fun at toaster's forum!
</p>
<p>

<?php 
//echo "<pre>".print_r($content,true)."</pre>";
foreach($content as $key => $cont):?>
		
<h3 id='topic'><?=$key?>
<?php if($isLoggedIn):?>
	<em style="float:right;"><a href='<?=create_url('news/createThread/'.$key)?>'>Skapa ny tråd</a></em>
<?php endif;?>
</h3>
	<?php foreach($cont as $val):?>
	<table>
	<tr>
	<td ><a href="<?=create_url('news/showThread/'.$val['id'])?>"><?=$val['title']?></a>
	<em style='display:block;'>Av <strong><?=$val['owner']?></strong> @ <?=$val['created']?></em>
	</td> 
	</tr>
	</table>
	<?php endforeach;?>
<?php endforeach;?>


</p>
</div>
