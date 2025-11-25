@php use Illuminate\Support\Facades\Storage; @endphp
<footer >
    <div class="container">
 
            <div class="col-lg-4">
                <h5 class="text-uppercase fw-bold mb-3">{{ $configuracion->nombre_empresa ?? 'Nuntris Teatro' }}</h5>
                <p class="mb-0">{{ $configuracion->metadescripcion ?? 'Compañía de teatro asturiana especializada en obras clásicas y contemporáneas.' }}</p>
            </div>
            <div class="col-lg-4">
                <a href="#">
                    <img src="/images/logo.png" alt="Nun Tris Teatro" height="48">
                </a>
               
            </div>
            <div class="col-lg-4">
                <nav>
                    <ul>
                    @foreach(($menusPie ?? []) as $item)
                        <li><a class="nav-link px-2 text-light" href="{{ menu_url($item, $idiomaRuta) }}">{{ $item->titulo }}</a></li>
                    @endforeach
                    </ul>
                </nav>
            </div>
            <div class="col-lg-4">
               
                <ul class="info-empresa">
                    <li>{{ $configEmpresa->nombre ?? '' }}</li>
                    <li><a href="tel:{{ $configEmpresa->telefono ?? '' }}">{{ $configEmpresa->telefono ?? '' }}</a></li>
                    <li><a href="mailto:{{ $configEmpresa->email ?? '' }}">{{ $configEmpresa->email ?? '' }}</a></li>
                </ul>
              
                    
                    @if(!empty($redesSociales))
                        <ul class="redes">
                            @foreach($redesSociales as $red)
                                @if(!empty($red['url']) && !empty($red['icono']))
                                    <li>
                                        <a href="{{ $red['url'] }}" target="_blank" rel="noopener">
                                            <img src="{{ Storage::url($red['icono']) }}" alt="{{ $red['alt'] ?? '' }}">
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
             
            </div><!-- //.col-lg-4 -->

    </div><!-- //.container -->



</footer>
    <div class="container cierre-footer-container">
        <div class="row">  
            <p> &copy; {{ now()->year }} {{ $configEmpresa->nombre ?? 'Nuntris Teatro' }} · {{ __('Todos los derechos reservados') }}</p>
        </div><!-- //.row -->
        </div><!-- //.container -->