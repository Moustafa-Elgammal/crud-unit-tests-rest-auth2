<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Students') }}
        </h2>
    </x-slot>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <br>
                    <div class="md-10 center">
                        <br>
                        <form class="form-inline" action="{{url('admin/students')}}" method="post">

                            <input type="text" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2" name="name"
                                   placeholder="Students Name" required>

                            <select  name="school_id" class="form-select" aria-label="Default select example">
                                <option value="0" selected>Select School</option>
                                @if(!empty($schools))
                                    @foreach($schools as $school)
                                        <option value="{{$school->id}}">{{$school->name}}</option>
                                    @endforeach
                                @endif
                            </select>

                            @csrf
                            <button type="submit" class="btn btn-primary mb-2">save</button>
                        </form>



                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                <tr>

                                    <th>Student Name</th>
                                    <th>School Name</th>
                                    <th>Student Order</th>
                                    <th>control</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($students as $student)
                                    <tr>
                                        <td>{{$student->name}}</td>
                                        <td>{{$student->school->name ?? ''}}</td>
                                        <td>{{$student->order}}</td>
                                        <td>
                                            <a class="btn btn-sm btn-outline-info" href="{{url("admin/students/$student->id/edit")}}">update</a>

                                            <form method="post" action="{{url("admin/students/$student->id")}}">
                                                @csrf
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
            </div>
        </div>
    </div>



</x-app-layout>

