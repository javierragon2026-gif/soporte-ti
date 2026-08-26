@auth
<div class="tinyHeader">
    <div class="float-left">
        @include('components.gravatar', ["user" => auth()->user()])
    </div>
    <a href="{{ route('profile.show') }}">
        <button class="ternary fs2">{{ auth()->user()->name }}</button>
    </a>
    <div class="float-right ml3">
        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit" class="ternary fs2">
                <i class="fa fa-sign-out"></i>
            </button>
        </form>
    </div>
</div>
@endauth