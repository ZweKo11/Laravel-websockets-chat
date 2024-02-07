<x-app-layout>
    <div class="container">
        <h1 class="mt-5 mb-3">Group Chat</h1>

        <!-- Button trigger modal -->
        <button type="button" class="btn bg-primary text-white" data-bs-toggle="modal" data-bs-target="#groupChatForm">
            Create Now!
        </button>

        {{-- groups table --}}
        <div class="card mt-4">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        @if (count($groups) != 0)
                            <table class="table text-center align-items-center">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Image</th>
                                        <th>Group Name</th>
                                        <th>Members Limit</th>
                                        <th>Total Members</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 0;
                                    @endphp
                                    @foreach ($groups as $group)
                                        <tr style="height: 120px;">
                                            <td>{{ ++$i }}.</td>
                                            <td><img src="{{ $group->image }}" alt="{{ $group->name }}"
                                                    class="img-thumbnail shadow-sm" width="100px" height="100px"></td>
                                            <td>{{ $group->name }}</td>
                                            <td>{{ $group->join_limit }}</td>
                                            <td>
                                                <a style="cursor: pointer" class="addMember"
                                                    data-limit="{{ $group->join_limit }}" data-id="{{ $group->id }}"
                                                    data-bs-toggle="modal" data-bs-target="#addMemberModal">Members</a>
                                            </td>
                                            <td class="col-3">
                                                <i class="fa fa-trash deleteGroup" aria-hidden="true"
                                                    data-id="{{ $group->id }}" data-name="{{ $group->name }}"
                                                    data-bs-toggle="modal" data-bs-target="#deleteGroupModal"></i>
                                                <i class="fa fa-edit ms-2 updateGroup" aria-hidden="true"
                                                    data-id="{{ $group->id }}" data-name="{{ $group->name }}"
                                                    data-limit="{{ $group->join_limit }}" data-bs-toggle="modal"
                                                    data-bs-target="#updateGroupModal"></i>
                                                <a class="copy" data-id="{{$group->id}}">
                                                    <i class="fa-solid fa-copy ms-2"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <h1 class="text-center text-secondary fs-1 my-5 ">No Groups Yet!</h1>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!--Create Group Modal -->
        <div class="modal fade" id="groupChatForm" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title fs-5 text-center" id="exampleModalLabel">Create Your Group</h3>
                    </div>
                    <form action="" id="createGroupForm" enctype="multipart/form-data">

                        <div class="modal-body">
                            <div class="my-3">
                                <label class="form-label">Group Name</label>
                                <input type="text" name="name" id="" class="form-control"
                                    placeholder="Enter your group name" required>
                            </div>

                            <div class="my-3">
                                <label class="form-label">Group Image</label>
                                <input type="file" name="image" class="form-control" id="" required>
                            </div>

                            <div class="my-3">
                                <label class="form-label">Members</label>
                                <input type="number" name="memberLimit" class="form-control" min="1"
                                    id="" placeholder="Assign your members limit" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-secondary text-white"
                                data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn bg-primary text-white">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Add membets modal --}}
        <div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title fs-5 text-center" id="exampleModalLabel">Add Group Members</h3>
                    </div>
                    <form action="" id="addMemberForm">
                        <div class="modal-body">

                            <input type="hidden" name="group_id" id="add-group-id">
                            <input type="hidden" name="limit" id="add-limit">

                            <table class="table text-center">
                                <thead>
                                    <tr>
                                        <td>Select</td>
                                        <td>Name</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="2">
                                            <div class="addMemberTable">
                                                <table class="table addMembersToTable">

                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                        <div class="modal-footer">
                            <div class="d-flex">
                                <div>
                                    <span id="add-member-error" class="text-center"></span>
                                </div>
                                <div class="ms-1">
                                    <button type="button" class="btn bg-secondary text-white"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn bg-primary text-white">Add</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Delete Group chat Modal --}}
        <div class="modal fade" id="deleteGroupModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title fs-5" id="exampleModalLabel">Delete Chat Group</h3>
                    </div>
                    <form action="" id="deleteGroupForm">
                        <div class="modal-body">

                            <input type="hidden" name="id" id="delete-group-id">
                            <p class="text-center">Do you want to delete <b id="group_name"></b>?</p>

                        </div>
                        <div class="modal-footer">
                            <div class="d-flex">
                                <div>
                                    <span id="add-member-error" class="text-center"></span>
                                </div>
                                <div class="ms-1">
                                    <button type="button" class="btn bg-secondary text-white"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn bg-danger text-white">Delete</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Update Group chat modal --}}
        <div class="modal fade" id="updateGroupModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-5 text-center" id="exampleModalLabel">Update Your Group</h3>
                </div>
                <form action="" id="updateGroupForm" enctype="multipart/form-data">

                    <div class="modal-body">
                        <input type="hidden" name="id" id="update-group-id">
                        <div class="my-3">
                            <label class="form-label">Group Name</label>
                            <input type="text" name="name" id="update-group-name" class="form-control"
                                placeholder="Change your group name" required>
                        </div>

                        <div class="my-3">
                            <label class="form-label">Group Image</label>
                            <input type="file" name="image" class="form-control" id="">
                        </div>

                        <div class="my-3">
                            <label class="form-label">Members</label>
                            <input type="number" name="memberLimit" class="form-control" min="1"
                                id="update-group-limit" placeholder="Change your members limit" required>
                        </div>
                        <span class="text-danger"><p>Note!</p><small>If you assign your members' limit less than the previous one, we will remove all members from this group!</small></span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-secondary text-white"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-success text-white">Update </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
</x-app-layout>
