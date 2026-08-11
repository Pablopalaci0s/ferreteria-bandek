@component('admin.layout')


@slot('header')
    Editar proveedor
@endslot

<a
    href="{{ route('admin.proveedores.index') }}"
    class="text-sm text-gray-500 hover:text-red-800 mb-4 inline-block"
>
    &larr; Volver a proveedores
</a>

<form
    action="{{ route('admin.proveedores.update', $proveedor) }}"
    method="POST"
    class="bg-white p-6 rounded-lg border max-w-lg"
>

    @csrf
    @method('PUT')

    @include('admin.proveedores._form')

    <button class="w-full bg-red-800 hover:bg-red-900 text-white font-medium py-2.5 rounded-md">
        Actualizar proveedor
    </button>

</form>


@endcomponent
