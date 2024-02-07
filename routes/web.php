<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['auth', 'verified'])->group(function(){
    Route::get('/dashboard',[UserController::class,'loadDashboard'])->name('dashboard');

    //One by one chat
    Route::get('/myData',[UserController::class,'getData']);

    Route::post('/saveChat',[UserController::class,'saveChat'])->name('user#saveChat');

    Route::post('/typing/status',[UserController::class,'typingStatus']);

    Route::post('/load-chats',[UserController::class,'loadChat']);

    Route::post('/deleteMessage',[UserController::class,'deleteMessage']);

    Route::post('/updateMessage',[UserController::class,'updateMessage']);

    //Group chat
    Route::get('/groups',[UserController::class,'myGroup'])->name('groups');

    Route::post('/create/group',[UserController::class,'createGroup']);

    Route::post('/get/members',[UserController::class,'getMembers']);

    Route::post('/add/members',[UserController::class,'addMembers']);

    Route::post('/delete/group',[UserController::class,'deleteGroup']);

    Route::post('/update/group',[UserController::class,'updateGroup']);

    Route::get('/share-group-link/{id}',[UserController::class,'shareGroup']);

    Route::post('/join/group',[UserController::class,'joinGroup']);

    Route::get('/get/group/data',[UserController::class,'getGroupData']);

    Route::get('/group/chat',[UserController::class,'groupChats'])->name('groupChats');

    Route::post('/group/typing/status',[UserController::class,'groupTypingStatus']);

    Route::post('/saveGroupChat',[UserController::class,'saveGroupChat']);

    Route::post('/load/group/chat',[UserController::class,'loadGroupChat']);

    Route::post('/delete/group/chat',[UserController::class,'deleteGroupChat']);

    Route::post('/update/group/message',[UserController::class,'updateGroupMessage']);
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
