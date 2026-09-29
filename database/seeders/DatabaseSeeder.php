<?php

namespace Database\Seeders;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed data demo "Secure Academic Portal ITS".
     *
     * Seeder ini idempotent (aman dijalankan berulang kali) karena memakai
     * `updateOrCreate` berdasarkan kolom unik.
     */
    public function run(): void
    {
        // --- Akun demo untuk pengujian otentikasi & otorisasi -------------
        $this->createUser('Dr. Ir. Budi Santoso, M.Kom.', 'dosen@its.ac.id', User::ROLE_DOSEN);
        $this->createUser('Rani Putri Anggraini, S.Kom.', 'asdos@its.ac.id', User::ROLE_ASDOS);
        $mahasiswa1 = $this->createUser(
            'Adriel Mahira Dharma (5025241097)',
            'mhs1@its.ac.id',
            User::ROLE_MAHASISWA,
        );
        $mahasiswa2 = $this->createUser(
            'Nabila Ayu Kirana (5025241099)',
            'mhs2@its.ac.id',
            User::ROLE_MAHASISWA,
        );

        // --- Contoh proposal milik mhs1 (untuk uji edit & delete) ---------
        $proposals = [
            [
                'judul' => 'Deteksi Dini Penyakit Daun Padi Menggunakan CNN',
                'deskripsi' => 'Proyek ini membangun model convolutional neural network '
                    .'untuk mengklasifikasikan lima jenis penyakit daun padi dari citra '
                    .'daun. Dataset diambil dari lapangan dan Kaggle, lalu diperkaya '
                    .'dengan augmentasi rotasi serta pencahayaan. Luaran berupa model '
                    .'terlatih dan API prediksi sederhana.',
                'status' => Proposal::STATUS_PENDING,
                'nilai' => null,
                'catatan_revisi' => null,
            ],
            [
                'judul' => 'Chatbot Layanan Akademik ITS Berbasis Retrieval Augmented Generation',
                'deskripsi' => 'Chatbot menjawab pertanyaan seputar layanan akademik ITS '
                    .'dengan pendekatan RAG: dokumen resmi diubah menjadi embedding, '
                    .'disimpan pada vector database, lalu dipakai sebagai konteks LLM. '
                    .'Fokus evaluasi ada pada akurasi jawaban dan ketahanan halusinasi.',
                'status' => Proposal::STATUS_APPROVED,
                'nilai' => 88,
                'catatan_revisi' => null,
            ],
            [
                'judul' => 'Sistem Rekomendasi Mata Kuliah Pilihan dengan Collaborative Filtering',
                'deskripsi' => 'Memanfaatkan riwayat pengambilan mata kuliah dan nilai '
                    .'mahasiswa untuk merekomendasikan mata kuliah pilihan. Metode yang '
                    .'dibandingkan adalah user-based dan item-based collaborative '
                    .'filtering beserta evaluasi RMSE.',
                'status' => Proposal::STATUS_REJECTED,
                'nilai' => null,
                'catatan_revisi' => 'Ruang lingkup masih terlalu luas dan data historis '
                    .'belum jelas sumbernya. Persempit ke satu departemen, jelaskan izin '
                    .'penggunaan data, dan tambahkan baseline sederhana sebelum revisi '
                    .'diterima.',
            ],
        ];

        foreach ($proposals as $data) {
            Proposal::query()->updateOrCreate(
                ['user_id' => $mahasiswa1->getKey(), 'judul' => $data['judul']],
                $data,
            );
        }

        // --- Contoh proposal milik mhs2 (uji akses 403 antar mahasiswa) ----
        Proposal::query()->updateOrCreate(
            [
                'user_id' => $mahasiswa2->getKey(),
                'judul' => 'Analisis Sentimen Ulasan E-Commerce Berbahasa Indonesia',
            ],
            [
                'deskripsi' => 'Membandingkan performa Naive Bayes, SVM, dan IndoBERT '
                    .'untuk analisis sentimen ulasan produk berbahasa Indonesia, '
                    .'termasuk penanganan slang dan singkatan.',
                'status' => Proposal::STATUS_PENDING,
                'nilai' => null,
                'catatan_revisi' => null,
            ],
        );

        $this->reportSeededAccounts();
    }

    /**
     * Buat atau perbarui akun demo.
     */
    protected function createUser(string $name, string $email, string $role): User
    {
        return User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'role' => $role,
                // Di-hash otomatis oleh cast 'hashed' pada model User.
                'password' => 'password',
                'email_verified_at' => now(),
            ],
        );
    }

    /**
     * Tampilkan daftar akun demo pada console setelah seeding.
     */
    protected function reportSeededAccounts(): void
    {
        $this->command?->info('Akun demo siap dipakai (password: password)');
        $this->command?->table(
            ['Peran', 'Email'],
            [
                ['dosen', 'dosen@its.ac.id'],
                ['asdos', 'asdos@its.ac.id'],
                ['mahasiswa (pemilik proposal)', 'mhs1@its.ac.id'],
                ['mahasiswa lain', 'mhs2@its.ac.id'],
            ],
        );
    }
}
