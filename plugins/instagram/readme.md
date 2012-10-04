# Instagram Plugin for Kirby by Simon Albrecht (1.0)
This is a plugin for [Kirby](http://getkirby.com/) that loads images from the [Instagram](http://instagram.com/) API.

## Installation
1. Put the `instagram.php` file in your `site/plugins` folder. If this folder doesn't exist yet, create it.
2. In order to interact with the Instagram API, you need to obtain an access token for yourself.
3. Visit <http://instagram.com/developer/clients/manage/> and register an application.
4. Set the OAuth redirect_uri to the main URL (i.e. http://yourdomain.com) of your website. 
5. Copy the Client-ID of the newly created app.
6. Visit: 

		https://instagram.com/oauth/authorize/?client_id=CLIENT-ID&redirect_uri=http://yourdomain.com&response_type=token

…in your browser, but replace CLIENT-ID with your client-id and YOURDOMAIN.COM with the OAuth redirect_uri you've entered while registering your app. 

7. The browser will redirect you to your own website and in the address bar you will find your access token like this: 

		http://yourdomain.com/#access_token=xxxxx.xxxxx.xxxxxxxxxxx

8. Copy the access token (everything after =) and save it somewhere. 
9. Implement the plugin into your template.

## Update instructions
To update, just replace the old `instagram.php` file in `site/plugins`, with the new one.

## Example usage
	<?php
    // Load an instagram object using your access_token (see installation)
    // containing 10 shots.
    // Note: Replace XXX… with your access_token
    $instagram  = instagram('XXX.XXXXXXX.XXXXXXXXXXXXXXXXXXXXXXXXX', 10);
    $images	    = $instagram->images();

    foreach ($images as $image): ?>
        <div class="instagram-photo">
            <a href="<?php echo $image->link ?>"><img src="<?php echo $image->url ?>" /></a>
            <div class="location">
                <span>
                    <?php echo $image->location ?> (<?php echo $image->latitude ?>, <?php echo $image->longitude ?>)
                </span>
            </div>
        </div>
	<?php endforeach ?>
	
**Advanced Users:** See the source for further options.

## Attributes for the image
* `$image->link` The link to the image
* `$image->comments` The number of comments
* `$image->likes` The number of likes
* `$image->created` The timestamp when the image was created
* `$image->thumb` The url of the thumbnail of the image
* `$image->url` The url of the full-sized image
* `$image->image_lowres` The url to a low-res version of the image
* `$image->filter` The filter that was used
* `$image->location` The location name
* `$image->latitude` The latitude of the location
* `$image->longitude` The longitude of the location
* `$image->tags` An array of tags of the image

## Attributes for the user-object
* `$user->username` The username of the user
* `$user->full_name` The full name of the user
* `$user->picture` The url to the avatar of the user

## Requirements
Your web server must have support for cURL [(installation instructions)](http://www.php.net/manual/en/curl.installation.php).

## Author
Copyright 2012, Simon Albrecht [http://albrecht.me/](http://albrecht.me).
If you use this plugin, feel free to ping me [@s_albrecht](http://twitter.com/s_albrecht).