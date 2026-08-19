@php
    $configSitio = \App\Models\Configuracion::pluck('valor', 'clave');
    $direccionSitio = $configSitio['direccion'] ?? 'San Salvador, El Salvador';
    $telefonoSitio = $configSitio['telefono'] ?? null;

    $secciones = [
        'marco-legal' => '1. Marco legal',
        'recoleccion' => '2. Qué información recolectamos',
        'navegador' => '3. Información guardada en tu navegador',
        'cuentas' => '4. Cuentas de administradores y vendedores',
        'derechos' => '5. Tus derechos sobre tus datos',
        'terceros' => '6. ¿Compartimos tu información?',
        'seguridad' => '7. Seguridad',
        'cambios' => '8. Cambios a esta política',
        'contacto' => '9. Contacto',
    ];
@endphp

<x-layouts.tienda
    :title="'Política de privacidad — Ferretería BANDEK'"
    :description="'Cómo usamos la información que nos compartís en Ferretería BANDEK, conforme a la Ley de Protección de Datos Personales de El Salvador.'"
>

    <div class="bandek-legal">

        {{-- =========================================================
             ENCABEZADO
        ========================================================= --}}

        <section class="bandek-legal-header">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <p class="bandek-legal-eyebrow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    Documento legal
                </p>
                <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3 leading-tight">Política de privacidad</h1>
                <p class="text-gray-400 max-w-2xl leading-7">
                    Cómo usamos la información que nos compartís, conforme a la Ley de Protección de Datos
                    Personales de la República de El Salvador.
                </p>
                <p class="text-gray-500 text-sm mt-4">
                    Última actualización: {{ now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
                </p>
            </div>
        </section>

        {{-- =========================================================
             CONTENIDO
        ========================================================= --}}

        <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
            <div class="grid lg:grid-cols-[240px_1fr] gap-10">

                {{-- ÍNDICE --}}
                <aside class="hidden lg:block">
                    <div class="bandek-legal-toc">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">En este documento</p>
                        <nav class="space-y-1">
                            @foreach ($secciones as $ancla => $titulo)
                                <a href="#{{ $ancla }}" class="bandek-legal-toc-link">{{ $titulo }}</a>
                            @endforeach
                        </nav>
                    </div>
                </aside>

                {{-- DOCUMENTO --}}
                <div class="bandek-legal-doc">

                    <div class="bandek-legal-resumen">
                        <p class="bandek-legal-resumen-titulo">En resumen</p>
                        <ul>
                            <li>No creamos una cuenta de cliente ni almacenamos deliberadamente tu pedido en la base de datos del catálogo cuando navegás el sitio.</li>
                            <li>Los pedidos se coordinan por WhatsApp, sujeto también a las políticas de esa plataforma.</li>
                            <li>Tu carrito, favoritos y preferencia de tema se guardan localmente en tu propio navegador.</li>
                            <li>Este sitio se elabora conforme a la Ley de Protección de Datos Personales de El Salvador (Decreto N.º 144).</li>
                        </ul>
                    </div>

                    <div class="prose prose-gray max-w-none">

                        <div id="marco-legal" class="bandek-legal-section">
                            <h2><span class="bandek-legal-num">1</span>Marco legal</h2>
                            <p>
                                Esta política se elabora conforme a la <strong>Ley de Protección de Datos
                                Personales</strong> de El Salvador (Decreto Legislativo N.º 144), y a las
                                disposiciones sobre confidencialidad de datos del consumidor de la
                                <strong>Ley de Protección al Consumidor</strong> (Decreto Legislativo N.º 776)
                                para las relaciones comerciales que coordinamos por WhatsApp.
                            </p>
                        </div>

                        <div id="recoleccion" class="bandek-legal-section">
                            <h2><span class="bandek-legal-num">2</span>Qué información recolectamos</h2>
                            <p>
                                Ferretería BANDEK es un catálogo en línea: no procesamos pagos ni creamos una
                                cuenta de cliente cuando navegás el sitio. Los pedidos se coordinan directamente
                                por WhatsApp. Cuando nos escribís (ya sea desde el botón de WhatsApp del sitio o
                                desde el carrito de compra), la información que compartís en esa conversación —
                                tu número de WhatsApp, tu nombre y los datos de entrega que nos indiques — no
                                se almacena deliberadamente en la base de datos de este catálogo.
                            </p>
                        </div>

                        <div id="navegador" class="bandek-legal-section">
                            <h2><span class="bandek-legal-num">3</span>Información guardada en tu navegador</h2>
                            <p>
                                Para que la tienda funcione mejor, guardamos algunos datos únicamente en tu
                                propio dispositivo (no en nuestros servidores):
                            </p>
                            <ul>
                                <li>Los productos que agregás al carrito de compra.</li>
                                <li>Los productos que marcás como favoritos.</li>
                                <li>Tu preferencia de modo oscuro/claro.</li>
                            </ul>
                            <p>
                                Esta información se almacena localmente en tu navegador y no se envía
                                deliberadamente a nuestros servidores como parte de estas funciones. Se borra
                                si limpiás los datos del navegador.
                            </p>
                        </div>

                        <div id="cuentas" class="bandek-legal-section">
                            <h2><span class="bandek-legal-num">4</span>Cuentas de administradores y vendedores</h2>
                            <p>
                                El panel administrativo del sitio es de uso interno, exclusivo para el personal
                                de Ferretería BANDEK. Las cuentas de acceso no están disponibles para el público
                                ni se generan a partir de la navegación del catálogo. Estas cuentas pueden
                                contener datos como nombre, correo electrónico, credenciales de acceso y
                                registros de actividad dentro del panel.
                            </p>
                        </div>

                        <div id="derechos" class="bandek-legal-section">
                            <h2><span class="bandek-legal-num">5</span>Tus derechos sobre tus datos</h2>
                            <p>
                                Bajo la Ley de Protección de Datos Personales, tenés derechos sobre la
                                información personal que nos hayas compartido, como acceder a ella, rectificarla
                                u oponerte a su uso. Podés comunicarte con nosotros mediante nuestros canales de
                                contacto para solicitar el ejercicio de los derechos que correspondan.
                            </p>
                        </div>

                        <div id="terceros" class="bandek-legal-section">
                            <h2><span class="bandek-legal-num">6</span>¿Compartimos tu información?</h2>
                            <p>
                                No vendemos tus datos personales. Podemos utilizar proveedores tecnológicos
                                necesarios para operar el sitio y prestar nuestros servicios (por ejemplo,
                                hosting, envío de correos y respaldo de información). Cuando utilizás WhatsApp
                                para coordinar un pedido, la información que compartís mediante esa plataforma
                                también está sujeta a las políticas de privacidad de WhatsApp y Meta.
                            </p>
                        </div>

                        <div id="seguridad" class="bandek-legal-section">
                            <h2><span class="bandek-legal-num">7</span>Seguridad</h2>
                            <p>
                                El panel administrativo está protegido con acceso restringido por usuario y
                                contraseña, exclusivo para personal autorizado de Ferretería BANDEK. No
                                procesamos pagos en línea ni almacenamos información de tarjetas en este sitio.
                                Aun así, ningún sistema conectado a Internet puede garantizar seguridad
                                absoluta.
                            </p>
                        </div>

                        <div id="cambios" class="bandek-legal-section">
                            <h2><span class="bandek-legal-num">8</span>Cambios a esta política</h2>
                            <p>
                                Podemos actualizar esta política en cualquier momento. La fecha de "última
                                actualización" al inicio de este documento indica cuándo se modificó por
                                última vez.
                            </p>
                        </div>

                        <div id="contacto" class="bandek-legal-section">
                            <h2><span class="bandek-legal-num">9</span>Contacto</h2>
                            <p>
                                Si tenés dudas sobre esta política o querés ejercer tus derechos sobre tus
                                datos, escribinos:
                            </p>
                            <ul class="bandek-legal-contacto">
                                <li>📍 {{ $direccionSitio }}</li>
                                @if ($telefonoSitio)
                                    <li>📞 {{ $telefonoSitio }}</li>
                                @endif
                            </ul>
                        </div>

                    </div>

                </div>

            </div>
        </section>

    </div>

    <style>
        .bandek-legal-header {
            background: #111827;
            padding: 3.5rem 0 3rem;
        }
        .bandek-legal-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: #f87171;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.9rem;
        }
        .bandek-legal-toc {
            position: sticky;
            top: 6rem;
        }
        .bandek-legal-toc-link {
            display: block;
            padding: 0.4rem 0.6rem;
            border-radius: 0.375rem;
            font-size: 0.85rem;
            color: #4b5563;
            text-decoration: none;
            line-height: 1.4;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .bandek-legal-toc-link:hover {
            background: #f9fafb;
            color: #991b1b;
        }
        .bandek-legal-doc {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(17,24,39,0.05);
            padding: 2rem 1.5rem;
        }
        @media (min-width: 640px) {
            .bandek-legal-doc { padding: 2.75rem 3rem; }
        }
        .bandek-legal-resumen {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 0.5rem;
            padding: 1.25rem 1.5rem;
            margin-bottom: 2.5rem;
        }
        .bandek-legal-resumen-titulo {
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #991b1b;
            margin-bottom: 0.6rem;
        }
        .bandek-legal-resumen ul {
            margin: 0;
            padding-left: 1.1rem;
            color: #7f1d1d;
            font-size: 0.9rem;
            line-height: 1.6;
        }
        .bandek-legal-section {
            padding: 1.75rem 0;
            border-top: 1px solid #f1f1f1;
        }
        .bandek-legal-section:first-of-type { border-top: none; padding-top: 0; }
        .bandek-legal-section h2 {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-family: 'Oswald', sans-serif;
            font-size: 1.15rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.75rem;
            scroll-margin-top: 6rem;
        }
        .bandek-legal-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.75rem;
            height: 1.75rem;
            border-radius: 9999px;
            background: #991b1b;
            color: #fff;
            font-size: 0.8rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        .bandek-legal-section p,
        .bandek-legal-section li {
            color: #4b5563;
            line-height: 1.7;
            font-size: 0.95rem;
        }
        .bandek-legal-section ul {
            padding-left: 1.1rem;
            margin: 0.5rem 0;
        }
        .bandek-legal-contacto {
            list-style: none;
            padding-left: 0;
            margin-top: 0.5rem;
        }
        body.oscuro .bandek-legal-doc { background: #1f2937 !important; border-color: #374151 !important; }
        body.oscuro .bandek-legal-section { border-color: #374151 !important; }
        body.oscuro .bandek-legal-section h2 { color: #f8fafc !important; }
        body.oscuro .bandek-legal-section p,
        body.oscuro .bandek-legal-section li { color: #b0bac6 !important; }
        body.oscuro .bandek-legal-toc-link { color: #9aa5b1 !important; }
        body.oscuro .bandek-legal-toc-link:hover { background: #374151 !important; color: #f87171 !important; }
        body.oscuro .bandek-legal-resumen { background: rgba(153,27,27,0.15) !important; border-color: #7f1d1d !important; }
        body.oscuro .bandek-legal-resumen ul { color: #fca5a5 !important; }
        html { scroll-behavior: smooth; }
    </style>

</x-layouts.tienda>
