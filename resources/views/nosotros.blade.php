<x-layouts.tienda
    :title="'Ferretería BANDEK — Nosotros'"
    :description="'Conocé la historia, misión y valores de Ferretería BANDEK: materiales de construcción, herramientas y asesoría técnica en El Salvador.'"
>

    {{-- =========================================================
    HERO
    ========================================================= --}}

    <section class="bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 text-center">

            <p class="text-red-500 font-semibold text-sm uppercase tracking-wider mb-3">
                Nosotros
            </p>

            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-4 leading-tight">
                Ferretería BANDEK
            </h1>

            <p class="text-gray-400 max-w-2xl mx-auto leading-7">
                Materiales de construcción, herramientas y soluciones eléctricas,
                con la asesoría técnica de un equipo que conoce el oficio.
            </p>

        </div>
    </section>

    {{-- =========================================================
    HISTORIA
    ========================================================= --}}

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid md:grid-cols-2 gap-10 items-center">

            <div>
                <p class="text-red-800 font-semibold text-sm uppercase tracking-wider mb-2">
                    Nuestra historia
                </p>

                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 leading-tight">
                    Comprometidos con cada proyecto
                </h2>

                <p class="text-gray-600 leading-7 mb-4">
                    Ferretería BANDEK nació en San Salvador con un objetivo simple:
                    ofrecer materiales de calidad y asesoría honesta a quienes construyen,
                    reparan y mantienen sus proyectos día a día, desde el maestro de obra
                    hasta la persona que arregla su casa por primera vez.
                </p>

                <p class="text-gray-600 leading-7">
                    Trabajamos para ser un punto de referencia en materiales eléctricos
                    y ferretería general, siempre con el mismo compromiso: tener el
                    producto correcto, al precio correcto, con alguien que sepa
                    explicarte para qué sirve.
                </p>
            </div>

            <div class="bg-white border rounded-lg h-64 md:h-80 flex items-center justify-center p-8">
                <img src="{{ asset('img/logo-completo.png') }}" alt="Ferretería BANDEK" class="h-full w-auto object-contain">
            </div>

        </div>
    </section>

    {{-- =========================================================
    MISIÓN / VISIÓN / VALORES
    ========================================================= --}}

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

        <div class="text-center max-w-2xl mx-auto mb-12">
            <p class="text-red-800 font-semibold text-sm uppercase tracking-wider mb-2">
                Lo que nos mueve
            </p>
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">
                Misión, visión y valores
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="bandek-card bg-white border rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Misión</h3>
                <p class="text-sm text-gray-600 leading-6">
                    Proveer materiales de construcción, herramientas y soluciones
                    eléctricas de calidad, con atención cercana y asesoría técnica
                    real, para que cada cliente resuelva su proyecto con confianza.
                </p>
            </div>

            <div class="bandek-card bg-white border rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Visión</h3>
                <p class="text-sm text-gray-600 leading-6">
                    Ser la ferretería de referencia en El Salvador, reconocida por
                    la calidad de sus productos, la confianza de sus clientes y
                    un servicio que combina experiencia con tecnología.
                </p>
            </div>

            <div class="bandek-card bg-white border rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Valores</h3>
                <p class="text-sm text-gray-600 leading-6">
                    Honestidad en cada recomendación, calidad en cada producto,
                    respeto por el tiempo del cliente y compromiso con el trabajo
                    bien hecho.
                </p>
            </div>

        </div>
    </section>

    {{-- =========================================================
    POR QUÉ ELEGIRNOS
    ========================================================= --}}

    <section class="bg-gray-50 border-y">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

            <div class="text-center max-w-2xl mx-auto mb-12">
                <p class="text-red-800 font-semibold text-sm uppercase tracking-wider mb-2">
                    Por qué elegirnos
                </p>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">
                    La ferretería de confianza para tu proyecto
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-3xl mx-auto">

                @foreach ([
                    'Asesoría técnica real, no solo venta de productos',
                    'Materiales eléctricos y de construcción de calidad',
                    'Atención rápida por WhatsApp',
                    'Precios justos y transparentes',
                    'Stock actualizado de nuestros productos más buscados',
                    'Compromiso real con cada proyecto, grande o pequeño',
                ] as $beneficio)

                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-700 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <p class="text-sm text-gray-700">{{ $beneficio }}</p>
                    </div>

                @endforeach

            </div>

        </div>
    </section>

    {{-- =========================================================
    CTA FINAL
    ========================================================= --}}

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">

        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">
            ¿Tenés un proyecto en mente?
        </h2>

        <p class="text-gray-600 max-w-xl mx-auto mb-8 leading-7">
            Escribinos y te ayudamos a encontrar exactamente lo que necesitás,
            con asesoría técnica incluida.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">

            <a href="https://wa.me/{{ \App\Models\Configuracion::where('clave', 'whatsapp_numero')->value('valor') }}"
               target="_blank" rel="noopener noreferrer"
               class="bandek-btn-cta bg-green-500 hover:bg-green-600 text-white font-semibold px-6 py-3 rounded-md transition">
                Contactar por WhatsApp
            </a>

            <a href="{{ route('catalogo.index') }}"
               class="bandek-btn-cta bg-red-800 hover:bg-red-900 text-white font-semibold px-6 py-3 rounded-md transition">
                Ver catálogo
            </a>

        </div>

    </section>

</x-layouts.tienda>
