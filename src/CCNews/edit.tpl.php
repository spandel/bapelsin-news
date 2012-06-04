<div id='page'>
<?php if(isset($content['created'])): ?>
  <h1>Redigera artikel</h1>
  <p>Här kan du redigera och spara din artikel.</p>
<?php else: ?>
  <h1>Skapa artikel</h1>
  <p>Skriv en ny artikel.</p>
<?php endif; ?>


<?=$form->getHTML(array('class'=>'content-edit'))?>

<p class='smaller-text'><em>
<?php if(isset($content['created'])): ?>
  Denna artikel skapades av <?=$content[0]['owner']?> <?=$content[0]['created']?>.
<?php else: ?>
  Artikeln har ännu inte skapats.
<?php endif; ?>

<?php if(isset($content['updated'])):?>
  Senast uppdaterad <?=$content['updated']?>.
<?php endif; ?>
</em></p>

<p><a href='<?=create_url('news/authoring')?>'>Visa alla</a></p>
</div>
