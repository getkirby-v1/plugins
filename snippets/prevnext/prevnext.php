<?php if($page->hasPrevVisible()): ?>
<a href="<?php echo $page->prevVisible()->url() ?>">previous page</a>
<?php endif ?>

<?php if($page->hasNextVisible()): ?>
<a href="<?php echo $page->nextVisible()->url() ?>">next page</a>
<?php endif ?>
