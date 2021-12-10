@extends('layouts.app')

@section('content')
<div class="d-flex container" id="wrapper">

    <!-- Sidebar -->
    @if (session('selected_company_name') )
    <div class="bg-light border-right" id="sidebar-wrapper">
      <div class="sidebar-heading">Menu </div>
      <div class="list-group list-group-flush">
        <a class="list-group-item list-group-item-action bg-light" data-toggle="modal" data-target="#EditCompany">Редактировать </a>
        <a href="{{ route('tusers', session('selected_company_id')) }}" class="list-group-item list-group-item-action bg-light">Users</a>
        <a href="{{ route('messages') }}" class="list-group-item list-group-item-action bg-light">Сообщение</a>
          <a href="{{ route('documentation') }}" class="list-group-item list-group-item-action bg-light">Документация</a>

      </div>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper">





      <div class="container-fluid">
          @include('tusers')
          @include('tmessages')
      </div>
    </div>
        @endif


</div>



@endsection
