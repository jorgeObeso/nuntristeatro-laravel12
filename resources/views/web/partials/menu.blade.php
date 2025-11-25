{{-- Menú principal --}}
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('inicio', ['idioma' => idioma_actual()]) }}">
            {{ config('app.name', 'Nuntris Teatro') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu" aria-controls="mainMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainMenu">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                {{-- Aquí puedes iterar los menús dinámicos si los tienes --}}
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('inicio', ['idioma' => idioma_actual()]) }}">@lang('Inicio')</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
