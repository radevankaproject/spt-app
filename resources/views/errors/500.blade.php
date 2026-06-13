@extends('errors.layout')

@section('title', 'Kesalahan Server Internal')
@section('code', '500')
@section('message', $exception->getMessage() ?: 'Waduh! 🛠️ Server kami sedang mengalami masalah. Silakan coba lagi beberapa saat atau hubungi administrator.')
