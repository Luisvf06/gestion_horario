@extends('../layouts.langind')
<!--En el primer section, el primer elemento es el elemento de layouts y el segundo es su valor. El segundo sectoin solo es el elemento ya que el contenido se indica dentro del section-->
@section('title','index')
@section('content')
<h1>Contenido de la aplicacion</h1>
@if($users->isEmpty())
    <p>No hay datos en usuarios</p>
@else
    <ul>
        @foreach ($users as $user)
            <li>{{ $user->name }}</li>
        @endforeach

    </ul>

@endif  
<!--Otra forma de hacer la iteración:  
    <ul>
        @forelse($users as $user)
            <li>{{$user->name}}</li>
            @empty
            <li>lista vacia</li>
        @endforelse
    </ul>
-->
    @endsection