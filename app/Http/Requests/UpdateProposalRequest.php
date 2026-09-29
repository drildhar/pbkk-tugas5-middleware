<?php

namespace App\Http\Requests;

/**
 * Aturan validasi edit proposal identik dengan aturan pembuatannya, sehingga
 * request ini mewarisi StoreProposalRequest agar tidak ada duplikasi aturan.
 */
class UpdateProposalRequest extends StoreProposalRequest
{
    //
}
