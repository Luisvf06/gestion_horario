@extends('layouts.langind')

@section('content')
<a href="{{route('ausencia.index')}}">Volver</a>
<h1>{{$ausencia->descripcion}}</h1>
@endsection