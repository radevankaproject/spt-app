@extends('errors.layout')

@section('title', 'Akses Ditolak')
@section('code', '403')
@section('message', $exception->getMessage() ?: 'Maaf! 🚫 Anda tidak memiliki hak akses atau izin untuk melihat halaman ini.')
