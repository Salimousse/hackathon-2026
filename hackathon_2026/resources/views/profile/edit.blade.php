@extends('layouts.base')

@section('title', 'Profile')

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Mon Profil</h1>
        <p class="text-gray-600">Gérez vos informations personnelles</p>
    </div>

    <div class="space-y-6">
        <!-- Update Profile Information -->
        <div class="bg-white p-6 rounded-lg shadow">
            @include('profile.partials.update-profile-information-form')
        </div>

        <!-- Update Password -->
        <div class="bg-white p-6 rounded-lg shadow">
            @include('profile.partials.update-password-form')
        </div>

        <!-- Delete Account -->
        <div class="bg-white p-6 rounded-lg shadow">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
