@extends('layouts.pengguna.app')

@section('content')
<style>
    /* ===== HEADER ===== */
    .header-tentang {
        background: linear-gradient(135deg, #565477, #474163);
        color: white;
        padding: 100px 20px;
        text-align: center;
        border-bottom-left-radius: 50px;
        border-bottom-right-radius: 50px;
    }
    .header-tentang h1 {
        font-size: 3rem;
        font-weight: 800;
        letter-spacing: 1px;
    }
    .header-tentang p {
        font-size: 1.2rem;
        margin-top: 15px;
        opacity: 0.9;
    }

    /* ===== SECTION ===== */
    .section {
        padding: 70px 20px;
    }
    .section h2 {
        font-size: 2.2rem;
        color: #565477;
        font-weight: 700;
        margin-bottom: 40px;
        text-align: center;
        position: relative;
    }
    .section h2::after {
        content: "";
        display: block;
        width: 60px;
        height: 4px;
        background: #565477;
        margin: 12px auto 0 auto;
        border-radius: 2px;
    }

    /* ===== VISI MISI ===== */
    .visi-misi {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 25px;
    }
    .visi-misi .card {
        flex: 1 1 300px;
        max-width: 350px;
        border-radius: 1rem;
        border: none;
        padding: 25px 20px;
        text-align: center;
        box-shadow: 0 6px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .visi-misi .card:hover {
        transform: translateY(-8px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
    .visi-misi .card h3 {
        color: #474163;
        font-size: 1.5rem;
        margin-bottom: 15px;
        font-weight: 600;
    }
    .visi-misi .card p {
        color: #6c757d;
        line-height: 1.6;
    }

    /* ===== TIM KAMI ===== */
    .tim {
        display: grid;
        grid-template-columns: repeat(3, 1fr); /* selalu 3 kolom */
        gap: 25px;
        justify-items: center;
    }

    .tim .card {
        width: 100%;
        max-width: 260px;
        border-radius: 1rem;
        border: none;
        overflow: hidden;
        text-align: center;
        box-shadow: 0 6px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .tim .card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 22px rgba(0,0,0,0.15);
    }
    .tim img {
        width: 100%;
        height: auto;
        max-height: 500px;
        object-fit: cover;
        transition: transform 0.6s ease, opacity 0.8s ease;
        opacity: 0;
        transform: scale(1.1);
    }
    .tim img.visible {
        opacity: 1;
        transform: scale(1);
    }
    .tim .card:hover img {
        transform: scale(1.05);
    }
    .tim h4 {
        margin: 15px 0 5px 0;
        color: #565477;
        font-weight: 600;
        text-decoration: none;
    }
    .tim p {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 15px;
        text-decoration: none;
    }
    .tim .card a {
        text-decoration: none;  /* hilangkan underline */
        color: inherit;         /* ikut warna teks aslinya */
        display: block;         /* biar link-nya blok penuh */
    }
    .tim .card a h4,
    .tim .card a p {
        text-decoration: none;
        color: inherit;
    }
    /* ===== HOVER TIM KAMI ===== */
    .tim .card {
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .tim .card:hover {
        transform: translateY(-10px) scale(1.03);
        box-shadow: 0 15px 25px rgba(0,0,0,0.2);
    }

    /* Efek gambar saat hover */
    .tim .card:hover img {
        transform: scale(1.1);
        filter: brightness(1.1);
    }

    /* Efek teks saat hover */
    .tim .card:hover h4 {
        color: #2c2a45;  /* lebih gelap dari #565477 */
    }

    .tim .card:hover p {
        color: #474163;  /* ganti jadi lebih kontras */
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1024px) {
        .header-tentang h1 {
            font-size: 2.5rem;
        }
    }
    @media (max-width: 768px) {
        .visi-misi, .tim {
            flex-direction: column;
            align-items: center;
        }
    }
    @media (max-width: 480px) {
        .header-tentang {
            padding: 60px 15px;
        }
        .section {
            padding: 40px 15px;
        }
        .visi-misi .card, .tim .card {
            max-width: 100%;
        }
        .tim img {
            max-height: 300px;
        }
        .kontak p {
            font-size: 1rem;
        }
    }

    /* ===== ANIMASI TAMBAHAN ===== */
    .animate-fade-up {
        opacity: 0;
        transform: translateY(50px);
        transition: all 0.8s ease-out;
    }
    .animate-zoom-in {
        opacity: 0;
        transform: scale(0.9);
        transition: all 0.8s ease-out;
    }
    .animate-visible {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
</style>

<div class="header-tentang animate-fade-up">
    <h1>G13</h1>
    <p>Meminjam barang lebih mudah dengan G13</p>
</div>

<div class="section animate-fade-up">
    <h2>Visi & Misi</h2>
    <div class="visi-misi">
        <div class="card animate-zoom-in">
            <h3>Visi</h3>
            <p>Mewujudkan sistem pengelolaan inventaris sekolah yang modern, cepat, dan terintegrasi melalui layanan online untuk mendukung kegiatan belajar mengajar yang efektif.</p>
        </div>
        <div class="card animate-zoom-in">
            <h3>Misi</h3>
            <p>Menyediakan platform online yang memudahkan peminjaman dan pengembalian barang secara praktis dan transparan.
            Mencatat dan mengelola data inventaris dengan rapi, akurat, dan mudah diakses.
            Mendukung efisiensi penggunaan sarana dan prasarana sekolah melalui pemanfaatan teknologi.
            Meningkatkan tanggung jawab pengguna dalam memanfaatkan barang sekolah dengan baik.</p>
        </div>
    </div>
</div>

<div class="section animate-fade-up" style="background:#f8f9fa;">
    <h2>Tim Kami</h2>
    <div class="tim">
        <div class="card animate-zoom-in">
            <a href="https://www.instagram.com/ghanifrasbasel/">
                <img src="{{ asset('images/pembuat/0076859975.png') }}" alt="Tim 2">
                <h4>Ghanifra Sobia Basel</h4>
                <p>Web Developer</p>
            </a>
        </div>
        <div class="card animate-zoom-in">
            <a href="https://www.instagram.com/ardivannrr/">
                <img src="{{ asset('images/pembuat/0076539726.png') }}" alt="Tim 1">
                <h4>Ardivan Nur Raihan Rahman</h4>
                <p>Web Developer</p>
            </a>
        </div>
        <div class="card animate-zoom-in">
            <a href="https://www.instagram.com/ardivannrr/">
                <img src="{{ asset('images/pembuat/0075816553.png') }}" alt="Tim 3">
                <h4>Zaidan Faaris Abidi</h4>
                <p>Web Developer</p>
            </a>
        </div>
    </div>
</div>

<script>
    // Observer untuk teks dan card
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-visible');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.animate-fade-up, .animate-zoom-in').forEach(el => {
        observer.observe(el);
    });

    // Observer khusus gambar tim
    const imgObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.2 });

    document.querySelectorAll('.tim img').forEach(img => {
        imgObserver.observe(img);
    });
</script>
@endsection
