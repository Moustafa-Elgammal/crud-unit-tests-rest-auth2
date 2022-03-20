<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Update School') }}
        </h2>
    </x-slot>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="md-10 center">
                    <br>
                    <form class="form-inline" action="{{url('admin/schools/'.$school->id)}}" method="post">

                        <input type="text" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2" name="name"
                               value="{{$school->name}}"
                               placeholder="School Name">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-primary mb-2">save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



</x-app-layout>
