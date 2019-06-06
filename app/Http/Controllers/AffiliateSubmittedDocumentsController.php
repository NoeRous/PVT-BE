<?php

namespace Muserpol\Http\Controllers;

use Illuminate\Http\Request;
use Muserpol\Models\Affiliate;
use Muserpol\Models\ProcedureRequirement;
use Illuminate\Support\Facades\DB;

class AffiliateSubmittedDocumentsController extends Controller
{
    public function getRequirements(Request $request)
    {
        logger($request->all());
        $affiliate = Affiliate::find($request->affiliate_id);
        $procedure_requirements_original = ProcedureRequirement::with('procedure_document')->where('procedure_modality_id', $request->procedure_modality_id)->where('number', '<>', 0)->orderBy('number')->get();
        $additional_requirements = ProcedureRequirement::with('procedure_document')->where('procedure_modality_id', $request->procedure_modality_id)->where('number', '=', 0)->orderBy('number')->get();
        $procedure_requirements = ProcedureRequirement::where('procedure_modality_id', $request->procedure_modality_id)
            ->where('number', '<>', 0)
            ->select(DB::raw("number, string_agg(procedure_document_id::text, ',') as procedure_document_ids"))
            ->groupBY('number')
            ->orderBy('number')
            ->get();
        $collect = collect([]);
        foreach ($procedure_requirements as $p) {
            foreach (explode(',', $p->procedure_document_ids) as $d) {
                foreach ($affiliate->submitted_documents()->where('status', true)->get() as $a) {
                    if ($a->procedure_document_id == $d) {
                        $collect->push($p->number);
                        break;
                    }
                }
            }
        }
        $requirements = collect([]);
        foreach ($procedure_requirements_original as $po) {
            if (!$collect->contains($po->number)) {
                $po->background = "";
                $po->status = false;
                $po->number = "N" . $po->number;
                $requirements->push($po);
            }
        }
        $requirements = $requirements->groupBy('number')->toArray();
        $data = [
            'additional_requirements' => $additional_requirements,
            'requirements' => $requirements
        ];
        return $data;
    }
}
