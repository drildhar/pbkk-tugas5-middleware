<?php

namespace App\Http\Requests;

use App\Models\Proposal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GradeProposalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Hanya dosen/asdos yang boleh menilai. Pemeriksaan ini dilakukan lewat
     * Gate global `kelola-nilai` di dalam controller.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(Proposal::STATUSES)],
            'nilai' => [
                'nullable',
                'integer',
                'between:0,100',
                'required_if:status,'.Proposal::STATUS_APPROVED,
            ],
            'catatan_revisi' => [
                'nullable',
                'string',
                'max:2000',
                'required_if:status,'.Proposal::STATUS_REJECTED,
            ],
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
            'status.required' => 'Status peninjauan wajib dipilih.',
            'status.in' => 'Status peninjauan tidak valid.',
            'nilai.integer' => 'Nilai harus berupa angka bulat.',
            'nilai.between' => 'Nilai harus berada di antara 0 sampai 100.',
            'nilai.required_if' => 'Nilai wajib diisi ketika proposal disetujui.',
            'catatan_revisi.string' => 'Catatan revisi harus berupa teks.',
            'catatan_revisi.max' => 'Catatan revisi maksimal :max karakter.',
            'catatan_revisi.required_if' => 'Catatan revisi wajib diisi ketika proposal ditolak.',
        ];
    }
}
