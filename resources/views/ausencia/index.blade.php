@extends('../layouts.langind')
<!--En el primer section, el primer elemento es el elemento de layouts y el segundo es su valor. El segundo sectoin solo es el elemento ya que el contenido se indica dentro del section-->
@section('title','index')
@section('content')
<h1>Contenido de la aplicacion</h1>
@if($ausencias->isEmpty())
    <p>No hay datos en ausencias</p>
@else
    <ul>
        @foreach ($ausencias as $ausencia)
            <li><a href="{{'ausencia.show',$ausencia->id}}">{{$ausencia->ausencia_descripcion}} </a>
                <a href="{{'ausencia.edit',['ausencia'=>$ausencia->id]}}">EDIT</a> |
                <form method="POST" action="{{route('ausencia.destroy',$ausencia->id)}}">
                    @csrf
                    @method('DELETE')
                <input type="submit" value="DELETE"/>
            </form>
        </li>
        @endforeach

    </ul>

@endif  
    @endsection