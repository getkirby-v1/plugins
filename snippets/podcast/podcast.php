<?php 
    header('Content-type: text/xml; charset="utf-8"');
    echo '<?xml version="1.0" encoding="UTF-8"?>'; 
?>
<!-- generator="<?php echo c::get('podcast.generator', 'Kirby') ?>" -->
<rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" version="2.0">
    <channel>
        <title><?php echo xml($page->title()); ?></title>
        <link><?php echo xml($page->url()); ?></link>
        <language><?php echo xml($page->language()); ?></language>
        <copyright><?php echo xml($page->copyright()); ?></copyright>
        <itunes:subtitle><?php echo xml($page->subtitle()); ?></itunes:subtitle>
        <itunes:author><?php echo xml($page->author()); ?></itunes:author>
        <itunes:summary><?php echo xml($page->summary()); ?></itunes:summary>
        <description><?php echo xml($page->summary()); ?></description>
        <itunes:explicit><?php echo xml($page->explicit()); ?></itunes:explicit>
        <itunes:owner>
        <itunes:name><?php echo xml($page->author()); ?></itunes:name>
        <itunes:email><?php echo xml($page->mail()); ?></itunes:email>
        </itunes:owner>
        <itunes:image href="<?php 
            if($page->hasImages()):
                echo xml($page->images()->first()->url());
            endif;
        ?>" />
        <?php foreach($categories as $key => $category):?>
        <?php if(is_array($category)): ?>
        <itunes:category text="<?php echo $key; ?>">
        <?php foreach($category as $key => $subcategory): ?>
            <itunes:category text="<?php echo xml($subcategory); ?>"/>
        <?php endforeach; ?>
        </itunes:category>
        <?php else: ?>
        <itunes:category text="<?php echo xml($category); ?>"/>
        <?php endif; ?>
        <?php endforeach; ?>
        <?php foreach($items as $item):
        // CHECK IF HAS SOUNDS OR VIDEO
        if($item->hasSounds()):
            $file = $item->sounds()->first();
        elseif($item->hasVideo()):
            $file = $item->videos()->first();
        else:
            echo '<!-- No sound or video on ' . $item->title() . '-->';
            continue;
        endif;
        ?>
        <item>
            <title><?php echo xml($item->title()); ?></title>
            <itunes:author><?php echo xml($page->author); ?></itunes:author>
            <itunes:subtitle><?php echo xml(($item->subtitle() == '' ? '' : $item->subtitle())); ?></itunes:subtitle>
            <itunes:summary><?php echo xml(($item->shownotes() == '' ? '' : $item->shownotes())); ?></itunes:summary>
            <itunes:image href="<?php 
                if($item->hasImages()):
                    echo $item->images()->first()->url();
                else:
                    echo $page->images()->first()->url();
                endif;                    
            ?>" />
            <enclosure url="<?php echo xml($file->url()); ?>" length="<?php echo xml($file->size()); ?>" type="<?php 
                // Custom switch because of sometimes wront Mimetype in mime()
                switch($file->extension()) {
                    case 'mp3':
                        echo 'audio/mpeg';
                        break;
                    case 'm4a':
                        echo 'audio/x-m4a';
                        break;
                    case 'mp4':
                        echo 'video/mp4';
                        break;
                    case 'm4v':
                        echo 'video/x-m4v';
                        break;
                    case 'mov':
                        echo 'video/quicktime';
                        break;
                    case 'pdf':
                        echo 'application/pdf';
                        break;
                    case 'epub':
                        echo 'document/x-epub';
                        break;
                    default:
                        echo xml($file->mime()); 
                        break;
                }

            ?>" />
            <guid><?php echo xml($file->url()); ?></guid>
            <pubDate><?php echo date('D, d M Y H:i:s e', $item->date()); ?></pubDate>
            <itunes:duration><?php echo xml($item->duration()); ?></itunes:duration>
            <itunes:keywords><?php echo xml($item->keywords()); ?></itunes:keywords>
        </item>
      <?php endforeach; ?>
    </channel>
</rss>
