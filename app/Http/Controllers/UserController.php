<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use App\Models\Group;
use App\Models\Member;
use App\Models\Group_Chat;
use App\Events\MessageEvent;
use Illuminate\Http\Request;
use App\Events\UserStatusEvent;
use App\Events\GroupMessageEvent;
use App\Events\TypingStatusEvent;
use App\Events\DeleteMessageEvent;
use App\Events\UpdateMessageEvent;
use Illuminate\Support\Facades\Auth;
use App\Events\DeleteGroupMessageEvent;
use App\Events\GroupTypingMessageEvent;
use App\Events\UpdateGroupMessageEvent;

class UserController extends Controller
{
    //Show users
    public function loadDashboard(){
        $users = User::whereNotIn('id',[Auth::user()->id])->get();

        //event
        // event(new UserStatusEvent);

        return view('dashboard',compact('users'));
    }

    //get ajax data
    public function getData(){

            $users = User::get();

            event(new UserStatusEvent($users));

            return response()->json(['data'=>$users]);;

    }

    //typing status message
    public function typingStatus(Request $req){
        $data = User::get();
        event(new TypingStatusEvent($data));
        return response()->json(['success' => true, 'userData' => $data]);
    }

    //saveChat
    public function saveChat(Request $req){
        try {
            $chat = Chat::create([
                'sender_id' => $req->sender_id,
                'receiver_id' => $req->receiver_id,
                'message' => $req->message
            ]);
            $chat = Chat::with('userData')->where('id',$chat->id)->first();
            event(new MessageEvent($chat));

            return response()->json(['success'=>true,'data' => $chat]);
        } catch (\Exception $e) {
            return response()->json(['success'=>false,'msg' => $e->getMessage()]);
        }
    }

    //loadChat
     public function loadChat(Request $req){
        $chats = Chat::with('userData')->where(function($query) use($req){
            $query->where('sender_id', '=', $req->sender_id)
                  ->orWhere('sender_id', '=', $req->receiver_id);
        })->where(function($query) use($req){
            $query->where('receiver_id', '=', $req->sender_id)
                  ->orWhere('receiver_id', '=', $req->receiver_id);
        })->get();

        return response()->json(['success'=>true,'data' => $chats]);
    }

    //deleteMessage
    public function deleteMessage(Request $req){
        Chat::where('id',$req->id)->delete();
        event(new DeleteMessageEvent($req->id));
        return response()->json(['success'=>true,'Message' => "Successfully Deleted!"]);
    }

    //Update message
    public function updateMessage(Request $req){
        Chat::where('id',$req->id)->update(['message' => $req->message]);

        $chat = Chat::where('id',$req->id)->first();

        event(new UpdateMessageEvent($chat));

        return response()->json(['success'=>true,'Message' => "Successfully Updated!"]);
    }


    //Group Chat

    public function myGroup(){
        $groups = Group::where('creator_id',Auth::user()->id)->get();
        return view('group',compact('groups'));
    }

    //create group
    public function createGroup(Request $req){

        try{
            $imageName = '';
            if($req->image){
                $imageName = time().'.'.$req->image->extension();
                $req->image->move(public_path('image'),$imageName);
                $imageName = 'image/'.$imageName;
            }
            Group:: insert([
                'creator_id' => Auth::user()->id,
                'name' => $req->name,
                'image' => $imageName,
                'join_limit' => $req->memberLimit
            ]);

            return response()->json(['success' => true,'msg' => $req->name.'  created a group']);
        } catch (\Exception $e) {
            return response()->json(['success'=>false,'msg' => $e->getMessage()]);
        }
    }

    //show add members
    public function getMembers(Request $req){

        $users = User::with(['groupUser' => function($query) use($req){
            $query->where('group_id',$req->group_id);
        }])
        ->whereNotIn('id',[Auth::user()->id])->get();

        return response()->json(['success' => true,'data' => $users]);
    }

    //add Membets
    public function addMembers(Request $req){

        try{
            if(!isset($req->members)){
                return response()->json(['success'=>false,'msg' => 'Please select members!']);
            }else if(count($req->members) > (int) $req->limit){
                return response()->json(['success'=>false,'msg' => "Please don't select more than ".$req->limit.' members!']);
            }else{

                Member::where('group_id',$req->group_id)->delete();

                $data = [];
                $x = 0;
                foreach($req->members as $user){
                    $data[$x] = ['group_id' => $req->group_id ,'user_id' => $user];
                    $x++;
                }
                logger($data);
                Member::insert($data);


                return response()->json(['success' => true,'msg' => "Added members successsfully!"]);
            }
        } catch (\Exception $e) {
            return response()->json(['success'=>false,'msg' => $e->getMessage()]);
        }
    }

