<?php if(!isset($subpages)) $subpages = $site->pages() ?>
<ul>
  <?php foreach($subpages->visible() AS $p): ?>
  <li class="depth-<?php echo $p->depth() ?>">
    <a<?php echo ($p->isActive()) ? ' class="active"' : '' ?> href="<?php echo $p->url() ?>"><?php echo $p->title() ?></a>
    <?php if($p->hasChildren()): ?>
    <?php snippet('treemenu', array('subpages' => $p->children())) ?>
    <?php endif ?>
  </li>
  <?php endforeach ?>
</ul>