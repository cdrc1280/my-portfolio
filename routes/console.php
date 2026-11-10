<?php

use Illuminate\Support\Facades\Schedule;

// Keep the home page's GitHub section warm so visitors never wait on the API.
Schedule::command('github:refresh')->hourly()->withoutOverlapping();