     //delete group chat
     public function deleteGroup(Request $req){

        try{
            $id = Group::where('id',$req->id)->delete();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success'=>false,'msg' => $e->getMessage()]);
        }
    }

    //update Group chat
    public function updateGroup(Request $req){

        try{
            $data = [];
            $imageName = '';
            if($req->image){
                $imageName = time().'.'.$req->image->extension();
                $req->image->move(public_path('image'),$imageName);
                $imageName = 'image/'.$imageName;

                $data = [
                    'creator_id' => Auth::user()->id,
                    'name' => $req->name,
                    'join_limit' => $req->memberLimit,
                    'image' =>$imageName
                ];

            }else{
                $data = [
                    'creator_id' => Auth::user()->id,
                    'name' => $req->name,
                    'join_limit' => $req->memberLimit
                ];
            }

            $groupData = Group::where('id',$req->id)->first();
            if($req->memberLimit < $groupData->join_limit){
                Member::where('group_id',$req->id)->delete();
            }

            Group::where('id',$req->id)->update($data);

            return response()->json(['success' => true,'msg' => 'Successfully updated!']);

        } catch (\Exception $e) {
            return response()->json(['success'=>false,'msg' => $e->getMessage()]);
        }
    }

    //apply share group link
    public function shareGroup($id){
        $groupData = Group::where('id',$id)->first();

        if($groupData){
            $totalMembers = Member::where('group_id',$id)->count();

            $available = $groupData->join_limit - $totalMembers;

            $isOwner = $groupData->creator_id == Auth::user()->id? true: false;

            $isJoined = Member::where(['group_id' => $id, 'user_id' => Auth::user()->id])->count();

            return view('shareGroup',compact('groupData','totalMembers','available','isOwner','isJoined'));
        }else{
            return view('404');
        }
    }

    //join group
    public function joinGroup(Request $req){
        Member::insert([
            'group_id' => $req->group_id,
            'user_id' =>Auth::user()->id
        ]);
        return response()->json(['success' => true,'msg' => 'You have joined successfully!']);
    }

    //get group data
    public function getGroupData(){
        try{
            $data  = Group::with('getGroupData')->get();
            return response()->json(['success' => true,'data' => $data]);
        }catch (\Exception $e) {
            return response()->json(['success'=>false,'msg' => $e->getMessage()]);
        }
    }

    //group chat
    public function groupChats(){
        $groups = Group::where('creator_id',Auth::user()->id)->get();
        $joinedGroups = Member::select('members.*','groups.*')
                        ->leftJoin('groups','groups.id','members.group_id')
                        ->where('user_id',Auth::user()->id)->get();
        // dd($joinedGroups->toArray());
        return view('groupChat',compact('groups','joinedGroups'));
    }

    //group chat messaging
    public function saveGroupChat(Request $req){
        try {
            $chat = Group_Chat::create([
                'sender_id' => $req->sender_id,
                'group_id' => $req->group_id,
                'message' => $req->message
            ]);

            $chat = Group_Chat::with('getUserData')->where('id',$chat->id)->first();
            // logger($chat->toArray());
            event(new GroupMessageEvent($chat));

            return response()->json(['success'=>true,'data' => $chat]);
        } catch (\Exception $e) {
            return response()->json(['success'=>false,'msg' => $e->getMessage()]);
        }
    }

    //group typing status
    public function groupTypingStatus(Request $req){
        $data = User::get();
        event(new GroupTypingMessageEvent($data));
        return response()->json(['success' => true, 'groupUserData' => $data]);
    }

    //load group chat
    public function loadGroupChat(Request $req){
        try {
           $chats = Group_Chat::with('getUserData')->where('group_id',$req->group_id)->get();

           return response()->json(['success'=>true,'chats' => $chats]);
        } catch (\Exception $e) {
            return response()->json(['success'=>false,'msg' => $e->getMessage()]);
        }
    }

    //delete group message
    public function deleteGroupChat(Request $req){
        try {
           $chat = Group_Chat::where('id',$req->id)->delete();

           event(new DeleteGroupMessageEvent($req->id));

           return response()->json(['success'=>true,'chats' => 'Message is deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success'=>false,'msg' => $e->getMessage()]);
        }
    }

    //Update group message
    public function updateGroupMessage(Request $req){
        try{
            Group_Chat::where('id',$req->id)->update(['message' => $req->message]);

            $chat = Group_Chat::where('id',$req->id)->first();

            event(new UpdateGroupMessageEvent($chat));

            return response()->json(['success'=>true,'Message' => "Successfully Updated!"]);
        } catch (\Exception $e) {
            return response()->json(['success'=>false,'msg' => $e->getMessage()]);
        }

    }



}
