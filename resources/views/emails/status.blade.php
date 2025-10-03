@extends('emails._layout')

@section('email-title', 'System Status Update')

@section('content')
    <p>Halo <strong>{{ $profileName }}</strong>,</p>
    
    {{-- <p>This is an update regarding your recent activity or system status:</p> --}}
    <p>Berikut update terbaru pengajuan status PAUD  {{ $paudName }}:</p>
    
    <div class="status-card">
        @php
            // Determine status color based on status type
            // $statusClass = match(strtolower($status)) {
            //     'success', 'completed' => 'status-success',
            //     'warning', 'pending' => 'status-warning',
            //     'error', 'failed' => 'status-error',
            //     default => 'status-info'
            // };
            
            $statusClass = match(strtolower($status)) {
                'success', 'verified', 'approve', 'Izin terbit' => 'status-success',
                'revision', 'pending' => 'status-warning',
                default => 'status-info'
            };
            
            $statusText = match(strtolower($status)) {
                'success', 'completed' => 'status-success',
                'verified' => 'Terverifikasi',
                'revision' => 'Revisi Data',
                'approve' => 'Disetujui',
                'Izin terbit' => 'Izin terbit',
                default => 'status-info'
            };
        @endphp
        
        <span class="status-indicator {{ $statusClass }}">
            {{ ucfirst($status) }}
        </span>
        
        <h3>Status Details</h3>
        <p>Your request has been processed with the status: <strong>{{ $status }}</strong></p>
        
        @if(strtolower($status) === 'success')
            <p>✅ Your operation was completed successfully. You can now proceed to the next step.</p>
        @elseif(strtolower($status) === 'pending')
            <p>⏳ Your request is being processed. Please check back later for updates.</p>
        @elseif(strtolower($status) === 'error')
            <p>❌ There was an issue processing your request. Please try again or contact support.</p>
        @else
            <p>ℹ️ Here's the latest update on your request.</p>
        @endif
    </div>
    
    {{-- <p>Click the button below to access your account or visit our website for more information:</p> --}}
    <p>Klik tombol dibawah untuk melihat status pengajuan anda lewat SIPENDI:</p>
    
    <div style="text-align: center;">
        <a href="{{ $actionUrl }}" class="btn-primary" target="_blank">
            {{ $actionText }}
        </a>
    </div>
    
    {{-- <p>If the button above doesn't work, copy and paste this link into your browser:</p> --}}
    <p>Jika tombol diatas tidak berfungsi, salin tautan dibawah ini ko kolom alamat di peramban anda:</p>
    <p style="word-break: break-all; color: #667eea;">
        <a href="{{ $actionUrl }}" style="color: #667eea; text-decoration: underline;">{{ $actionUrl }}</a>
    </p>
    
    <p>Salam hormat,<br>Tim{{ config('app.name') }}</p>
@endsection