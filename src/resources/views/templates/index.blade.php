@extends('layouts.app')

@section('title', "Plantillas para notificaciones")

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
@include('templates.modals.create')
@include('templates.modals.details')
@include('templates.modals.edit')
@include('templates.modals.delete')
@endsection

@section('content')
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Plantillas para notificaciones</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modal-crear-plantilla">
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
              <th scope="col">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($plantillas as $plantilla)
            <tr data-plantilla-id="{{ $plantilla->id }}">
              <td>{{ $plantilla->id }}</td>
              <td>{{ $plantilla->nombre }}</td>
              <td>
                <a href="#" class="link-secondary btn-visualizar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" title="Visualizar"><i class="fa-solid fa-eye"></i></a>
                
                <a href="#" class="link-secondary btn-modificar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" title="Modificar"><i class="fa-solid fa-pencil"></i></a>
                
                <a href="#" class="link-secondary btn-eliminar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" title="Eliminar"><i class="fa-solid fa-trash-can"></i></a>
              </td>
            </tr>
            @empty
            <tr><td colspan="3"><em>No hay plantillas para mostrar por el momento... Crea la primera</em></td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <form id="form-eliminar-plantilla" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="DELETE"/>
        <input type="hidden" name="delete_template" id="delete_template" value="">
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
      responsive: {
        details: {
          renderer: $.fn.dataTable.Responsive.renderer.listHiddenNodes()
        }
      },
      buttons: [
        'copy', 'csv', 'excel', 'pdf', 'print'
      ],
      dom: 'lfrtBip',
      "drawCallback": function( settings ) {
        $('.btn-visualizar').on('click', actionViewTemplate)
        $('.btn-modificar').on('click', actionEditTemplate)
        $('.btn-eliminar').on('click', actionDeleteTemplate)
      }
    })

    $('.btn-crear-plantilla').on('click', function() {
      $('#form-crear-plantilla').submit()
    })

    @if ($errors->store->any())
      var myModal = new bootstrap.Modal(document.getElementById('modal-crear-plantilla'))
      myModal.show()
    @elseif ($errors->update->any())
      var myModal = new bootstrap.Modal(document.getElementById('modal-editar-plantilla'))
      myModal.show()
    @endif

    function actionViewTemplate (e) {
      e.preventDefault()
      var plantilla = $(this).closest('tr').hasClass('child') ?
        $(this).closest('tr').prev().data('plantilla-id') :
        $(this).closest('tr').data('plantilla-id')
      $.ajax({
        url: "{{ route('templates.show', ':plantilla') }}".replace(':plantilla', plantilla),
        type: 'GET',
        dataType: 'json',
        success: function(r) {
          $('#modal-plantilla-detalles .modal-title').html(`Detalles de plantilla: ${r.template}`)
          $('#modal-plantilla-detalles .modal-body').html(r.html)
          var myModal = new bootstrap.Modal(document.getElementById('modal-plantilla-detalles'))
          myModal.show()
        },
        error: function(xhr, status) {
          alert('Un error inesperado ha ocurrido.'); // replace with modal
          console.log({xhr:xhr, status:status})
        },
      })
    }

    function actionEditTemplate (e) {
      e.preventDefault()
      var plantilla = $(this).closest('tr').hasClass('child') ?
        $(this).closest('tr').prev().data('plantilla-id') :
        $(this).closest('tr').data('plantilla-id')
      $.ajax({
        url: "{{ route('templates.edit', ':plantilla') }}".replace(':plantilla', plantilla),
        type: 'GET',
        dataType: 'json',
        success: function(plantilla) {
          $.each( plantilla , function(key, value) {
            $(`#edit_${key}`).val(value)
          })
          $('#modal-editar-plantilla .modal-title').html(`Editar detalles de plantilla: ${plantilla.nombre}`)
          var myModal = new bootstrap.Modal(document.getElementById('modal-user-edit'))
          myModal.show()
        },
        error: function(xhr, status) {
          alert('Un error inesperado ha ocurrido.'); // replace with modal
          console.log({xhr:xhr, status:status})
        },
      })
    }

    $('.btn-editar-plantilla').on('click', function (e) {
      e.preventDefault()
      var plantilla = $('#edit_id').val()
      var action = "{{ route('templates.update', ':plantilla') }}".replace(':plantilla', plantilla)
      $('#form-editar-plantilla').attr('action', action).submit()
    })

    function actionDeleteTemplate (e) {
      e.preventDefault()
      var plantilla = $(this).closest('tr').hasClass('child') ?
        $(this).closest('tr').prev().data('plantilla-id') :
        $(this).closest('tr').data('plantilla-id')
      var plantilla_nombre = $(this).closest('tr').find('td:nth-child(2)').text()
      var action = '{{ route('templates.delete', ':plantilla') }}'.replace(':plantilla', plantilla)
      var myModal = new bootstrap.Modal(document.getElementById('modal-eliminar-plantilla'))
      
      $('#delete_task').val(plantilla)
      $('#form-eliminar-plantilla').attr('action', action)
      $('#modal-eliminar-plantilla .modal-title').html(`Eliminar plantilla: <strong>${plantilla_nombre}</strong>`)
      $('#modal-eliminar-plantilla .modal-body').html(`Está por eliminar de manera irreversible la plantilla <em>"${plantilla_nombre}"</em> junto con todos sus datos, ¿Desea continuar?`)
      
      myModal.show()
    }

    $('.btn-eliminar-plantilla').on('click', function (e) {
      e.preventDefault()
      $('#form-eliminar-plantilla').submit()
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