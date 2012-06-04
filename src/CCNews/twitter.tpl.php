<div id='twitter'><h3><?=$search?></h3>
<?php
$i=0;
foreach($feed as $val){?>
<p id='post-posted'><em><a href="<?=$val['link']?>"><?=$val['author']?></a> at <?=$val['date']?> </em></p>
<p id='post-content'>
<?=$val['desc']?>
</p>
<?php $i++;
if($i>19){break;}
}?>
</div>



<?php
/**
author
desc
link
date
*/
