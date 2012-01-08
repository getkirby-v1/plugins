<?php 

// defaults
if(!isset($descriptionExcerpt)) $descriptionExcerpt = true; 

// send the right header
header('Content-type: text/xml; charset="utf-8"');

// echo the doctype
echo '<?xml version="1.0" encoding="utf-8"?>';

?>
<!-- generator="<?php echo c::get('feed.generator', 'Kirby') ?>" -->

<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom">

  <channel>
    <title><?php echo (isset($title)) ? xml($title) : xml($page->title()) ?></title>
    <link><?php echo (isset($link)) ? xml($link) : xml(url()) ?></link>
    <generator><?php echo c::get('feed.generator', 'Kirby') ?></generator>
    <lastBuildDate><?php echo (isset($modified)) ? date('r', $modified) : date('r', $site->modified()) ?></lastBuildDate>
    <atom:link href="<?php echo xml(thisURL()) ?>" rel="self" type="application/rss+xml" />

    <?php if($page->description() || isset($description)): ?>
    <description><?php echo (isset($description)) ? xml($description) : xml($page->description()) ?></description>
    <?php endif ?>
  
    <?php foreach($items as $item): ?>
    <item>
      <title><?php echo xml($item->title()) ?></title>  
      <link><?php echo xml($item->url()) ?></link>
      <guid><?php echo xml($item->url()) ?></guid>
      <pubDate><?php echo ($item->date()) ? date('r', $item->date()) : date('r', $item->modified()) ?></pubDate>
        
      <?php if(isset($descriptionField)): ?>
      <?php if(!$descriptionExcerpt): ?>
      <description><![CDATA[<?php echo kirbytext($item->{$descriptionField}) ?>]]></description>      
      <?php else: ?>
      <description><![CDATA[<?php echo excerpt($item->{$descriptionField}, (isset($descriptionLength)) ? $descriptionLength : 140) ?>]]></description>
      <?php endif ?>
      <?php endif ?>

    </item>
    <?php endforeach ?>
        
  </channel>
</rss>