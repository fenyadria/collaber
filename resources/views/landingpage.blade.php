<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collaber - Temukan Partner Proyek</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    @vite([
    'resources/css/app.css',
    'resources/css/landingpage.css',
    'resources/js/app.js'
    ])
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a href="{{ url('/') }}" class="brand">Collaber</a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarMenu"
                aria-label="Buka navigasi">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">
            <div class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
                <a href="#proyek" class="nav-link">Katalog Proyek</a>
                <a href="#cara-kerja" class="nav-link">Cara Kerja</a>

                <a href="{{ url('/login') }}"
                   class="btn btn-outline-custom btn-sm px-3">
                    Masuk
                </a>

                <a href="{{ url('/register') }}"
                   class="btn btn-primary-custom btn-sm px-3">
                    Daftar
                </a>
            </div>
        </div>
    </div>
</nav>

<main>
    <section class="hero">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">

                    <h1>Temukan Partner Proyek Idealmu</h1>

                    <p>
                        Cari proyek yang sesuai skill-mu, bergabung dengan
                        tim, dan kerjakan bersama dalam satu workspace.
                    </p>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a href="#proyek"
                           class="btn btn-primary-custom btn-sm px-4 py-2">
                            Cari Proyek
                        </a>

                        <a href="{{ url('/register') }}"
                           class="btn btn-outline-custom btn-sm px-4 py-2">
                            Posting Proyek
                        </a>
                    </div>
                </div>

                <div class="col-lg-5 offset-lg-1">
                    <div class="preview-card">
                        <h2 class="fw-bold fs-6 mb-3">
                            Proyek Unggulan
                        </h2>

                        @forelse ($featuredProjects as $project)
                            <a href="{{ url('/login') }}"
                                class="preview-item d-block text-decoration-none text-dark">

                                <strong>{{ $project->name }}</strong>

                                <span>
                                    Kategori: {{ $project->category_name }}
                                </span>

                                <span class="d-block mt-1">
                                    {{ $project->total_applicants }} pendaftar
                                </span>
                            </a>
                        @empty
                            <div class="preview-item">
                                <strong>Belum ada proyek unggulan</strong>
                                <span>Proyek yang disetujui Admin akan muncul di sini.</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container pb-3">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-number">{{ $jumlahProyek }}</div>
                    <div class="stat-label">Proyek tersedia</div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-number">{{ $jumlahMahasiswa }}</div>
                    <div class="stat-label">Mahasiswa bergabung</div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-number">{{ $jumlahTim }}</div>
                    <div class="stat-label">Tim terbentuk</div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="kategori">
        <div class="container">
            <h2 class="section-title">Kategori Populer</h2>

            <div class="d-flex flex-wrap gap-2">
                @forelse ($popularCategories as $category)
                    <a href="#proyek"
                    class="category-chip text-decoration-none">
                        {{ $category->name }}
                        <span class="ms-1">
                            ({{ $category->total_projects }})
                        </span>
                    </a>
                @empty
                    <p class="text-secondary">
                        Belum ada kategori tersedia.
                    </p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="section pt-2" id="proyek">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="section-title mb-0">Proyek Terbaru</h2>
                <a href="{{ url('/register') }}"
                   class="text-decoration-none small">
                    Lihat semua →
                </a>
            </div>
            
            <div class="row g-3">
                @forelse ($projects as $project)
                    <div class="col-md-4">
                        <article class="project-card">

                            <div class="project-body">
                                <span class="project-tag">
                                    {{ $project->category_name }}
                                </span>

                                <h3>{{ $project->name }}</h3>

                                <p class="project-description">
                                    {{ $project->description ?: 'Belum ada deskripsi proyek.' }}
                                </p>

                                <a href="{{ url('/login') }}"
                                class="btn btn-primary-custom btn-sm">
                                    Lihat Detail
                                </a>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="stat-card">
                            <h3 class="fs-6 fw-semibold">Belum ada proyek tersedia</h3>
                            <p class="text-secondary mb-3">
                                Proyek yang telah disetujui Admin akan tampil di sini.
                            </p>
                            <a href="{{ url('/register') }}"
                            class="btn btn-primary-custom btn-sm">
                                Mulai Bergabung
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="section" id="cara-kerja">
        <div class="container">

            <div class="mb-4">
                <h2 class="section-title">Untuk Mahasiswa</h2>

                <div class="row g-3">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="stat-card">
                            <div class="stat-number">1</div>
                            <h3 class="fs-6 fw-semibold mt-2">Daftar</h3>
                            <p class="text-secondary small mb-0">
                                Buat akun dan pilih peran Mahasiswa.
                            </p>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="stat-card">
                            <div class="stat-number">2</div>
                            <h3 class="fs-6 fw-semibold mt-2">Lengkapi profil</h3>
                            <p class="text-secondary small mb-0">
                                Isi NIM, bio, dan skill beserta tingkat kemahiran.
                            </p>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="stat-card">
                            <div class="stat-number">3</div>
                            <h3 class="fs-6 fw-semibold mt-2">Cari dan apply</h3>
                            <p class="text-secondary small mb-0">
                                Temukan proyek yang cocok lalu kirim pesan motivasi.
                            </p>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="stat-card">
                            <div class="stat-number">4</div>
                            <h3 class="fs-6 fw-semibold mt-2">Berkolaborasi</h3>
                            <p class="text-secondary small mb-0">
                                Kerjakan tugas bersama tim dan beri ulasan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="section-title">Untuk Project Owner</h2>

                <div class="row g-3">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="stat-card">
                            <div class="stat-number">1</div>
                            <h3 class="fs-6 fw-semibold mt-2">Daftar</h3>
                            <p class="text-secondary small mb-0">
                                Buat akun dan pilih peran Project Owner.
                            </p>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="stat-card">
                            <div class="stat-number">2</div>
                            <h3 class="fs-6 fw-semibold mt-2">Posting proyek</h3>
                            <p class="text-secondary small mb-0">
                                Isi detail, kuota, tenggat, dan skill yang dibutuhkan.
                            </p>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="stat-card">
                            <div class="stat-number">3</div>
                            <h3 class="fs-6 fw-semibold mt-2">Verifikasi dan seleksi</h3>
                            <p class="text-secondary small mb-0">
                                Admin memverifikasi, lalu kamu menerima atau menolak pelamar.
                            </p>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="stat-card">
                            <div class="stat-number">4</div>
                            <h3 class="fs-6 fw-semibold mt-2">Kelola tim</h3>
                            <p class="text-secondary small mb-0">
                                Bagikan tugas, pantau progres, dan beri penilaian.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="cta d-flex flex-column flex-md-row
                        justify-content-between align-items-md-center gap-3">
                <div>
                    <h2>Siap mulai berkolaborasi?</h2>
                    <p class="mb-0 text-white-50">
                        Temukan tim yang tepat untuk ide hebatmu.
                    </p>
                </div>

                <a href="{{ url('/register') }}"
                   class="btn btn-light btn-sm px-4 py-2">
                    Daftar Sekarang
                </a>
            </div>
        </div>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>