@extends('layout')
@section('content')
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
@endsection
