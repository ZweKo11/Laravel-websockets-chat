<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('statusUpdate',function($user){
    return $user;
});

Broadcast::channel('broadcast-message',function($user){
    return $user;
});

Broadcast::channel('delete-message',function($user){
    return $user;
});

Broadcast::channel('update-message',function($user){
    return $user;
});

Broadcast::channel('broadcast-group-message',function($user){
    return $user;
});

Broadcast::channel('delete-group-message',function($user){
    return $user;
});

Broadcast::channel('update-group-message',function($user){
    return $user;
});

Broadcast::channel('typing-status',function($user){
    return $user;
});

Broadcast::channel('group-typing-message-broadcast',function($user){
    return $user;
});





