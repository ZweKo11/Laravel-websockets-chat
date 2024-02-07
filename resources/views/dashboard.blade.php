<x-app-layout>
    <div class="container">
        <div class="row mt-5">
            <div class="col-sm-4 mb-5">
                @if (count($users) != 0)
                    <ul class="list-group">
                        @foreach ($users as $user)
                            <li class="list-group-item shadow-sm py-1 user-list" data-id="{{$user->id}}">
                                <div class="d-flex">
                                    @if ($user->image != null)
                                        <img src="{{ $user->image }}" class="userImg">
                                    @else
                                        <img src="{{ asset('image/default_user.jpg') }}" class="userImg">
                                    @endif
                                    <div>
                                        <p class="ms-3 mt-1"> {{ $user->name }}</p>
                                        <div class="ms-3 mb-1">
                                            <small id="{{$user->id}}-status" class="offline-status">Offline</small>
                                        </div>
                                    </div>
                                 </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="col-md-7">
                <h3 class="title">Your Chat</h3>
                <div class="chat-section shadow-sm">
                    <div class=".row d-flex justify-content-end">
                        <div id="header" class="col-6">

                        </div>
                        <div id="typing" class="typing-status col-6">

                        </div>
                    </div>
                    <div id="chat-container">


                    </div>

                    <form action="" id="chat-form">
                        <input type="text" name="message" id="message" placeholder="Enter a message" class="border">
                        <button type="submit" class="btn bg-primary text-white">Send</button>
                    </form>
                </div>

              <!--Delete Modal -->
              <div class="modal modal-sm fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered ">
                  <div class="modal-content ">
                    <div class="modal-header">
                      <h3 class="modal-title fs-5 text-center" id="exampleModalLabel">Are you sure to delete this message?</h3>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" id="delete-msg-form">
                        <div class="modal-body">
                            <input type="hidden" name="id" id="delete-msg-id">
                            {{-- <h5 class="text-center">Are you sure to delete this message?</h5> --}}
                            <h4 class="text-center mt-2"><b id="delete-message"></b></h4>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-outline bg-secondary" data-bs-dismiss="modal">Close</button>
                          <button type="submit" class="btn btn-outline bg-danger">Delete</button>
                        </div>
                    </form>
                  </div>
                </div>
              </div>

              {{-- Update Modal --}}
              <div class="modal modal-sm fade" id="myUpdateModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered ">
                  <div class="modal-content ">
                    <div class="modal-header">
                      <h3 class="modal-title fs-5 text-center" id="exampleModalLabel">Do you want to edit your message?</h3>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" id="update-msg-form">
                        <div class="modal-body">
                            <input type="hidden" name="id" id="update-msg-id">
                            <input type="text" name="message" class="form-control" placeholder="Edit your message" required id="update-message">
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
    </div>
</x-app-layout>

