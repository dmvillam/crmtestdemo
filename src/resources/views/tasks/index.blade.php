@extends('layouts.app')

@section('title', "Tareas")

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
@include('tasks.modals.create')
@include('tasks.modals.details')
@include('tasks.modals.edit')
@include('tasks.modals.delete')
@endsection

@section('content')
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Tareas</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modal-crear-tarea">
            Crear tarea
          </button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-sm">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Nombre</th>
              <th scope="col">Cliente</th>
              <th scope="col">Periodicidad</th>
              <th scope="col">Notificación</th>
              <th scope="col">SMS</th>
              <th scope="col">Email</th>
              <th scope="col">Estado</th>
              <th scope="col">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($tareas as $tarea)
            <tr data-tarea-id="{{ $tarea->id }}">
              <td>{{ $tarea->id }}</td>
              <td>{{ $tarea->nombre }}</td>
              <td>{{ $tarea->cliente->nombre }}</td>
              <td>{{ $tarea->periodicidad }}</td>
              <td>{{ $tarea->notificacion ? $tarea->notificacion->nombre : "" }}</td>
              <td>{{ $tarea->notificacion ? $tarea->notificacion->notificar_email : "" }}</td>
              <td>{{ $tarea->notificacion ? $tarea->notificacion->notificar_sms : "" }}</td>
              <td>?</td>
              <td>
                <a href="#" class="link-secondary btn-visualizar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" title="Visualizar"><i class="fa-solid fa-eye"></i></a>
                
                <a href="#" class="link-secondary btn-modificar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" title="Modificar"><i class="fa-solid fa-pencil"></i></a>
                
                <a href="#" class="link-secondary btn-eliminar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" title="Eliminar"><i class="fa-solid fa-trash-can"></i></a>
              </td>
            </tr>
            @empty
            <tr><td colspan="9"><em>No hay tareas para mostrar por el momento...</em></td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <form id="form-eliminar-tarea" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="DELETE"/>
        <input type="hidden" name="delete_task" id="delete_task" value="">
      </form>

      <div class="toast-container position-fixed bottom-0 start-0 p-3">
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
        $('.btn-visualizar').on('click', actionViewTask)
        $('.btn-modificar').on('click', actionEditTask)
        $('.btn-eliminar').on('click', actionDeleteTask)
      }
    })

    $('.btn-crear-tarea').on('click', function() {
      $('#form-crear-tarea').submit()
    })

    @if ($errors->store->any())
      var myModal = new bootstrap.Modal(document.getElementById('modal-crear-tarea'))
      myModal.show()
    @elseif ($errors->update->any())
      var myModal = new bootstrap.Modal(document.getElementById('modal-editar-tarea'))
      myModal.show()
    @endif

    function actionViewTask (e) {
      e.preventDefault()
      var tarea = $(this).closest('tr').data('tarea-id')
      $.ajax({
        url: "{{ route('tasks.show', ':tarea') }}".replace(':tarea', tarea),
        type: 'GET',
        dataType: 'json',
        success: function(r) {
          $('#modal-tarea-detalles .modal-title').html(`Detalles de tarea: ${r.task}`)
          $('#modal-tarea-detalles .modal-body').html(r.html)
          var myModal = new bootstrap.Modal(document.getElementById('modal-tarea-detalles'))
          myModal.show()
        },
        error: function(xhr, status) {
          alert('Un error inesperado ha ocurrido.'); // replace with modal
          console.log({xhr:xhr, status:status})
        },
      })
    }

    function actionEditTask (e) {
      e.preventDefault()
      var tarea = $(this).closest('tr').data('tarea-id')
      $.ajax({
        url: "{{ route('tasks.edit', ':tarea') }}".replace(':tarea', tarea),
        type: 'GET',
        dataType: 'json',
        success: function(tarea) {
          $.each( tarea , function(key, value) {
            $(`#edit_${key}`).val(value)
          })
          $('#modal-editar-tarea .modal-title').html(`Editar detalles de usuario: ${tarea.nombre}`)
          var myModal = new bootstrap.Modal(document.getElementById('modal-editar-tarea'))
          myModal.show()
        },
        error: function(xhr, status) {
          alert('Un error inesperado ha ocurrido.'); // replace with modal
          console.log({xhr:xhr, status:status})
        },
      })
    }

    $('.btn-editar-tarea').on('click', function (e) {
      e.preventDefault()
      var tarea = $('#edit_id').val()
      var action = "{{ route('tasks.update', ':tarea') }}".replace(':tarea', tarea)
      $('#form-editar-tarea').attr('action', action).submit()
    })

    function actionDeleteTask (e) {
      e.preventDefault()
      var task_id = $(this).closest('tr').data('tarea-id')
      var task_name = $(this).closest('tr').find('td:nth-child(2)').text()
      var action = '{{ route('tasks.delete', ':tarea') }}'.replace(':tarea', task_id)
      var myModal = new bootstrap.Modal(document.getElementById('modal-eliminar-tarea'))
      
      $('#delete_company').val(task_id)
      $('#form-eliminar-tarea').attr('action', action)
      $('#modal-eliminar-tarea .modal-title').html(`Eliminar usuario: <strong>${task_name}</strong>`)
      $('#modal-eliminar-tarea .modal-body').html(`Está por eliminar de manera irreversible al usuario <em>"${task_name}"</em> junto con todos sus datos, ¿Desea continuar?`)
      
      myModal.show()
    }

    $('.btn-eliminar-tarea').on('click', function (e) {
      e.preventDefault()
      $('#form-eliminar-tarea').submit()
    })

@if (session('status'))
    const toastLiveExample = document.getElementById('liveToast')
    const toast = new bootstrap.Toast(toastLiveExample)
    $('#liveToast .toast-body').html(`{{ session('status') }}`.replace(/\*(.*?)\*/g, '<em>$1</em>'))
    toast.show()
@endif

  })
</script>
@endsection