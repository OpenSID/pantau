<ul class="nav p-3 ">
    @foreach(array_slice(app(JeroenNoten\LaravelAdminLte\AdminLte::class)->menu(),2, 5) as $itemMenu)
    <li class="nav-item w-100"><a class="nav-link " href="{{ url($itemMenu['href']) }}"><i
                class="{{ $itemMenu['icon'] }} "></i>{{ $itemMenu['text'] }}</a></li>    
    @endforeach
    <li class="nav-item w-100  bg-primary text-center mt-2 text-bold"><a class="nav-link " href="{{ url('login') }}"><i
        class="fas pantau-icon fa-sign-in-alt "></i>Login</a></li>
</ul>

            