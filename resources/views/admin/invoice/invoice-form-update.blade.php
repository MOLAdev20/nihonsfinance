@extends('layouts.admin')

@section('title', 'Edit Invoice')
@section('page_header', 'Edit Invoice')
@section('page_subheader', 'Perbarui invoice dengan layout yang sama seperti form input.')

@section('content')
    @include('admin.invoice._invoice-form-layout', [
        'formAction' => route('admin.invoice.update', $invoice),
        'formMethod' => 'PUT',
        'submitLabel' => 'Perbarui Invoice',
        'invoice' => $invoice,
    ])
@endsection

@section('modals')
    @include('admin.invoice._customer-modal')
@endsection
