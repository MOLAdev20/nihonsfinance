@extends('layouts.admin')

@section('title', 'Invoice Baru')
@section('page_header', 'Invoice Baru')
@section('page_subheader', 'Susun invoice baru dengan format dokumen invoice.')

@section('content')
    @include('admin.invoice._invoice-form-layout', [
        'formAction' => route('admin.invoice.store'),
        'formMethod' => 'POST',
        'submitLabel' => 'Submit Invoice',
        'invoice' => null,
    ])
@endsection

@section('modals')
    @include('admin.invoice._customer-modal')
@endsection
