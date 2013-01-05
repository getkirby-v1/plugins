GitHub Plugin for Kirby
=======================

With that plugin you can implement a users GitHub information or list your repos easily in Kirby.

Usage example for repos:
========================
```php
<?php
$github = new github('abahlo', 10, true);

foreach($github->repos() as $repo):
?>
    <li>
        <h1><a href="<?php echo $repo->url ?>"><?php echo $repo->name ?></a></h1>
        <p><?php echo $repo->description ?></p>
    </li>
<?php endforeach ?>
```

Attributes for repo()
=====================
- `$repo->name` the name of the repository
- `$repo->description` the description
- `$repo->url` the url to the repository
- `$repo->last_update` the timestamp to the last update
- `$repo->forkcount` the number of forks
- `$repo->watchers` the number of watchers

Attributes for user()
=====================
- `$user->username` the GitHub username
- `$user->name` the real name
- `$user->email` the email, if given
- `$user->followers` the count of followers
- `$user->following` the count of following
- `$user->url` the GitHub-url
- `$user->gravatar_id` the gravatar-id
- `$user->repos_url` the url to the repos
