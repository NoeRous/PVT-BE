<?php

namespace Muserpol\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Muserpol\Models\Tag;
use Muserpol\Models\Affiliate;

class AffiliateTagsReport implements WithMultipleSheets
{
    use Exportable;
    public function sheets(): array
    {
        $sheets = [];
        $tags = Tag::whereIn('id', [15,16])->get();
        $affiliates = Affiliate::with('tags')
            ->select(
                'affiliates.id',
                'identity_card',
                'affiliate_city.first_shortened',
                'affiliates.first_name as affiliate_primer_nombre',
                'affiliates.second_name as affiliate_segundo_nombre',
                'affiliates.last_name as affiliate_paterno',
                'affiliates.mothers_last_name as affiliate_materno',
                'affiliates.surname_husband as affiliate_ap_casada',
                'affiliates.birth_date as affiliate_fecha_nacimiento',
                'affiliates.nua as affiliate_codigo_nua_cua'
            )
            ->leftJoin('cities as affiliate_city', 'affiliates.city_identity_card_id', '=', 'affiliate_city.id')
            ->has('tags')
            ->get();
        foreach ($tags as $t) {
            $sheets[] = new AffiliateTagSheet($t, $affiliates);
        }
        return $sheets;
    }
}
