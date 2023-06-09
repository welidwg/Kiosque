<?php

namespace App\Http\Controllers;

use App\Models\Carburant;
use App\Models\Compte;
use App\Models\Releve;
use Illuminate\Http\Request;

class CarburantControllerA extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $carburants = Carburant::all();
        return view("dashboard.pages.carburant.index", compact('carburants'));
    }
    public function stats()
    {
        $all = Carburant::all();
        return view("dashboard.pages.carburant.stats", ["carburants" => $all]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view("dashboard.pages.carburant.add_carburant");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {
        try {
            Carburant::create($req->all());


            return response(json_encode(["type" => "success", "message" => "Carburant bien ajouté !"]), 200);
        } catch (\Throwable $th) {
            return response(json_encode(["type" => "error", "message" => $th->getMessage()]), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Carburant  $carburant
     * @return \Illuminate\Http\Response
     */
    public function show(Carburant $carburant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Carburant  $carburant
     * @return \Illuminate\Http\Response
     */
    public function edit(Carburant $carburant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Carburant  $carburant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Carburant $carburant)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Carburant  $carburant
     * @return \Illuminate\Http\Response
     */
    public function destroy(Carburant $carburant)
    {
        //
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function jauge()
    {
        # code...
        $all = Carburant::all();
        return view("dashboard.pages.carburant.jauge", ["carburants" => $all]);
    }
    public function seuil()
    {
        # code...
        $all = Carburant::all();
        return view("dashboard.pages.carburant.seuil", ["carburants" => $all]);
    }
    public function marge()
    {
        # code...
        $all = Carburant::all();
        return view("dashboard.pages.carburant.marge", ["carburants" => $all]);
    }
    public function prixA()
    {
        # code...
        $all = Carburant::all();
        return view("dashboard.pages.carburant.prixA", ["carburants" => $all]);
    }
    public function prixV()
    {
        # code...
        $all = Carburant::all();
        return view("dashboard.pages.carburant.prixV", ["carburants" => $all]);
    }
    public function editjauge(Request $req)
    {
        # code...
        $all = Carburant::all();
        $column = "";
        $data = "";
        $ecraser = false;
        if ($req->has("ecraser")) {
            $ecraser = true;
        }
        foreach ($all as $v) {
            $data = "qtiteJg" . $v->id;


            $v->qtiteJg = $req->$data;
            if ($ecraser) {
                $v->qtiteStk = $req->$data;
                $v->valeur_stock = $v->prixV * $req->$data;
            }
            $v->save();
        }

        return "done";
    }

    public function editSeuil(Request $req)
    {
        # code...
        $all = Carburant::all();
        $column = "";
        $data = "";
        foreach ($all as $v) {
            $data = "seuil" . $v->id;

            $v->seuilA = $req->$data;
            $v->save();
            # code...
        }

        return "done";
    }

    public function editMarge(Request $req)
    {
        # code...
        $all = Carburant::all();
        $column = "";
        $data = "";
        foreach ($all as $v) {
            $data = "marge_beneficiere" . $v->id;
            // $newPV = $v->prixA * (1 + $req->$data);
            $v->marge_beneficiere = $req->$data;
            // $v->prixV = $newPV;
            // $v->valeur_stock = $v->qtiteStk * $newPV;
            $v->save();
            # code...
        }

        return "done";
    }

    public function editPrixA(Request $req)
    {
        # code...
        $all = Carburant::all();
        $column = "";
        $data = "";
        foreach ($all as $v) {
            $data = "prixA" . $v->id;
            if ($v->prixA != $req->$data) {
                $v->prixA = $req->$data;

                $newPV = $req->$data * (1 + $v->marge_beneficiere);
                $v->prixV = $newPV;
                $v->valeur_stock = $v->qtiteStk * $newPV;
                $v->save();
            }


            # code...
        }

        return "done";
    }
    public function editPrixV(Request $req)
    {
        # code...
        $all = Carburant::all();
        $column = "";
        $data = "";
        foreach ($all as $v) {
            $data = "prixV" . $v->id;
            if ($v->prixV != $req->$data) {
                $v->prixV = $req->$data;
                $v->valeur_stock = $v->qtiteStk * $req->$data;
                $v->save();
            }


            # code...
        }

        return "done";
    }

    public function majSeuilCalcule(Request $req)
    {
        $carbs = Carburant::all();
        $oneWeekAgo = now()->subWeek();
        $data = "";
        $moyennes = [];
        $test = 0;
        foreach ($carbs as $carburant) {
            $moyennes[$carburant->titre] = $carburant->seuilA;
        }
        $dates = Releve::select('date_systeme')->whereBetween("date_systeme", [date("Y-m-d", strtotime($oneWeekAgo)), date("Y-m-d")])->distinct()->get();
        foreach ($dates as $date) {
            $rels = Releve::where('date_systeme', '=', $date->date_systeme)->get();
            if ($rels) {
                foreach ($rels as $rel) {
                    $ventes = json_decode($rel->vente);
                    foreach ($carbs as $carburant) {
                        $total = 0;
                        foreach ($ventes as $vente) {
                            foreach ($vente as $k => $v) {
                                if ($k == $carburant->titre) {
                                    $total += $v->qte;
                                    if (isset($moyennes[$carburant->titre])) {
                                        $moyennes[$carburant->titre] += $total;
                                    } else {
                                        $moyennes[$carburant->titre] = $total;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if (count($moyennes) !== 0) {
            foreach ($moyennes as $k => $v) {
                $carb = Carburant::where("titre", $k)->first();
                if ($carb->seuilA == $v) {
                    $carb->seuil = $carb->seuilA;
                } else {
                    $moyennes[$k] = (($v / 7) * 3) + $carb->seuilA;
                    $carb->seuil = (($v / 7) * 3) + $carb->seuilA;
                }
                $carb->save();

                // $test = $carb;
            }
        }
        return json_encode(count($moyennes));
    }
    public function ventes(Request $req, $id)
    {
        $month = date("m", strtotime($req->date));
        $year = date("Y", strtotime($req->date));
        $rels = Releve::whereMonth('date_systeme', $month)
            ->whereYear('date_systeme', $year)
            ->get();

        $carburant = Carburant::find($id);
        $rec = 0;
        $qte = 0;
        foreach ($rels as $rel) {
            $ventes = json_decode($rel->vente);
            foreach ($ventes as $vente) {
                foreach ($vente as $key => $value) {
                    if ($key == $carburant->titre) {
                        $rec += $value->montant;
                        $qte += $value->qte;
                    }
                }
            }
        }
        return json_encode(["qte" => $qte, "rec" => $rec]);
    }
}
