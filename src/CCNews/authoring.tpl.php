<div id='page'>
<h1>Behind the scenes</h1>
<p>Här kan du redigera och skapa artiklar samt nya sidor.
</p>
<p>
<a href="<?=create_url("news/users")?>">Redigera användarkonton</a>
</p>
<p>
<a href="<?=create_url("news/create")?>">Skapa ny artikel</a>
</p><p>

<table>
<?php foreach($content as $val):?>
<tr>
<td style="width:460px; text-align:right;"><a href="<?=create_url('news/post/'.$val['key'])?>"><?=$val['title']?></a></td> 
	<td><a href="<?=create_url('news/edit/'.$val['key'])?>">  edit</a> | <a href="<?=create_url('news/remove/'.$val['id'])?>">ta bort</a></td> 
</tr>
<?php endforeach; ?>
</table

</p>
</div>
