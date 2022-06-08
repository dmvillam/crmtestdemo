@extends('layouts.app')

@section('title', "Usuarios")

@section('styles')
<style type="text/css">
  table {
    width: 100% !important;
  }
  .btn-visualizar, .btn-modificar, .btn-eliminar {
    margin-right: 8px;
  }
  .invalid-feedback {
    display: block;
    margin: -15px 0 5px 0;
  }
</style>
@endsection

@section('modals')
@include('users.modals.create')
@include('users.modals.details')
@include('users.modals.edit')
@include('users.modals.delete')
@endsection

@section('content')
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Usuarios</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <!--
          <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
          </div>
          -->
          <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
            Crear usuario
          </button>
        </div>
      </div>

      <div class="table-responsive">
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
      </div>

      <form id="form-eliminar-usuario" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="DELETE"/>
        <input type="hidden" name="delete_user" id="delete_user" value="">
      </form>

      <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="liveToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="d-flex">
            <div class="toast-body">
              Hello, world! This is a toast message.
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
        </div>
      </div>
@endsection

@section('scripts')
<script type="text/javascript">
  $(document).ready(function(){
    $('[data-bs-toggle=tooltip]').tooltip()

    $('.btn-modificar, .btn-eliminar').on('click', function(e){
      e.preventDefault()
    })

    $('table').DataTable({
      responsive: true,
      buttons: [
        'copy', 'csv', 'excel', 'pdf', 'print'
      ],
      dom: 'lfrtBip',
      "drawCallback": function( settings ) {
        $('.btn-visualizar').on('click', acionViewUser)
        $('.btn-modificar').on('click', actionEditUser)
        $('.btn-eliminar').on('click', actionDeleteUser)
      }
    })

    $('.btn-crear-usuario').on('click', function() {
      $('#form-crear-usuario').submit()
    })

    @if ($errors->store->any())
      var myModal = new bootstrap.Modal(document.getElementById('staticBackdrop'))
      myModal.show()
    @elseif ($errors->update->any())
      var myModal = new bootstrap.Modal(document.getElementById('modal-user-edit'))
      myModal.show()
    @endif

    function acionViewUser (e) {
      e.preventDefault()
      var user = $(this).closest('tr').data('user-id')
      $.ajax({
        url: "{{ route('users.show', ':user') }}".replace(':user', user),
        type: 'GET',
        dataType: 'json',
        success: function(user) {
          var user_details_html = ''
          $.each( user , function(key, value) {
            user_details_html += `
  <div class="row justify-content-start">
    <div class="col-4"><strong>${key}:</strong></div>
    <div class="col-4">${value}</div>
  </div>`
          })
          $('#modal-user-details .modal-title').html(`Detalles de usuario: ${user.nombre}`)
          $('#modal-user-details .modal-body').html(user_details_html)
          var myModal = new bootstrap.Modal(document.getElementById('modal-user-details'))
          myModal.show()
        },
        error: function(xhr, status) {
          alert('Un error inesperado ha ocurrido.'); // replace with modal
          console.log({xhr:xhr, status:status})
        },
      })
    }

    function actionEditUser (e) {
      e.preventDefault()
      var user = $(this).closest('tr').data('user-id')
      $.ajax({
        url: "{{ route('users.edit', ':user') }}".replace(':user', user),
        type: 'GET',
        dataType: 'json',
        success: function(user) {
          $.each( user , function(key, value) {
            $(`#edit_${key}`).val(value)
          })
          $('#modal-user-edit .modal-title').html(`Editar detalles de usuario: ${user.nombre}`)
          var myModal = new bootstrap.Modal(document.getElementById('modal-user-edit'))
          myModal.show()
        },
        error: function(xhr, status) {
          alert('Un error inesperado ha ocurrido.'); // replace with modal
          console.log({xhr:xhr, status:status})
        },
      })
    }

    $('.btn-editar-usuario').on('click', function (e) {
      e.preventDefault()
      var user = $('#edit_id').val()
      var action = "{{ route('users.update', ':user') }}".replace(':user', user)
      $('#form-editar-usuario').attr('action', action).submit()
      return

      var formdata = {}
      $("#form-editar-usuario input").each(function () {
        formdata[$(this).attr('name')] = $(this).val();
      })
      console.log({action:action, params:formdata})
    })

    function actionDeleteUser (e) {
      e.preventDefault()
      var user = $(this).closest('tr').data('user-id')
      $.ajax({
        url: "{{ route('users.show', ':user') }}".replace(':user', user),
        type: 'GET',
        dataType: 'json',
        success: function(user) {
          $('#delete_user').val(user.id)
          var action = '{{ route('users.delete', ':user') }}'.replace(':user', user.id)
          $('#form-eliminar-usuario').attr('action', action)
          $('#modal-user-delete .modal-title').html(`Eliminar usuario: <strong>${user.nombre}</strong>`)
          $('#modal-user-delete .modal-body').html(`Está por eliminar de manera irreversible al usuario <em>"${user.nombre}"</em> junto con todos sus datos, ¿Desea continuar?`)
          var myModal = new bootstrap.Modal(document.getElementById('modal-user-delete'))
          myModal.show()
        },
        error: function(xhr, status) {
          alert('Un error inesperado ha ocurrido.'); // replace with modal
          console.log({xhr:xhr, status:status})
        },
      })
    }

    $('.btn-eliminar-usuario').on('click', function (e) {
      e.preventDefault()
      $('#form-eliminar-usuario').submit()
    })

@if (session('status'))
    const toastLiveExample = document.getElementById('liveToast')
    const toast = new bootstrap.Toast(toastLiveExample)
    $('#liveToast .toast-body').text(`{{ session('status') }}`)
    toast.show()
@endif

  })
</script>
@endsection