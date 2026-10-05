<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClienteController extends Controller
{
    /**
     * Muestra la lista de clientes con soporte para búsqueda.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');

        $clientes = Cliente::query()
            ->when($search, function ($query, $search) {
                $query->where('numero_documento', 'ilike', "%{$search}%")
                    ->orWhere('nombres', 'ilike', "%{$search}%")
                    ->orWhere('apellidos', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
            })
            ->withCount('cuentas', 'prestamos')
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('clientes/Index', [
            'clientes' => $clientes,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Muestra el formulario para registrar un nuevo cliente.
     */
    public function create(): Response
    {
        return Inertia::render('clientes/Create');
    }

    /**
     * Almacena un nuevo cliente en la base de datos (RF-004, RF-005).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'tipo_documento' => ['required', 'in:ci,pasaporte,ruc'],
            'numero_documento' => ['required', 'string', 'max:50', 'unique:clientes,numero_documento'],
            'email' => ['required', 'email', 'max:150', 'unique:clientes,email'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string'],
            'fecha_nacimiento' => ['nullable', 'date'],
        ]);

        $validated['estado'] = 'activo';
        $validated['registrado_por'] = $request->user()?->id;

        $cliente = Cliente::create($validated);

        Bitacora::registrar(
            accion: 'Registro de Cliente',
            detalles: "Se registró al cliente {$cliente->nombreCompleto()} con documento {$cliente->numero_documento}",
            tablaAfectada: 'clientes',
            registroId: $cliente->id
        );

        return redirect()->route('clientes.show', $cliente->id)
            ->with('success', 'Cliente registrado exitosamente en el sistema.');
    }

    /**
     * Muestra el expediente completo del cliente con sus cuentas y préstamos.
     */
    public function show(Cliente $cliente): Response
    {
        $cliente->load([
            'cuentas.tarjeta',
            'prestamos' => function ($query) {
                $query->latest();
            },
            'registradoPor',
        ]);

        return Inertia::render('clientes/Show', [
            'cliente' => $cliente,
        ]);
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(Cliente $cliente): Response
    {
        return Inertia::render('clientes/Edit', [
            'cliente' => $cliente,
        ]);
    }

    /**
     * Actualiza la información del cliente.
     */
    public function update(Request $request, Cliente $cliente): RedirectResponse
    {
        $validated = $request->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'tipo_documento' => ['required', 'in:ci,pasaporte,ruc'],
            'numero_documento' => ['required', 'string', 'max:50', "unique:clientes,numero_documento,{$cliente->id}"],
            'email' => ['required', 'email', 'max:150', "unique:clientes,email,{$cliente->id}"],
            'telefono' => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'estado' => ['required', 'in:activo,inactivo'],
        ]);

        $cliente->update($validated);

        Bitacora::registrar(
            accion: 'Actualización de Cliente',
            detalles: "Se actualizaron los datos del cliente {$cliente->nombreCompleto()}",
            tablaAfectada: 'clientes',
            registroId: $cliente->id
        );

        return redirect()->route('clientes.show', $cliente->id)
            ->with('success', 'Datos del cliente actualizados correctamente.');
    }
}
