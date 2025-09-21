<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Petugas | G13</title>
    <link rel="shortcut icon" href="{{asset('images/logo.png')}}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="{{ asset('admin/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    <style>
        #chartPeminjaman,
        #lineChart,
        #stokChart {
            max-height: 250px;
        }

        /* Loading animation */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        /* Status badge styling */
        .badge {
            font-size: 0.75rem;
        }

        /* Alert styling */
        .alert {
            border-left: 4px solid;
            margin-bottom: 1rem;
        }

        .alert-success {
            border-left-color: #28a745;
        }

        .alert-danger {
            border-left-color: #dc3545;
        }
    </style>
    @stack('styles')
</head>
<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @include('layouts.petugas.sidebar')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">

                <!-- Topbar -->
                @include('layouts.petugas.topbar')
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid mt-4">
                    @yield('content')
                </div>
                <!-- End Page Content -->

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('admin/js/sb-admin-2.min.js') }}"></script>

    <script>
    $(document).ready(function() {
        // Setup Pusher dengan konfigurasi yang lebih robust
        Pusher.logToConsole = {{ config('app.debug') ? 'true' : 'false' }};

        var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            forceTLS: true,
            enabledTransports: ['ws', 'wss', 'xhr_polling', 'xhr_streaming'],
            activityTimeout: 30000,
            pongTimeout: 10000
        });

        var channel = pusher.subscribe('gudang13');

        // Debug connection
        pusher.connection.bind('connecting', function() {
            console.log('Petugas: Connecting to Pusher...');
        });

        pusher.connection.bind('connected', function() {
            console.log('Petugas: Connected to Pusher successfully');
        });

        pusher.connection.bind('unavailable', function() {
            console.error('Petugas: Connection unavailable');
        });

        pusher.connection.bind('failed', function() {
            console.error('Petugas: Connection failed');
        });

        // Debug subscription
        channel.bind('pusher:subscription_succeeded', function() {
            console.log('Petugas: Successfully subscribed to gudang13 channel');
        });

        channel.bind('pusher:subscription_error', function(err) {
            console.error('Petugas: Subscription error:', err);
        });

        // Listen untuk perubahan status gudang
        channel.bind('status.gudang.updated', function(data) {
            console.log("=== PETUGAS EVENT RECEIVED ===");
            console.log("Status:", data.status);
            console.log("Timestamp:", data.timestamp);
            console.log("Message:", data.message);

            // Update button menggunakan fungsi yang sudah ada di topbar
            if (typeof updateGudangButton === 'function') {
                updateGudangButton(data.status);
                console.log('Button updated for petugas');
            } else {
                console.error('updateGudangButton function not found');
            }

            // Show notification
            if (typeof showAlert === 'function') {
                showAlert(data.message || 'Status gudang berubah menjadi: ' + data.status.toUpperCase(), 'success');
            } else {
                // Fallback alert
                showFallbackAlert(data.message || 'Status gudang berubah menjadi: ' + data.status.toUpperCase(), 'info');
            }
        });

        // Fallback alert function jika showAlert tidak tersedia
        function showFallbackAlert(message, type) {
            $('.alert').remove();
            var alertClass = 'alert-' + (type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info');
            var iconClass = type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-triangle' : 'fa-info-circle';

            var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                           '<i class="fas ' + iconClass + ' mr-2"></i>' + message +
                           '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                           '<span aria-hidden="true">&times;</span></button></div>';
            $('.container-fluid').prepend(alertHtml);

            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);
        }

        // Periodic connection health check
        setInterval(function() {
            if (pusher.connection.state === 'disconnected') {
                console.warn('Pusher connection lost, attempting to reconnect...');
                pusher.connect();
            }
        }, 30000);

        // Handle page visibility change untuk reconnect jika perlu
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden && pusher.connection.state === 'disconnected') {
                console.log('Page became visible, reconnecting Pusher...');
                pusher.connect();
            }
        });
    });
    </script>

    @stack('scripts')
</body>
</html>
