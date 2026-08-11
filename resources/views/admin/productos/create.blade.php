@component('admin.layout')

    @slot('header')
        Nuevo producto
    @endslot

    <a href="{{ route('admin.productos.index') }}"
       class="text-sm text-gray-500 hover:text-red-800 mb-4 inline-block">
        &larr; Volver a productos
    </a>

    <form
        action="{{ route('admin.productos.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="bg-white p-6 rounded-lg border max-w-2xl"
    >
        @csrf

        @include('admin.productos._form')

        <button
            type="submit"
            class="w-full bg-red-800 hover:bg-red-900 text-white font-medium py-2.5 rounded-md"
        >
            Guardar producto
        </button>
    </form>

@endcomponent