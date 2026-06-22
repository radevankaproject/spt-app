@isset($pageConfigs)
  {!! App\Helpers\Helpers::updatePageConfig($pageConfigs) !!}
@endisset

@php
  $configData = App\Helpers\Helpers::appClasses();
  $customizerHidden = $customizerHidden ?? '';
@endphp

@extends('layouts.commonMaster')

@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('layoutContent')
  @include('layouts.partials._alerts')
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-6">
      @yield('content')
    </div>
  </div>
@endsection
