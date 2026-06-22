@extends('errors.layout')

@section('title', __('Terlalu Banyak Permintaan'))
@section('code', '429')
@section('message', __('Mohon maaf, Anda telah mengirimkan terlalu banyak permintaan ke server kami dalam waktu singkat. Silakan tunggu beberapa saat lalu coba kembali.'))
