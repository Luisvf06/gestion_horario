@extends('layouts.langind')

@section('content')
<!--
    @if($errors->any())
     <ul>
        @foreach($errors->all() as $error)
        <li>{{$error}}</li>
        @endforeach
     </ul>
     @endif
-->
<a href="{{route('ausencia.index')}}">Volver</a>
<form method="POST" action="{{route('ausencia.create')}}">
    @csrf

    <label>Descripcion:</label>
    <input type="text"name="descripcion" /><br>
    @error('title')
        <p>{{$message}}</p>
    
    <input type="submit" value="Create"/>
</form>
@endsection