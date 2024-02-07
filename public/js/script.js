$.ajaxSetup({
    headers:{
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function(){
    $('.user-list').click(function(){

        let getUserId = $(this).attr('data-id');
        receiver_id = getUserId;

        $('#header').html('');
        $('#chat-container').html('');
        // $('#typing').html('');
        $('.title').hide();

        $.ajax({
            url:'http://127.0.0.1:8000/myData',
            type: 'get',
            success: function(res){
                let defaultLink = 'http://127.0.0.1:8000/image/default_user.jpg';
                    for(let i = 0; i < res.data.length; i++){
                        if(res.data[i].id == receiver_id){

                            let html = `
                            <div class="nav shadow-sm" >
                                <div class="d-flex">
                                    <img src="${res.data[i].image != ''? res.data[i].image : defaultLink}" class="userImg">
                                    <div>
                                        <p class="ms-3 mt-1"> ${res.data[i].name}</p>
                                        <div class="ms-3 mb-1">
                                            <small id="${res.data[i].id}-status" class="myStatus offline-status">Offline</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            `;
                            $('#header').append(html);

                            if($('#'+res.data[i].id+'-status').html() != $('.myStatus').html()){
                                $('.myStatus').removeClass("offline-status");
                                $('.myStatus').addClass("online-status");
                                $('.myStatus').text("Online");
                            }
                        }
                    }
            }
        });
        $('.chat-section').show();
        loadingOldChat();
    });

    //save chat
    $('#chat-form').submit(function(e){
        e.preventDefault();

        let message = $('#message').val();

        $.ajax({
            url:'/saveChat',
            type: 'POST',
            data: {
                sender_id : sender_id,
                receiver_id : receiver_id,
                message: message,
            },
            success: function(res){
                if(res.success){
                    let date = res.data.created_at;
                    let myDate = new Date(date);
                    let formattedDate = myDate.getFullYear() + "-" +
                                        ("0" + (myDate.getMonth() + 1)).slice(-2) + "-" +
                                        ("0" + myDate.getDate()).slice(-2) + "  "+ ("0" + myDate.getHours()).slice(-2)+ ":" +
                                        ("0" + myDate.getMinutes()).slice(-2);
                        $('#message').val();
                        let chat = res.data.message;

                        let html=`<div class="mt-3" id="`+res.data.id+`-chat">`;
                        if(res.data.sender_id == sender_id){
                            html+=`
                            <div class="message sender-message">
                                 <b><small>You</small></b>
                            </div>
                            `
                         }
                        html += `
                        <div class="message sender-message" >
                            <p data-id="`+res.data.id+`"><span>`+chat+`</span>
                              <i class="fa-solid fa-trash ms-4" data-id="`+res.data.id+`" data-bs-toggle="modal" data-bs-target="#myModal"></i>
                              <i class="fa-solid fa-edit ms-1" data-id="`+res.data.id+`" data-msg="`+res.data.message+`" data-bs-toggle="modal" data-bs-target="#myUpdateModal"></i>
                            </p>
                        </div>
                        <div class="message sender-message text-muted"><small>${formattedDate}</small></div>
                        `;
                        `</div>`;

                        $('#chat-container').append(html);
                        scrollMessage();
                }else{
                    alert(res.msg);
                }
            }
        })
        $('#message').val("");
    });

    //typing message
    $('#message').on('keypress',function(){
        $('#typing').html('');
        $.ajax({
            url : "typing/status",
            type : 'post',
            data : {receiver_id : receiver_id},
            success : function(res){
                for(let i = 0; i < res.userData.length; i++){
                    if(res.userData[i].id == sender_id){

                        $('#typing').html('');

                    }
                }
            }
        });
    })

    //delete chat message
    $(document).on('click','.fa-trash',function(){
        let id = $(this).attr('data-id');
        $('#delete-msg-id').val(id);
        let addText = $(this).parent().text();
        $('#delete-message').text(addText);
    });

    $('#delete-msg-form').submit((e)=>{
        e.preventDefault();

        let id = $('#delete-msg-id').val();

        $.ajax({
            url : "/deleteMessage",
            type: "POST",
            data:{id:id},
            success: function(res){
                if(res.success){
                    $('#'+id+'-chat').remove();
                    $('#myModal').modal('hide');

                }
            }
        })
    });

    //update message
    $(document).on('click','.fa-edit',function(){
        $('#update-msg-id').val($(this).attr('data-id'));
        $('#update-message').val($(this).attr('data-msg'));
    })

    $('#update-msg-form').submit((e)=>{
        e.preventDefault();

        let id = $('#update-msg-id').val();
        let msg = $('#update-message').val();

        $.ajax({
            url : '/updateMessage',
            type : 'POST',
            data : {
                id : id,
                message : msg
            },
            success: function(res){
                if(res.success){
                    $('#myUpdateModal').modal('hide');
                    console.log($('#'+id+'-chat').find('span'));
                    // $('#'+id+'-chat').find('span').text(msg);
                    $('#'+id+'-chat').find('.fa-edit').attr('data-msg',msg);
                }
            }
        })
    })
});

//load chat datas
function loadingOldChat(){

    $.ajax({
        url: '/load-chats',
        type: 'POST',
        data: {
            sender_id : sender_id,
            receiver_id : receiver_id
        },
        success: function(res){
            if(res.success){
                let chats = res.data;
                let html = '';
                for(let i = 0; i < chats.length; i++){

                    let date = chats[i].created_at;
                    let myDate = new Date(date);
                    let formattedDate = myDate.getFullYear() + "-" +
                                        ("0" + (myDate.getMonth() + 1)).slice(-2) + "-" +
                                        ("0" + myDate.getDate()).slice(-2) + "  "+ ("0" + myDate.getHours()).slice(-2)+ ":" +
                                        ("0" + myDate.getMinutes()).slice(-2);
                    let addClass = '';
                    if(chats[i].sender_id == sender_id){
                        addClass = 'message sender-message';
                    }else{
                        addClass = 'message receiver-message';
                    }

                    if(chats[i].sender_id == sender_id){
                       html+=`<div id="`+chats[i].id+`-chat">
                       <div class="message `+addClass+`"  >
                            <b><small>You</small></b>
                       </div>
                       `
                    }else{
                        html+=`<div id="`+chats[i].id+`-chat">
                           <div class="message `+addClass+`">
                                <b><small>${chats[i].user_data.name}</small></b>
                           </div>
                           `
                    }
                    html += `
                    <div class="message `+addClass+`" >
                       <p data-id="`+chats[i].id+`-chat"><span>`+chats[i].message+`</span> `;
                       if(chats[i].sender_id == sender_id){
                        html += `
                            <i class="fa-solid fa-trash ms-4" data-id="`+chats[i].id+`" data-bs-toggle="modal" data-bs-target="#myModal"></i>
                            <i class="fa-solid fa-edit ms-1" data-id="`+chats[i].id+`" data-msg="`+chats[i].message+`" data-bs-toggle="modal" data-bs-target="#myUpdateModal"></i>
                        `;
                    }
                    html += `
                       </p>
                       </div>
                       <div class="message `+addClass+` text-muted"><small>${formattedDate}</small></div>
                    </div>
                    `;

                }
                $('#chat-container').append(html);
                scrollMessage();
            }
        }
    })
}

//scroll message
function scrollMessage(){
    $('#chat-container').animate({
        scrollTop : $('#chat-container').offset().top + $('#chat-container')[0].scrollHeight
    },300)
}

//User Status Listen
setTimeout(()=>{
    window.Echo.join('statusUpdate')
    .here((users)=>{
        // console.log(users);
        for(let i = 0; i < users.length; i++){
            if(sender_id != users[i]['id']){
                $('#'+users[i]['id']+'-status').removeClass('offline-status');
                $('#'+users[i]['id']+'-status').addClass('online-status');
                $('#'+users[i]['id']+'-status').text('Online');
            }
        }
    })
    .joining((user)=>{
        $('#'+user.id+'-status').removeClass('offline-status');
        $('#'+user.id+'-status').addClass('online-status');
        $('#'+user.id+'-status').text('Online');
    })
    .leaving((user)=>{
       $('#'+user.id+'-status').removeClass('online-status');
       $('#'+user.id+'-status').addClass('offline-status');
       $('#'+user.id+'-status').text('Offline');
    })
    .listen('.status_update',(e)=>{
        // console.log(e);
    })
},200);

//typing message broadcast
setTimeout(() => {
    window.Echo.private('typing-status')
    .listen('TypingStatusEvent',(data)=>{
        for(let i = 0; i < data.userData.length; i++){
            if(data.userData[i].id != sender_id && data.userData[i].id == receiver_id){
                $('#typing').html(`
                    <p class="py-3 flex-end" ><b style="color:brown;">${data.userData[i].name}</b> is Typing...</p>
                `);
                setTimeout(() => {
                    $('#typing').html('');
                }, 4000);
            }
        }
    })
}, 200);

//messaging broadcast
setTimeout(()=>{
    window.Echo.private('broadcast-message')
    .listen('.getMessageData',(data)=>{
        if(sender_id == data.chat.receiver_id && receiver_id == data.chat.sender_id){

            let date = data.chat.created_at;
            let myDate = new Date(date);
            let formattedDate = myDate.getFullYear() + "-" +
                                ("0" + (myDate.getMonth() + 1)).slice(-2) + "-" +
                                ("0" + myDate.getDate()).slice(-2) + "  "+ ("0" + myDate.getHours()).slice(-2)+ ":" +
                                ("0" + myDate.getMinutes()).slice(-2);

            let html =`<div class=" mt-3" id='`+data.chat.id+`-chat'>`
            if(data.chat.sender_id != sender_id){
                html+=`
                <div class="message receiver-message" >
                     <b><small>${data.chat.user_data.name}</small></b>
                </div>
                `
             }
            html += `
            <div class="message receiver-message" >
                <p>
                    <span>`+data.chat.message+`</span>
                </p>
             </div>
             <div class="message receiver-message text-muted"><small>${formattedDate}</small></div>
            `;
            `</div>`;

            $('#chat-container').append(html);
            scrollMessage();
        }
    })
},200);

// delete Message Listen
setTimeout(()=>{
    window.Echo.private('delete-message')
    .listen('.deletedMessage',(data)=>{
        console.log(data.id);
        $('#'+data.id+'-chat').remove();
    })
},200);

// update Message Listen
setTimeout(()=>{
    window.Echo.private('update-message')
    .listen('.updateMessage',(data)=>{
        // console.log(data.msg);
        $('#'+data.msg.id+'-chat').find('span').text(data.msg.message);
    })
},200)


//Group Chat Part
$(document).ready(function(){
    $('#createGroupForm').submit(function(e){
        e.preventDefault();

        $.ajax({
            url: '/create/group',
            type: 'post',
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function(res){

                if(res.success){
                    location.reload();
                }
            }
        })
    })
});

//adding members
$(document).ready(function(){
    $('.addMember').click(function(){
        let limit = $(this).attr('data-limit');
        let id = $(this).attr('data-id');

        $('#add-group-id').val(id);
        $('#add-limit').val(limit);

        $.ajax({
            url : '/get/members',
            type : "post",
            data : {group_id : id},
            success: function(res){
                if(res.success){
                    let users = res.data;

                    let html = '';

                    for(let i = 0; i < users.length; i++){
                        let isGroupMemberChecked = '';
                        if(users[i]['group_user'] != null){
                            isGroupMemberChecked = 'checked';
                        }
                        html += `
                            <tr>
                                <td>
                                    <input type="checkbox" name="members[]" value="${users[i]['id']}" ${isGroupMemberChecked}/>
                                </td>
                                <td>
                                    ${users[i]['name']}
                                </td>
                            </tr>
                        `;
                    }
                    $('.addMembersToTable').html(html);
                }
            }
        });
    });

    //add members
    $('#addMemberForm').submit(function(e){
        e.preventDefault();

        let formData = $(this).serialize();

        $.ajax({
            url : "/add/members",
            type : "POST",
            data : formData,
            success : function(res){
                if(res.success){
                    $('#addMemberModal').modal('hide');
                    $('#addMemberForm')[0].reset();
                    alert(res.msg);
                }else{
                    $('#add-member-error').text(res.msg);

                    setTimeout(function(){
                        $('#add-member-error').text('');
                    },5000);
                }
            }
        });
    });


    //delete group chat
    $('.deleteGroup').click(function(){
        let id = $(this).attr('data-id');
        let name = $(this).attr('data-name');

        $('#delete-group-id').val(id);
        $('#group_name').text(name);
    });

    $('#deleteGroupForm').submit(function(e){
        e.preventDefault();

        let formData = $(this).serialize();
        $.ajax({
            url : '/delete/group',
            type : 'POST',
            data : formData,
            success : function(res){
                if(res.success){
                    location.reload();
                }
            }
        });
    });

    //update group chat
    $('.updateGroup').click(function(){
        $('#update-group-id').val($(this).attr('data-id'));
        $('#update-group-name').val($(this).attr('data-name'));
        $('#update-group-limit').val($(this).attr('data-limit'));
    });


    $('#updateGroupForm').submit(function(e){
        $.ajax({
            url : "/update/group",
            type : "post",
            data : new FormData(this),
            contentType : false,
            cache : false,
            processData : false,
            success: function(res){
                alert(res.msg);
                if(res.success){
                    location.reload();
                }
            }
        });
    });

    //creating sharable link
    $('.copy').click(function(){
        $(this).append('<sup class="copied-text">Copied</sup>');

        setTimeout(()=>{
            $('.copied-text').remove();
        },3000);

        let groupId = $(this).attr('data-id');
        let url = window.location.host+'/share-group-link/'+groupId;

        let temp = $("<input>");
        $('body').append(temp);
        temp.val(url).select();

        document.execCommand("copy");

        temp.remove();
    });


    //join group
    $('.join-now').click(function(){
        $(this).text('Wait...');
        $(this).attr('disabled','disabled');

        let group_id = $(this).attr('data-id');

        $.ajax({
            url : "/join/group",
            type : "POST",
            data : {group_id:group_id},
            success : function(res){
                alert(res.msg);
                if(res.success){
                    location.reload();
                }else{
                    $(this).text('Join Now');
                    $(this).removeAttr('disabled');
                }
            }
        });
    });

    //group chat
    $('.group-list').click(function(){
        let groupId = $(this).attr('data-id');
        global_group_id = groupId;

        $('#header').html('');
        $('#group-typing').html('');
        $('#group-chat-container').html('');

        $('.group-title').hide();

        console.log(global_group_id);
        $.ajax({
            url : "/get/group/data",
            type: "get",
            success: function(res){
                if(res.success){
                    for(let i=0; i< res.data.length; i++){
                        if(res.data[i].id == global_group_id){
                            let html = `
                            <div class="nav shadow-sm">
                                <div class="d-flex">
                                    <img src="/${res.data[i].image}" class="groupImg">
                                    <div class="my-3 ms-3">
                                        <b><p>${res.data[i].name}</p></b>
                                    </div>
                                </div>
                            </div>
                        `;
                        $('#header').append(html);
                        }
                    }

                }
            }

        });

        $('.group-chat-section').show();

        loadGroupChat();
    });

    //group chat messaging
    $('#group-chat-form').submit(function(e){
        e.preventDefault();

        let message = $('#group-message').val();

        $.ajax({
            url:'/saveGroupChat',
            type: 'POST',
            data: {
                sender_id : sender_id,
                group_id : global_group_id,
                message: message,
            },
            success: function(res){
                if(res.success){
                    let date = res.data.created_at;
                    let myDate = new Date(date);
                    let formattedDate = myDate.getFullYear() + "-" +
                                        ("0" + (myDate.getMonth() + 1)).slice(-2) + "-" +
                                        ("0" + myDate.getDate()).slice(-2) + "  "+ ("0" + myDate.getHours()).slice(-2)+ ":" +
                                        ("0" + myDate.getMinutes()).slice(-2);
                                    console.log(formattedDate);
                        $('#group-message').val();
                        let chat = res.data.message;

                        let html = `<div class="mt-3" id="`+res.data.id+`-chat">`
                        if(res.data.sender_id == sender_id){
                            html +=`
                                <div class="message sender-message">
                                   <b> <small class="text-muted">You</small></b>
                                </div>
                            `
                        }
                        html +=`
                        <div class="message sender-message" id="`+res.data.id+`-chat">
                            <p data-id="`+res.data.id+`"><span>`+chat+`</span>
                              <i class="fa-solid fa-trash ms-4 deleteGroupMessage" data-id="`+res.data.id+`" data-bs-toggle="modal" data-bs-target="#deleteGroupModal"></i>
                              <i class="fa-solid fa-edit updateGroupChat ms-1" data-id="`+res.data.id+`" data-msg="`+res.data.message+`" data-bs-toggle="modal" data-bs-target="#updateGroupChatModal"></i>
                            </p>
                        </div>
                        <div class="message sender-message text-muted"><small>${formattedDate}</small></div>
                        `;
                        `</div>`;

                        $('#group-chat-container').append(html);
                        scrollGrouoMessage();
                }else{
                    alert(res.msg);
                }
            }
        })
        $('#group-message').val("");
    });

    //group typing status
    $('#group-message').on('keypress',function(){
        $.ajax({
            url : "/group/typing/status",
            type : 'post',
            data : {},
            success : function(res){
                if(res.success){
                    for(let i = 0; i < res.groupUserData.length; i++){
                        if(res.groupUserData[i].id == sender_id){
                            $('#group-typing').html('');
                        }
                    }
                }
            }
        });
    });
});



//group typing status broadcast
setTimeout(()=>{
    window.Echo.private('group-typing-message-broadcast')
    .listen('GroupTypingMessageEvent',(data)=>{
        console.log(data);
        for(let i = 0; i < data.groupUserData.length; i++){
           if(data.groupUserData[i].id != sender_id ){
            $('#group-typing').html(`
                    <p class="py-3"><b style="color:aqua;">${data.groupUserData[i].name}</b> <small> is Typing</small></p>
            `);
            setTimeout(()=>{
                $('#group-typing').html('');
            },4000);
           }
        }
    })
},200)

//scroll group message
function scrollGrouoMessage(){
    $('#group-chat-container').animate({
        scrollTop : $('#group-chat-container').offset().top + $('#group-chat-container')[0].scrollHeight
    },300)
};

//broadcasting group message
setTimeout(()=>{
    window.Echo.private('broadcast-group-message')
    .listen('.getGroupChatMessage',(data)=>{

        let date = data.chat.created_at;
        let myDate = new Date(date);
        let formattedDate = myDate.getFullYear() + "-" +
                            ("0" + (myDate.getMonth() + 1)).slice(-2) + "-" +
                            ("0" + myDate.getDate()).slice(-2) + "  "+ ("0" + myDate.getHours()).slice(-2)+ ":" +
                            ("0" + myDate.getMinutes()).slice(-2);

        if(sender_id != data.chat.sender_id && global_group_id == data.chat.group_id){
            let html =`<div class="mt-3" id='`+data.chat.id+`-chat'>`;
            if(data.chat.sender_id != sender_id){
                html +=`
                    <div class="message receiver-message">
                        <b><small class="text-muted">${data.chat.get_user_data.name}</small></b>
                    </div>
                `
            }
            html += `
            <div class="message receiver-message" id='`+data.chat.id+`-chat'>
                <p><span>`+data.chat.message+`</span>
                </p>
             </div>
             <div class="message receiver-message text-muted"><small>${formattedDate}</small></div>
            `;
            `</div>`;

            $('#group-chat-container').append(html);
            scrollGrouoMessage();
        }
    })
},200);

//load group chat
function loadGroupChat(){
    $('#group-chat-container').html('');
    $.ajax({
        url : "/load/group/chat",
        type : "POST",
        data : {group_id: global_group_id},
        success : function (res){
            if(res.success){
                let chats = res.chats;
                let html = '';
                for(let i = 0; i < chats.length; i++){

                    let date = chats[i].created_at;
                    let myDate = new Date(date);
                    let formattedDate = myDate.getFullYear() + "-" +
                                        ("0" + (myDate.getMonth() + 1)).slice(-2) + "-" +
                                        ("0" + myDate.getDate()).slice(-2) + "  "+ ("0" + myDate.getHours()).slice(-2)+ ":" +
                                        ("0" + myDate.getMinutes()).slice(-2);

                    let addClass = 'receiver-message';
                    if(chats[i].sender_id == sender_id){
                        addClass = 'sender-message';
                    }

                    if(chats[i].sender_id == sender_id){
                        html +=`<div id='`+chats[i].id+`-chat'>
                            <div class="message `+addClass+`">
                                <b><small class="text-muted">You</small></b>
                            </div>
                        `
                    }else{
                        html +=`<div id='`+chats[i].id+`-chat'>
                        <div class="message `+addClass+`">
                            <b><small class="text-muted">${chats[i].get_user_data.name}</small></b>
                        </div>
                    `
                    }
                    html += `
                    <div class="message `+addClass+`" id='`+chats[i].id+`-chat'>
                        <p><span>`+chats[i].message+`</span>`;
                        if(chats[i].sender_id == sender_id){
                            html+=`<i class="fa-solid fa-trash deleteGroupMessage ms-4" data-id="`+chats[i].id+`" data-bs-toggle="modal" data-bs-target="#deleteGroupModal"></i>
                                   <i class="fa-solid fa-edit updateGroupChat ms-1" data-id="`+chats[i].id+`" data-msg="`+chats[i].message+`" data-bs-toggle="modal" data-bs-target="#updateGroupChatModal"></i>
                            `
                        }
                        html+=`
                        </p>
                        </div>
                        <div class="message `+addClass+` text-muted"><small>${formattedDate}</small></div>
                     </div>
                    `;

                }
                $('#group-chat-container').append(html);
                scrollGrouoMessage();
            }
        }
    });
};

//delete group message
$(document).ready(function(){
    $(document).on('click','.deleteGroupMessage',function(){
        let msg = $(this).parent().text();
        $('#delete-group-message').text(msg);

        $('#delete-group-msg-id').val($(this).attr('data-id'));
    });

    $('#delete-group-msg-form').submit(function(e){
        e.preventDefault();
        let id = $('#delete-group-msg-id').val();
        $.ajax({
            url : "/delete/group/chat",
            type : "post",
            data : {id : id},
            success : function(res){
                if(res.success){
                    $('#'+id+'-chat').remove();
                    $('#deleteGroupModal').modal('hide');
                }
            }
        });
    })
});

//delete group message listen
setTimeout(()=>{
    window.Echo.private('delete-group-message')
    .listen('.deleteGroupMessage',(data)=>{
        $('#'+data.id+'-chat').remove();
    })
},200);


//update group message
$(document).on('click','.updateGroupChat',function(){
    $('#update-group-msg-id').val($(this).attr('data-id'));
    $('#update-group-message').val($(this).attr('data-msg'));
})

$('#update-group-msg-form').submit((e)=>{
    e.preventDefault();

    let id = $('#update-group-msg-id').val();
    let msg = $('#update-group-message').val();

    $.ajax({
        url : '/update/group/message',
        type : 'POST',
        data : {
            id : id,
            message : msg
        },
        success: function(res){
            if(res.success){
                $('#updateGroupChatModal').modal('hide');
                $('#'+id+'-chat').find('span').text(msg);
                $('#'+id+'-chat').find('.fa-edit').attr('data-msg',msg);
            }
        }
    })
});

// update group Message Listen
setTimeout(()=>{
    window.Echo.private('update-group-message')
    .listen('.updateGroupMessage',(data)=>{
        $('#'+data.data.id+'-chat').find('span').text(data.data.message);
    })
},200)

