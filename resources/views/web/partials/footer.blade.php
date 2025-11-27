@php use Illuminate\Support\Facades\Storage; @endphp
<footer >
    <div class="container">
 
            <div class="col-lg-3">
                <h5 class="text-uppercase fw-bold mb-3">{{ $configEmpresa->nombre ?? '' }}</h5>
                @php
                    $idiomaEtiqueta = app()->getLocale();
                    $textoEmpresa = $configEmpresa->textos->first(function($t) use ($idiomaEtiqueta) {
                        return optional($t->idioma)->etiqueta === $idiomaEtiqueta;
                    }) ?? $configEmpresa->textos->first();
                @endphp
                <p class="mb-0">{{ $textoEmpresa->metadescripcion ?? '' }}</p>
            </div>
            <div class="col-lg-3">
                <a href="#">
                    <img src="/images/logo.png" alt="Nun Tris Teatro" height="48">
                </a>
               
            </div>
            <div class="col-lg-3">
                <nav>
                    <ul>
                    @foreach(($menusPie ?? []) as $item)
                        <li><a href="{{ menu_url($item, $idiomaRuta) }}">{{ $item->titulo }}</a></li>
                    @endforeach
                    </ul>
                </nav>
            </div>
            <div class="col-lg-3">
               
                <ul class="info-empresa">
                    <li>{{ $configEmpresa->metatitulo ?? '' }}</li>
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
    <div class="cierre-footer-container">
        <div class="row">  
            <p> &copy; {{ now()->year }} {{ $configEmpresa->nombre ?? 'Nuntris Teatro' }} · {{ __('Todos los derechos reservados') }}</p>
        </div><!-- //.row -->
        </div><!-- //.container -->