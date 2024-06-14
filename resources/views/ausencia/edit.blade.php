@extends('layouts.langind')

@section('content')
<a href="{{route('ausencia.index')}}">Volver</a>
<form method="POST" action="{{route('ausencia.update',$ausencia->id)}}">
    @method('PUT')
    @csrf

    <label>Descripcion:</label>
    <input type="text"name="descripcion" value="{{ausencia->descripcion}}" />



    
    <input type="submit" value="Update"/>
</form>
@endsection