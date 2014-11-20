<?php 

if(!isset($podcast)) $podcast = $page;

$defaults = array(
  'title'       => $podcast->title(),
  'link'        => url(),
  'language'    => 'en-en',
  'modified'    => $podcast->modified(),
  'copyright'   => $podcast->copyright(),
  'description' => $podcast->description(),
  'subtitle'    => $podcast->subtitle(),
  'author'      => $podcast->author(),
  'summary'     => $podcast->summary(),
  'category'    => $podcast->category(),
  'ownername'   => $podcast->ownername(),
  'owneremail'  => $podcast->owneremail(),
  'items'       => array(),  
  'image'       => ($podcast->images()->first()) ? $podcast->images()->first()->url() : false,  
);

$vars = array_merge($defaults, $vars);
extract($vars);

// send the right header
header('Content-type: text/xml; charset="utf-8"');

// echo the doctype
echo '<?xml version="1.0" encoding="utf-8"?>';

?>
<!-- generator="<?php echo c::get('feed.generator', 'Kirby') ?>" -->

<rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" version="2.0">

  <channel>

    <title><?php echo xml($title) ?></title>
    <link><?php echo xml($link) ?></link>
    <language><?php echo xml($language) ?></language>
    <generator><?php echo c::get('feed.generator', 'Kirby') ?></generator>
    <lastBuildDate><?php echo date('r', $modified) ?></lastBuildDate>
    <copyright><?php echo xml($copyright) ?></copyright>
    <description><?php echo xml($description) ?></description>

    <itunes:subtitle><?php echo xml($subtitle); ?></itunes:subtitle>
    <itunes:author><?php echo xml($author) ?></itunes:author>
    <itunes:summary><?php echo xml($summary) ?></itunes:summary>
    <itunes:owner>
      <itunes:name><?php echo xml($ownername) ?></itunes:name>
      <itunes:email><?php echo xml($owneremail) ?></itunes:email>
    </itunes:owner>
    <itunes:category text="<?php echo xml($category) ?>" />
    <itunes:image href="<?php echo $image; ?>" />

    <image>
      <url><?php echo xml($image); ?></url>
      <title><?php echo xml($title); ?></title>
      <link><?php echo xml($link); ?></link>
      <width>300</width>
      <height>300</height>
    </image>

    <?php foreach($items as $item): ?>
            
      <item>
        <title><?php echo xml($item->title()) ?></title>  
        <link><?php echo xml($item->url()) ?></link>
        <pubDate><?php echo xml(strftime('%a, %d %b %Y %R %Z', $item->date())) ?></pubDate>
          
        <description><![CDATA[<?php echo kirbytext($item->text()) ?>]]></description>

        <itunes:author><?php echo xml($author) ?></itunes:author>
        <itunes:subtitle><?php echo xml($subtitle); ?></itunes:subtitle>
        <itunes:summary><?php echo xml($summary) ?></itunes:summary>

        <?php if($image = $item->images()->first()): ?>
        <itunes:image href="<?php echo $image->url() ?>" />
        <?php endif ?>

        <?php if($mp3 = $item->files()->filterBy('extension', 'mp3')->first()): ?>
        <enclosure url="<?php echo xml($mp3->url()) ?>" length="<?php echo $mp3->size() ?>" type="audio/mpeg" />
        <guid><?php echo xml($mp3->url()) ?></guid>
        <?php endif ?>
        
        <itunes:duration><?php echo xml($item->duration()) ?></itunes:duration>
        <itunes:keywords><?php echo xml($item->keywords()) ?></itunes:keywords>

      </item>
    <?php endforeach ?>
        
  </channel>
</rss>