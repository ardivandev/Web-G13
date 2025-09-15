<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="{{asset("images/logo.png")}}" type="image/x-icon">
    <title>G13</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            overflow: hidden;
            background-color: black;
        }

        #intro-video {
            width: 100vw;
            height: 100vh;
            object-fit: cover;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 5;
        }

        #skip-button {
            position: fixed;
            z-index: 20;
            padding: 12px 24px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            background-color: rgba(255, 255, 255, 0.85);
            top: 20px;
            right: 20px;
        }

        #skip-button:hover {
            background-color: rgba(255, 255, 255, 1);
        }
    </style>
</head>
<body>

    <!-- Tombol Lewati -->
    <button id="skip-button">Lewati Intro</button>

    <!-- Video Intro -->
    <video id="intro-video" autoplay muted playsinline>
      <source src="{{ asset('videos/intro.mp4') }}" type="video/mp4">
      Browser Anda tidak mendukung tag video.
    </video>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
    const video = document.getElementById('intro-video');
    const skipBtn = document.getElementById('skip-button');
    let alreadyRedirected = false;

    function redirectToLogin() {
        if (alreadyRedirected) return;
        alreadyRedirected = true;
        window.location.href = "{{ route('login') }}";
    }

    skipBtn.addEventListener('click', redirectToLogin);
    video.addEventListener('ended', redirectToLogin);

    // Fallback redirect
    video.addEventListener('loadedmetadata', () => {
        let duration = isNaN(video.duration) ? 15 : video.duration;
        setTimeout(() => {
            redirectToLogin();
        }, duration * 1000);
    });
});
    </script>

</body>
</html>
