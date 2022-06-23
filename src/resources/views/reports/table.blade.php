<table class="table table-striped table-sm">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Nombre</th>
      <th scope="col">Empresa</th>
      <th scope="col">Rol</th>
      <th scope="col">Acciones</th>
    </tr>
  </thead>
  <tbody>
    @forelse($users as $user)
    <tr data-user-id="{{ $user->id }}">
      <td>{{ $user->id }}</td>
      <td>{{ $user->nombre }}</td>
      <td>{{ $user->empresa ? $user->empresa->nombre : 'Ninguna' }}</td>
      <td>{{ $user->rol->nombre }}</td>
      <td>
        <a href="#" class="link-secondary btn-visualizar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" title="Visualizar"><i class="fa-solid fa-eye"></i></a>
                
        <a href="#" class="link-secondary btn-modificar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" title="Modificar"><i class="fa-solid fa-pencil"></i></a>
                
        <a href="#" class="link-secondary btn-eliminar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" title="Eliminar"><i class="fa-solid fa-trash-can"></i></a>
      </td>
    </tr>
    @empty
    <tr><td colspan="5"><em>No hay usuarios para mostrar por el momento...</em></td></tr>
    @endforelse
  </tbody>
</table>
