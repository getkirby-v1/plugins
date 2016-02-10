# Kirbytext Photo Extension (1.0)

A simple extension for [Kirby's](http://getkirby.com/) kirbytext which automatically parses the EXIF data of a photo and displays it along with the photo itself.

The extension will display the camera model, the f-stop, the exposure time, the focal length as well as the ISO setting.

## Installation
Copy the `kirbytext.photo.php` file to the `site/plugins` directory.

## Basic Usage
The extension is very easy to use. It's just one tag.

	(photo: dscf1000_foo.jpg)
	
This will generate the following HTML:

	<figure>
		<img src="http://foo.bar/.../dscf1000_foo.jpg"> 
		<figcaption>
			<p>
				<span class="photo-title">dscf1000_foo.jpg</span>
				<span class="photo-exif">FUJIFILM X-E1, 55mm, F/1.4, 5s, ISO 200</span>
			</p> 
		</figcaption>
	</figure>
	
	
## Advanced Usage
You can use these additional attributes with the tag:

- `title` to set a title for the image (if not set, the filename is displayed)
- `alt` to display an alternative text
- `class` to set a CSS class
- `link` to add a link to the image

Here's the full construct:

	(photo: dscf1000_foo.jpg title: My Cat alt: A picture of my cat class: larger link: http://foo.bar/)
    
This will output the following HTML:

	<figure class="larger linked">
		<a href="http://foo.bar">
			<img src="http://foo.bar/.../dscf1000_foo.jpg" alt="A picture of my cat">
		</a>
		<figcaption>
			<p>
				<span class="photo-title">My Cat</span>
				<span class="photo-alt">A picture of my cat</span>
				<span class="photo-exif">FUJIFILM X-E1, 55mm, F/1.4, 5s, ISO 200</span>
			</p>
		</figcaption>
	</figure>