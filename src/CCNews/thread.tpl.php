<div id='page'>
<table>
<tr>
<td style="width:150px">
		<img src='<?=get_gravatar(120)?>'/>

		<p><?=$contents['owner']?></p>
		<em><?=$contents['created']?></em>
	</td>
	<td style='vertical-align:top;'>
		<h3><?=$contents['title']?></h3>
		<p><?=$contents->getFilteredData()?></p>
	</td>
</tr>
</table>
</div>
<br/>

