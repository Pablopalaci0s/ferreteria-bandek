@php
    $configSitio = \App\Models\Configuracion::pluck('valor', 'clave');
    $direccionSitio = $configSitio['direccion'] ?? 'San Salvador, El Salvador';
    $telefonoSitio = $configSitio['telefono'] ?? null;

    $secciones = [
        'aceptacion' => '1. Aceptación de los términos',
        'naturaleza' => '2. Qué es este sitio',
        'precios' => '3. Precios y disponibilidad',
        'pedidos-whatsapp' => '4. Confirmación de pedidos por WhatsApp',
        'entregas' => '5. Entregas',
        'uso-permitido' => '6. Uso permitido del sitio',
        'restriccion-acceso' => '7. Restricción de acceso y uso indebido',
        'propiedad-intelectual' => '8. Propiedad intelectual',
        'datos' => '9. Protección de datos',
        'responsabilidad' => '10. Limitación de responsabilidad',
        'cambios' => '11. Cambios a estos términos',
        'ley-aplicable' => '12. Ley aplicable y jurisdicción',
        'contacto' => '13. Contacto',
    ];
@endphp

<x-layouts.tienda
    :title="'Términos de servicio — Ferretería BANDEK'"
    :description="'Condiciones de uso del sitio web de Ferretería BANDEK, conforme a la legislación de El Salvador.'"
