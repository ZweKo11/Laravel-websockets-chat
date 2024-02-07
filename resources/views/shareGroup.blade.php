<x-app-layout>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-4">
                        <img src="/{{$groupData->image}}" class="img-thumbnail shadow-sm" width="200px" height="180px" alt="">
                    </div>
                    <div class="col-6">
                        <p class="my-3"><b class="text-uppercase">{{$groupData->name}}</b></p>
                        <p class="my-3">Total Members : <b>{{$totalMembers}}</b></p>

                        @if ($available != 0)
                            <p class="my-3">Available for (<b style="color:rgb(7, 244, 7)">{{$available}}</b>) member(s) only!</p>
                        @endif

                        @if ($isOwner)
                            <p class="my-3">You are an owner of this group. <br> You cannot join.</p>
                        @elseif ($isJoined > 0)
                            <p class="my-3" style="color:rgb(234, 156, 13);"><em>You've already joined this group!</em></p>
                        @elseif ($available == 0)
                            <p class="my-3" style="color:red;">Full Members!!!<br>You cannot join this group anymore! </p>
                        @else
                            <button class="btn bg-info text-white my-3 join-now" data-id="{{$groupData->id}}">Join Now</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
