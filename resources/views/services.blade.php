@extends('layouts.langind')
<!--En el primer section, el primer elemento es el elemento de layouts y el segundo es su valor. El segundo sectoin solo es el elemento ya que el contenido se indica dentro del section-->
@section('title','services')
@section('content')
    <h1>servicios de la aplicacion</h1>
    @component('_components.card')
        @slot('title','Service 1')
        @slot('content','Lorem ipsum...')
    @endcomponent
    @component('_components.card')
    @slot('title','Service 2')
    @slot('content','Lorem ipsum...')
@endcomponent
@endsection