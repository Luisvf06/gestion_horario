<header>
    <nav>
        <ul>
            <li>
                <a href="{{route('user.index')}}">Home</a><!-- con este sistema se logra que la aplicación considere que la ruta es la que tiene el ->name en routes/web de forma que si se cambia la ruta, mientras se mantenga el valor de name, no se romperá el enlace--> 
            </li>
            <li>
                <a href="/about">about</a>
            </li>
            <li>
                <a href="/services">services</a>
            </li>
        </ul>
    </nav>
</header>