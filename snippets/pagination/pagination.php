<nav role="pagination">

  <?php if(!$pagination->isFirstPage()): ?>
  <a class="first" href="<?php echo $pagination->firstPageURL() ?>">first page</a>
  <?php else: ?>
  <span class="first">first page</span>
  <?php endif ?>
  
  <?php if($pagination->hasPrevPage()): ?>
  <a class="prev" href="<?php echo $pagination->prevPageURL() ?>">previous page</a>
  <?php else: ?>
  <span class="prev">previous page</span>
  <?php endif ?>

  <?php if(isset($range) && $pagination->countPages() > 1): ?> 
    <?php foreach($pagination->range($range) as $r): ?>
    <a class="range" href="<?php echo $pagination->pageURL($r) ?>"><?php echo ($pagination->page() == $r) ? '<strong>' . $r . '</strong>' : $r ?></a>
    <?php endforeach ?>
  <?php endif ?>
  
  <?php if($pagination->hasNextPage()): ?>
  <a class="next" href="<?php echo $pagination->nextPageURL() ?>">next page</a>
  <?php else: ?>
  <span class="next">next page</span>
  <?php endif ?>
  
  <?php if(!$pagination->isLastPage()): ?>
  <a class="last" href="<?php echo $pagination->lastPageURL() ?>">last page</a>
  <?php else: ?>
  <span class="last">last page</span>
  <?php endif ?>

</nav>