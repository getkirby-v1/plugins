# Contactform Plugin & Snippets (Alpha)

This plugin helps you render and submit a simple contact form with name, email and text

## Installation
Put the `contactform` folder in your `site/plugins` folder. If the `site/plugins` folder doesn't exist yet, create it.

### Installing snippets
Copy the snippets from `snippets/contactform` in this repo and add them to `site/snippets`, don't create a subfolder in `site/snippets`.

### Add the contact form

To add the contact form to your site, add the contactform snippet to your templates: 

```

<?php snippet('contactform') ?>

```  

### Contact form settings

Open `site/snippets/contactform.php` to set your email address and other options for the contact form:

```

$form = new contactform(array(
  'to'       => 'John <john@yourdomain.com>',
  'from'     => 'Contact Form <contact@yourdomain.com>',
  'subject'  => 'New contact form request',
  'goto'     => $page->url() . '/status:thank-you'
));

```

You can also customize the HTML for the contact form however you need. 

### Changing the email template

Open `site/snippets/contactform.mail.php` to customize the email template. The contact form plugin will only send plain text emails. You can use the following placeholders in your template: 

```

{name}   
{email}   
{text}   

```

## Requirements 

Since the plugin works with PHP closures, you need to run your site on PHP 5.3 + Older versions are not supported










