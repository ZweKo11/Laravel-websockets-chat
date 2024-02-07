<x-app-layout>
    <div class="container">
        <div class="row mt-5">
            <div class="col-sm-4 mb-5">

                @if (count($groups) != 0 || count($joinedGroups) != 0)
                    <ul class="list-group">
                        @foreach ($groups as $group)
                        <input type="hidden" name="id">
                            <li class="list-group-item shadow-sm py-1 group-list" data-id="{{$group->id}}">
                                <div class="d-flex py-1">
                                    <img src="/{{$group->image}}" alt="" class="groupImg">
                                    <div>
                                        <p class="ms-3 mt-3" style="user-select: none"><b>{{ $group->name }}</b> </p>
                                    </div>
                                 </div>
                            </li>
                        @endforeach

                        <!-- joined groups -->
                        @foreach ($joinedGroups as $joinedGroup)
                        <li class="list-group-item shadow-sm py-1 group-list" data-id="{{$joinedGroup->id}}">
                            <div class="d-flex py-1">
                                <img src="/{{$joinedGroup->image}}" alt="" class="groupImg">
                                <div>
                                    <p class="ms-3 mt-3" style="user-select: none"><b>{{ $joinedGroup->name }}</b> </p>
                                </div>
                             </div>
                        </li>
                    @endforeach
                    </ul>
                @else
                    <h1 class="text-center">Groups Not Found!</h1>
                @endif
            </div>
            <div class="col-md-7">
                <h3 class="group-title">Your Chat</h3>
                <div class="group-chat-section shadow-sm">
                    <div class=".row d-flex ">
                        <div id="header" class="col-6">

                        </div>
                        <div id="group-typing" class="typing-status col-6">

                        </div>
                    </div>
                    <div id="group-chat-container">

                    </div>
                    <form action="" id="group-chat-form">
                        <input type="text" name="message" id="group-message" placeholder="Enter a message" class="border">
                        <button type="submit" class="btn bg-primary text-white">Send</button>
                    </form>
                </div>

        </div>

        {{-- Delete group chat modal --}}
        <div class="modal modal-sm fade" id="deleteGroupModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered ">
              <div class="modal-content ">
                <div class="modal-header">
                  <h3 class="modal-title fs-5 text-center" id="exampleModalLabel">Are you sure to delete this message?</h3>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" id="delete-group-msg-form">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="delete-group-msg-id">
                        {{-- <h5 class="text-center">Are you sure to delete this message?</h5> --}}
                        <h4 class="text-center mt-2"><b id="delete-group-message"></b></h4>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-outline bg-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-outline bg-danger">Delete</button>
                    </div>
                </form>
              </div>
            </div>
        </div>

        {{-- Update Group chat modal --}}
          {{-- Update Modal --}}
          <div class="modal modal-sm fade" id="updateGroupChatModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered ">
              <div class="modal-content ">
                <div class="modal-header">
                  <h3 class="modal-title fs-5 text-center" id="exampleModalLabel">Do you want to edit your message?</h3>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" id="update-group-msg-form">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="update-group-msg-id">
                        <input type="text" name="message" class="form-control" placeholder="Edit your message" required id="update-group-message">
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-outline bg-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-outline bg-success">Update</button>
                    </div>
                </form>
              </div>
            </div>
          </div>
    </div>
</x-app-layout>

