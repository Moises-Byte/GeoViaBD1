@extends('layouts.app')

@php
    $title = 'Mi perfil';
@endphp

@section('actions')
    <a href="{{ route('dashboard') }}"
       class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
        Volver al panel
    </a>
@endsection

@section('slot')
    <div class="mx-auto w-full max-w-3xl space-y-8">
        <section class="border-b border-slate-200 pb-8">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </section>

        <section class="border-b border-slate-200 pb-8">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </section>

        <section>
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </section>
    </div>
@endsection
