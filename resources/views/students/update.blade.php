<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Update Student') }}
        </h2>
    </x-slot>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="md-10 center">
                    <br>
                    <form class="form-inline" action="{{url('admin/students/'.$student->id)}}" method="post">

                        <input type="text" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2" name="name"
                               value="{{$student->name}}"
                               placeholder="School Name">

                        <select  name="school_id" class="form-select" aria-label="Default select example">
                            <option value="0" selected>Select School</option>
                            @if(!empty($schools))
                                @foreach($schools as $school)
                                    <option value="{{$school->id}}" {{$student->school_id == $school->id ? 'selected':''}}>{{$school->name}}</option>
                                @endforeach
                            @endif
                        </select>
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-primary mb-2">save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



</x-app-layout>
