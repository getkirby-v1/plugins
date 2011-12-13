<?php if($page->hasPrev()): ?>
<a href="<?php echo $page->prev()->url() ?>">previous page</a>
<?php endif ?>

<?php if($page->hasNext()): ?>
<a href="<?php echo $page->next()->url() ?>">next page</a>
<?php endif ?>