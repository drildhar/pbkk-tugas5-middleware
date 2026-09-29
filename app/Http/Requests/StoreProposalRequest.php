<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProposalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Otorisasi sesungguhnya ditangani oleh Policy (`Gate::authorize('create')`)
     * di dalam controller.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Hanya `judul` dan `deskripsi` yang boleh dikirim mahasiswa. Kolom
     * `status`, `nilai`, dan `catatan_revisi` sengaja tidak ada di sini agar
     * tidak bisa dimanipulasi lewat mass assignment.
     *
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'min:5', 'max:150'],
            'deskripsi' => ['required', 'string', 'min:20', 'max:5000'],
        ];
    }

    /**
     * Pesan validasi dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'judul.required' => 'Judul proposal wajib diisi.',
            'judul.string' => 'Judul proposal harus berupa teks.',
            'judul.min' => 'Judul proposal minimal :min karakter.',
            'judul.max' => 'Judul proposal maksimal :max karakter.',
            'deskripsi.required' => 'Deskripsi proposal wajib diisi.',
            'deskripsi.string' => 'Deskripsi proposal harus berupa teks.',
            'deskripsi.min' => 'Deskripsi proposal minimal :min karakter.',
            'deskripsi.max' => 'Deskripsi proposal maksimal :max karakter.',
        ];
    }
}
