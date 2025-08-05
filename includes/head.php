<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMBA - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Menambahkan transisi halus untuk sidebar dan konten utama */
        #sidebar,
        .main-content {
            transition: all 0.3s ease-in-out;
        }

        .marquee-container {
            overflow: hidden;
            white-space: nowrap;
        }

        .marquee-text {
            display: inline-block;
            padding-left: 100%;
            animation: marquee-animation 15s linear infinite;
        }

        @keyframes marquee-animation {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-100%);
            }
        }
    </style>
</head>

<body class="bg-slate-50">