@extends('layouts.app')

@section('title', "Empresas")

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
@include('companies.modals.create')
@include('companies.modals.details')
@include('companies.modals.edit')
@include('companies.modals.delete')
@endsection

@section('content')
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Empresas</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <!--
          <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
          </div>
          -->
          <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modal-crear-empresa">
            Crear empresa
          </button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-sm">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Nombre</th>
              <th scope="col">Contacto</th>
              <th scope="col">Teléfono</th>
              <th scope="col">Email</th>
              <th scope="col">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($empresas as $empresa)
            <tr data-company-id="{{ $empresa->id }}">
              <td>{{ $empresa->id }}</td>
              <td>{{ $empresa->nombre }}</td>
              <td>{{ '-' }}</td>
              <td>{{ $empresa->telefono }}</td>
              <td>{{ $empresa->email }}</td>
              <td>
                <a href="#" class="link-secondary btn-visualizar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" title="Visualizar"><i class="fa-solid fa-eye"></i></a>
                
                <a href="#" class="link-secondary btn-modificar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" title="Modificar"><i class="fa-solid fa-pencil"></i></a>
                
                <a href="#" class="link-secondary btn-eliminar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" title="Eliminar"><i class="fa-solid fa-trash-can"></i></a>
              </td>
            </tr>
            @empty
            <tr><td colspan="6"><em>No hay empresas para mostrar por el momento...</em></td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <form id="form-eliminar-empresa" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="DELETE"/>
        <input type="hidden" name="delete_company" id="delete_company" value="">
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
        $('.btn-visualizar').on('click', actionViewCompany)
        $('.btn-modificar').on('click', actionEditCompany)
        $('.btn-eliminar').on('click', actionDeleteCompany)
      }
    })

    $('.btn-crear-empresa').on('click', function() {
      $('#form-crear-empresa').submit()
    })

    @if ($errors->store->any())
      var myModal = new bootstrap.Modal(document.getElementById('modal-crear-empresa'))
      myModal.show()
    @elseif ($errors->update->any())
      var myModal = new bootstrap.Modal(document.getElementById('modal-editar-empresa'))
      myModal.show()
    @endif

    function actionViewCompany (e) {
      e.preventDefault()
      var company = $(this).closest('tr').data('company-id')
      $.ajax({
        url: "{{ route('companies.show', ':company') }}".replace(':company', company),
        type: 'GET',
        dataType: 'json',
        success: function(r) {
          $('#modal-empresa-detalles .modal-title').html(`Detalles de empresa: ${r.company}`)
          $('#modal-empresa-detalles .modal-body').html(r.html)
          var myModal = new bootstrap.Modal(document.getElementById('modal-empresa-detalles'))
          myModal.show()
        },
        error: function(xhr, status) {
          alert('Un error inesperado ha ocurrido.'); // replace with modal
          console.log({xhr:xhr, status:status})
        },
      })
    }

    function actionEditCompany (e) {
      e.preventDefault()
      var company = $(this).closest('tr').data('company-id')
      $.ajax({
        url: "{{ route('companies.edit', ':company') }}".replace(':company', company),
        type: 'GET',
        dataType: 'json',
        success: function(company) {
          $.each( company , function(key, value) {
            $(`#edit_${key}`).val(value)
          })
          $('#modal-editar-empresa .modal-title').html(`Editar detalles de usuario: ${company.nombre}`)
          var myModal = new bootstrap.Modal(document.getElementById('modal-editar-empresa'))
          myModal.show()
        },
        error: function(xhr, status) {
          alert('Un error inesperado ha ocurrido.'); // replace with modal
          console.log({xhr:xhr, status:status})
        },
      })
    }

    $('.btn-editar-empresa').on('click', function (e) {
      e.preventDefault()
      var company = $('#edit_id').val()
      var action = "{{ route('companies.update', ':company') }}".replace(':company', company)
      $('#form-editar-empresa').attr('action', action).submit()
    })

    function actionDeleteCompany (e) {
      e.preventDefault()
      var company_id = $(this).closest('tr').data('company-id')
      var company_name = $(this).closest('tr').find('td:nth-child(2)').text()
      var action = '{{ route('companies.delete', ':company') }}'.replace(':company', company_id)
      var myModal = new bootstrap.Modal(document.getElementById('modal-eliminar-empresa'))
      
      $('#delete_company').val(company_id)
      $('#form-eliminar-empresa').attr('action', action)
      $('#modal-eliminar-empresa .modal-title').html(`Eliminar usuario: <strong>${company_name}</strong>`)
      $('#modal-eliminar-empresa .modal-body').html(`Está por eliminar de manera irreversible al usuario <em>"${company_name}"</em> junto con todos sus datos, ¿Desea continuar?`)
      
      myModal.show()
    }

    $('.btn-eliminar-empresa').on('click', function (e) {
      e.preventDefault()
      $('#form-eliminar-empresa').submit()
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