<?php

/*
|--------------------------------------------------------------------------
| Routes, one file per domain module
|--------------------------------------------------------------------------
| Each module file holds its public routes and, inside the admin group
| (auth + can:access-admin, prefix /admin, names admin.*), its CMS routes.
| Every admin controller also authorizes per action through its policy.
*/

require __DIR__.'/site.php';
require __DIR__.'/profile.php';
require __DIR__.'/projects.php';
require __DIR__.'/blog.php';
require __DIR__.'/services.php';
require __DIR__.'/uses.php';
require __DIR__.'/contact.php';
require __DIR__.'/assistant.php';
require __DIR__.'/seo.php';
require __DIR__.'/dashboard.php';
require __DIR__.'/tags.php';
require __DIR__.'/board.php';
require __DIR__.'/experience.php';
require __DIR__.'/testimonials.php';
require __DIR__.'/work-approach.php';
require __DIR__.'/account.php';
require __DIR__.'/media.php';
