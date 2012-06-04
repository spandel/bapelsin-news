<div id='page'>
<h1>Behind the scenes</h1>
<p>Här kan hantera användare.
</p>
<p>
<a href="<?=create_url("user/create")?>">Skapa ny användare</a>
</p><p>

<table>
<tr>
<th>Användarnamn</th><th>Riktigt namn</th><th>E-mail</th><th>Författare/Admin</th></tr>
<?php foreach($users as $val):?>
<tr>
<td>
	
		<?=$val['acronym']?> 

</td> 
<td>
<?=$val['name']?>
</td>
<td>
<?=$val['email']?>
</td>
<td>
<?php
if(isset($val['groups'][0]) && $val['groups'][0]['id'] == 1)
{
	echo "Ja <a href='".create_url("news/makeUserRegular/".$val['id'])."'>Ändra</a>";
}
else if(isset($val['groups'][0]) && $val['groups'][0]['id'] == 2)
{
	echo"Nej <a href='".create_url("news/makeUserAdmin/".$val['id'])."'>Ändra</a>";
}
else
{
	echo"-";
}

//echo"<pre>".print_r($val['groups'][0]['id'],true)."</pre>";
?> 
</td> 
</tr>
<?php endforeach; ?>
</table

</p>
</div>