>

    <div class="bandek-legal">

        {{-- =========================================================
             ENCABEZADO
        ========================================================= --}}

        <section class="bandek-legal-header">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <p class="bandek-legal-eyebrow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Documento legal
                </p>
                <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3 leading-tight">Términos de servicio</h1>
                <p class="text-gray-400 max-w-2xl leading-7">
                    Condiciones de uso del sitio de Ferretería BANDEK, conforme a la legislación vigente de la República de El Salvador.
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
                            <li>Este sitio es un catálogo; los pedidos se coordinan por WhatsApp, no hay pago en línea.</li>
                            <li>Escribirnos por WhatsApp no confirma una compra por sí solo: la compra queda cerrada cuando acordamos precio, cantidad y condiciones.</li>
                            <li>Precios, existencias y condiciones de entrega pueden variar y siempre se confirman antes de cerrar un pedido.</li>
                            <li>Podemos restringir el acceso a quien use el sitio de forma abusiva, fraudulenta o dañina.</li>
                        </ul>
                    </div>

                    <div class="prose prose-gray max-w-none">

                        <p class="text-gray-600 leading-7 mb-10">
                            Al usar el sitio web de Ferretería BANDEK aceptás estos términos. Si no estás de
                            acuerdo con alguno, te pedimos que no uses el sitio.
                        </p>

                        <div id="aceptacion" class="bandek-legal-section">
                            <h2><span class="bandek-legal-num">1</span>Aceptación de los términos</h2>
                            <p>
                                El acceso y uso de este sitio implica la aceptación plena de estos términos de
                                servicio. Estos términos se enmarcan en la legislación mercantil y de protección
                                al consumidor de El Salvador, en particular el <strong>Código de Comercio</strong>
                                y la <strong>Ley de Protección al Consumidor</strong> (Decreto Legislativo N.º 776).
                            </p>
                        </div>

                        <div id="naturaleza" class="bandek-legal-section">
                            <h2><span class="bandek-legal-num">2</span>Qué es este sitio</h2>
                            <p>
                                Este sitio es un catálogo en línea de Ferretería BANDEK. No es una tienda con pago
                                en línea: los pedidos se coordinan directamente por WhatsApp, y la compra se
                                concreta en tienda o al momento de la entrega. La información publicada en el
                                catálogo está sujeta a verificación y confirmación antes de concretar cualquier
                                compra.
                            </p>
                        </div>

                        <div id="precios" class="bandek-legal-section">
                            <h2><span class="bandek-legal-num">3</span>Precios y disponibilidad</h2>
                            <p>
                                Los precios, existencias y descripciones publicados en el sitio pueden cambiar
                                sin previo aviso o contener errores. Antes de confirmar un pedido, verificaremos
                                por WhatsApp el precio, la disponibilidad y las condiciones de la compra. Si
                                una compra ya coordinada con nosotros no se resuelve de forma directa, podés
                                acudir a la Defensoría del Consumidor.
                            </p>
                        </div>

                        <div id="pedidos-whatsapp" class="bandek-legal-section">
                            <h2><span class="bandek-legal-num">4</span>Confirmación de pedidos por WhatsApp</h2>
                            <p>
                                Como los pedidos se coordinan por WhatsApp, es importante aclarar cómo funciona
                                ese proceso:
                            </p>
                            <ul>
                                <li>El envío de un mensaje por WhatsApp no constituye por sí mismo una compra confirmada.</li>
                                <li>Ferretería BANDEK confirma disponibilidad, precio, cantidad y condiciones antes de dar por hecho un pedido.</li>
                                <li>La compra queda confirmada cuando ambas partes acuerdan esas condiciones, no antes.</li>
                            </ul>
                        </div>

                        <div id="entregas" class="bandek-legal-section">
                            <h2><span class="bandek-legal-num">5</span>Entregas</h2>
                            <p>Para los pedidos que se coordinan con entrega a domicilio:</p>
                            <ul>
                                <li>El costo de envío puede variar según la ubicación de entrega.</li>
                                <li>El tiempo estimado de entrega puede cambiar según disponibilidad y logística.</li>
                                <li>Es responsabilidad de quien compra proporcionar una dirección correcta y completa.</li>
                                <li>El costo, tiempo y demás condiciones de entrega se confirman por WhatsApp antes de cerrar la compra.</li>
                            </ul>
                        </div>

                        <div id="uso-permitido" class="bandek-legal-section">
                            <h2><span class="bandek-legal-num">6</span>Uso permitido del sitio</h2>
                            <p>Al navegar el sitio te comprometés a no:</p>
                            <ul>
                                <li>Intentar dañar, sobrecargar o interrumpir el funcionamiento del sitio o sus sistemas.</li>
                                <li>Intentar acceder sin autorización a áreas restringidas, cuentas ajenas o datos que no te correspondan.</li>
                                <li>Usar robots, scrapers o herramientas automatizadas para extraer contenido de forma masiva o abusiva.</li>
                                <li>Usar el sitio para fines ilegales, fraudulentos o que perjudiquen a Ferretería BANDEK o a terceros.</li>
                                <li>Suplantar a otra persona o proporcionar información falsa al contactarnos.</li>
                            </ul>
                            <p>
                                Las conductas anteriores, cuando afectan sistemas informáticos, su infraestructura
                                o los datos que contienen, pueden constituir delito conforme a la
                                <strong>Ley Especial Contra los Delitos Informáticos y Conexos</strong>
                                (Decreto Legislativo N.º 260), sin perjuicio de las acciones civiles que
                                correspondan.
                            </p>
                        </div>

                        <div id="restriccion-acceso" class="bandek-legal-section">
                            <h2><span class="bandek-legal-num">7</span>Restricción de acceso y uso indebido</h2>
                            <p>
                                Ferretería BANDEK puede restringir, suspender o limitar el acceso al sitio, así
                                como el trato comercial con una persona en particular (incluyendo por WhatsApp),
                                cuando exista abuso, fraude, intentos de ataque o intrusión, uso automatizado
                                excesivo, u otra conducta que dañe la plataforma, a otros usuarios o a la
                                empresa. Esta restricción se aplica de forma proporcional a la situación y no
                                requiere aviso previo cuando la urgencia del caso lo justifique.
                            </p>
                        </div>

                        <div id="propiedad-intelectual" class="bandek-legal-section">
                            <h2><span class="bandek-legal-num">8</span>Propiedad intelectual</h2>
                            <p>
                                El nombre "Ferretería BANDEK", su logo y el diseño del sitio son propiedad de
                                Ferretería BANDEK. Las marcas, logotipos y demás material de fabricantes y
                                proveedores que se venden en la tienda pertenecen a sus respectivos titulares;
                                se muestran en el catálogo únicamente con fines informativos, sin que eso
                                implique una cesión de derechos a nuestro favor. No está permitido reproducir
                                ningún contenido del sitio con fines comerciales sin autorización.
                            </p>
                        </div>

                        <div id="datos" class="bandek-legal-section">
                            <h2><span class="bandek-legal-num">9</span>Protección de datos</h2>
                            <p>
                                El tratamiento de datos personales en este sitio se rige por nuestra
                                <a href="{{ route('privacidad') }}">Política de privacidad</a>, elaborada
                                conforme a la Ley de Protección de Datos Personales de El Salvador
                                (Decreto Legislativo N.º 144).
                            </p>
                        </div>

                        <div id="responsabilidad" class="bandek-legal-section">
                            <h2><span class="bandek-legal-num">10</span>Limitación de responsabilidad</h2>
                            <p>
                                El sitio se ofrece "tal cual". No garantizamos que esté libre de errores o
                                interrupciones. Ferretería BANDEK no se hace responsable por daños indirectos
                                derivados del uso del sitio, sin perjuicio de los derechos irrenunciables que
                                la ley aplicable reconozca a los consumidores.
                            </p>
                        </div>

                        <div id="cambios" class="bandek-legal-section">
                            <h2><span class="bandek-legal-num">11</span>Cambios a estos términos</h2>
                            <p>
                                Podemos actualizar estos términos en cualquier momento; la fecha de "última
                                actualización" arriba refleja la versión vigente. El uso continuado del sitio
                                después de un cambio implica que lo aceptás.
                            </p>
                        </div>

                        <div id="ley-aplicable" class="bandek-legal-section">
                            <h2><span class="bandek-legal-num">12</span>Ley aplicable y jurisdicción</h2>
                            <p>
                                Estos términos se rigen por las leyes de la República de El Salvador. Cualquier
                                controversia derivada del uso del sitio se someterá a los tribunales competentes
                                de El Salvador, sin perjuicio de los mecanismos de protección al consumidor que
                                correspondan.
                            </p>
                        </div>

                        <div id="contacto" class="bandek-legal-section">
                            <h2><span class="bandek-legal-num">13</span>Contacto</h2>
                            <p>Si tenés dudas sobre estos términos, escribinos:</p>
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
        .bandek-legal-section a {
            color: #991b1b;
            font-weight: 600;
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
