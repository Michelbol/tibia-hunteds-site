<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('online-characters.{guildName}', function () {
    return true;
});
