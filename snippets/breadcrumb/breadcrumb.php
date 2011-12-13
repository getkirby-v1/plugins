<nav class="breadcrumb">
  <ul>
    <?php foreach($site->breadcrumb() AS $crumb): ?>
    <li><a<?php echo ($crumb->isActive()) ? ' class="active"' : '' ?> href="<?php echo $crumb->url() ?>"><?php echo $crumb->title() ?> &rsaquo;</a></li>
    <?php endforeach ?>
  </ul>
</nav>