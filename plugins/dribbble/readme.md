# Dribbble Plugin for Kirby by Simon Albrecht (1.1)
This is a plugin for [Kirby](http://getkirby.com/) that fetches shots, player-info or likes for a user on [Dribbble](http://dribbble.com/) from the [Dribbble](http://dribbble.com/)-API.

## Installation
Put the `dribbble.php` file in your `site/plugins` folder. If this folder doesn't exist yet, create it.

## Update instructions
To update, just replace the old `dribbble.php` file in `site/plugins`, with the new one.

## Limitation
The use of the [Dribbble API](http://dribbble.com/api) is restricted to a maximum of 60 API calls per minute.
If your site exceeds that maximum, think about [enabling the cache](http://getkirby.com/docs/advanced-stuff/caching) for the site.

## Example usage
	<?php
    // Load a Dribbble object for the user 's_albrecht'
    // containing 3 shots and 3 likes.
    $dribbble   = dribbble('s_albrecht', 3, true, 3);
    $shots	    = $dribbble->shots();
    $player	    = $dribbble->player();
    $likes      = $dribbble->likes();

    foreach ($shots as $shot): ?>
	
	<div class="shot">
		<h1><?php echo html($shot->title) ?></h1>
		<span class="player"><?php echo $player->name ?></span>
		<a href="<?php echo $shot->url ?>">
			<img src="<?php echo $shot->image ?>" />
		</a>
        <span class="views"><?php echo $shot->views ?> View(s)</span>
        <span class="likes"><?php echo $shot->likes ?> Like(s)</span>
	</div>
	
	<?php endforeach ?>
	
	<ul>
        <?php foreach ($likes as $like): ?>
        <li>
            <a href="<?php echo $like->url ?>" title="<?php echo $like->title ?>">
                &hearts; <?php echo $like->title ?> by <?php echo $like->player->name ?>
            </a>
        </li>
        <?php endforeach ?>
    </ul>

An example for use of this plugin can be viewed [on my website](http://albrecht.me/#dribbble).

## Attributes for the shot-object
* `$shot->id` The ID of the shot
* `$shot->title` The title of the shot
* `$shot->url` The (long) url of the shot
* `$shot->short_url` The short-url of the shot
* `$shot->image` The image-url of the shot
* `$shot->likes` The number of likes on the shot
* `$shot->views` The number of views on the shot
* `$shot->rebounds` The number of rebounds of the shot
* `$shot->comments` The number of comments on the shot
* `$shot->created` The date/time the liked post was created

## Attributes for the player-object
* `$player->id` The ID of the player
* `$player->name` The name of the player
* `$player->username` The username of the player
* `$player->avatar_url` The avatar-url of the player
* `$player->twitter` The Twitter handle of the player
* `$player->location` The location of the player
* `$player->followers` The number of followers of the player
* `$player->following` The number of users the player is following
* `$player->likes` The number of shots the player has liked

## Attributes for the like-object
* `$like->id` The ID of the liked shot
* `$like->title` The title of the liked shot
* `$like->url` The (long) url of the liked shot
* `$like->short_url` The short-url of the liked shot
* `$like->image` The image-url of the liked shot
* `$like->likes` The number of likes on the liked shot
* `$like->views` The number of views on the liked shot
* `$like->rebounds` The number of rebounds of liked the shot
* `$like->comments` The number of comments on liked the shot
* `$like->created` The date/time the liked post was created
* `$like->player` A representation of the user that created the liked shot. (same attributes as the player-object)
	
## Requirements
Your web server must have support for cURL [(installation instructions)](http://www.php.net/manual/en/curl.installation.php).

## Author & Copyright
Copyright 2012, Simon Albrecht [http://albrecht.me/](http://albrecht.me).
If you use this plugin, feel free to ping me [@s_albrecht](http://twitter.com/s_albrecht).