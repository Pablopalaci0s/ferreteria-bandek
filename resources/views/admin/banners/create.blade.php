@component('admin.layout')

    @slot('header')
        Nuevo banner
    @endslot

    <a href="{{ route('admin.banners.index') }}"
       class="text-sm text-gray-500 hover:text-red-800 mb-4 inline-block">
        &larr; Volver a banners
    </a>

    <form
        action="{{ route('admin.banners.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="bg-white p-6 rounded-lg border max-w-2xl"
    >
        @csrf

        @include('admin.banners._form')

        <button
            type="submit"
            class="w-full bg-red-800 hover:bg-red-900 text-white font-medium py-2.5 rounded-md"
        >
            Guardar banner
        </button>
    </form>

@endcomponent
