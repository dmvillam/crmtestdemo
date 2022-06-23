@extends('layouts.app')

@section('title', "Reportes")

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
  .descripcion-larga {
    max-height: 150px;
    overflow: auto;
  }
  @media(max-width: 575px) {
    .input-daterange, .btn-group, .btn-generar-reporte, .btn-toolbar {
      width: 100%;
    }
    .btn-group > select {
      width: 100%;
    }
  }
  @media(min-width: 992px) {
    .input-daterange {
      width: 200px;
    }
  }
</style>
@endsection

@section('content')
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Reportes</h1>
        <div class="btn-toolbar mb-2 mb-md-0">

          <input type="text" name="daterange" class="btn btn-sm btn-outline-secondary input-daterange me-2 mb-2">

          <div class="btn-group me-2 mb-2">
            <select class="btn btn-sm btn-outline-secondary select-clientes">
              <option value="0">Seleccione al cliente</option>
              @foreach($clientes as $cliente)
                <option value="{{$cliente->id}}">{{$cliente->nombre}}</option>
              @endforeach
            </select>
            <select class="btn btn-sm btn-outline-secondary select-estados">
              <option value="0">Seleccione el estado</option>
            </select>
          </div>

          <button type="button" class="btn btn-sm btn-outline-secondary btn-generar-reporte me-2 mb-2">
            Generar reporte
          </button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-sm">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Nombre</th>
              <th scope="col">Descripción larga</th>
              <th scope="col">Fecha programada</th>
              <th scope="col">Estado de la operación</th>
            </tr>
          </thead>
          <tbody>
            @forelse($notificaciones as $notificacion)
            <tr data-notificacion-id="{{ $notificacion->id }}">
              <td>{{ $notificacion->id }}</td>
              <td>{{ $notificacion->tarea->cliente->nombre }}</td>
              <td><div class="descripcion-larga">{!! $notificacion->plantilla->descripcion_larga !!}</div></td>
              <td>{{$notificacion->next_activity}}</td>
              <td>?</td>
            </tr>
            @empty
            <tr><td colspan="5"><em>No hay notificaciones para mostrar por el momento...</em></td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

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
    $('input[name="daterange"]').daterangepicker()

    if ($('table tbody td').length > 1) {
      $('table').DataTable({
        responsive: true,
        buttons: [
          'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        dom: 'Bfrtip',
          "columnDefs": [
            { "width": "45%", "targets": 2 },
            { "targets": 3, render: $.fn.dataTable.render.moment( 'YYYY-MM-DD HH:mm:ss', 'DD/MM/YYYY HH:mm:ss' ) }
          ],
      })
    }

    $('.btn-generar-reporte').on('click', function() {
      var range = $('input[name=daterange]').val().replaceAll('/','-')
      var cliente = $('.select-clientes').val()
      var estado = $('.select-estados').val()

      window.location.href = `{{route('reports.index')}}/${cliente}/${estado}/${range}`
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