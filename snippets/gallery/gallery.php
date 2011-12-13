<?php if($page->hasImages()): ?>
<ul class="gallery">
  <?php foreach($page->images() as $image): ?>
  <li>
    <a href="<?php echo $image->url() ?>"><img src="<?php echo $image->url() ?>" width="<?php echo $image->width() ?>" height="<?php echo $image->height() ?>" alt="<?php echo $image->name() ?>" /></a>
  </li>
  <?php endforeach ?>
</ul>
<?php endif ?>