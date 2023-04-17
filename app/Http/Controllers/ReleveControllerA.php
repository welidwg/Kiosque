<?php

namespace App\Http\Controllers;

use App\Models\Carburant;
use App\Models\Cigarette;
use App\Models\Compte;
use App\Models\Releve;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PHPUnit\Framework\MockObject\Stub\ReturnStub;

class ReleveControllerA extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $releves = Releve::with("caissier")->orderBy('date_systeme', 'desc')->orderBy('heure_d', 'asc')->get();

        return view("dashboard.pages.releve.index", ["releves" => $releves]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $carburants = Carburant::all();
        $cigarettes = Cigarette::orderBy("id", "desc")->orderBy("type", "asc")->get();

        return view("dashboard.pages.releve.add_releve", ["carburants" => $carburants, "cigarettes" => $cigarettes]);
    }
    public function updateCarburant($title, $qte, $prixV = null)
    {
        $carb = Carburant::where("titre", $title)->first();
        //    $oldQte=$carb->qtiteStk;
        $newQte = $carb->qtiteStk - $qte;
        $prix_v = $carb->prixV;
        if ($prixV != null && $prixV != 0) {
            $carb->qtiteStk = $newQte;
            if ($carb->prixV != $prixV) {
                $prix_v = $prixV;
                $carb->prixV = $prixV;
                $carb->valeur_stock = $newQte * $prix_v;
            }
        }


        $carb->save();
    }
    public function updateCigarette($type, $qte)
    {
        $cigar = Cigarette::where("type", $type)->first();
        $cigar->qte -= $qte;
        $cigar->save();
    }
    public function editCarburant($title, $qte)
    {
        $carb = Carburant::where("titre", $title)->first();
        //    $oldQte=$carb->qtiteStk;
        $newQte = $carb->qtiteStk + $qte;
        $carb->qtiteStk = $newQte;
        $carb->valeur_stock = $newQte * $carb->prixV;
        $carb->save();
    }
    public function editCigarette($title, $qte)
    {
        $cigar = Cigarette::where("type", $title)->first();
        //    $oldQte=$carb->qtiteStk;
        $cigar->qte += $qte;
        $cigar->save();
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $data["date_systeme"] = date("Y-m-d");
            $qte = "";
            $prix = "";
            $montant = "";


            $check = Releve::where("user_id", $request->user_id)->where("date_systeme", date("Y-d-m", strtotime($request->date_systeme)))->get();
            // $check2 = Releve::where("date_systeme", $data["date_systeme"])->whereTime("heure_d", "<=", $data["heure_d"])->whereTime("heure_f", ">=", $data["heure_d"])->whereTime("heure_d", "<=", $data["heure_f"])->whereTime("heure_f", "<=", $data["heure_f"])->first();
            $final = [];
            $final_cigars = [];
            $check2 = Releve::orderBy('id', 'desc')->first();

            $carbs = Carburant::all();

            if ($check->count() == 0) {
                // if ($check2) {


                //     if ($check2->date_systeme == date("Y-m-d")) {
                //         if ((date("H:i:s", strtotime($data["heure_d"])) >= $check2->heure_d && date("H:i:s", strtotime($data["heure_f"])) <= $check2->heure_f) ||
                //             (date("H:i:s", strtotime($data["heure_d"])) >= $check2->heure_d) || (date("H:i:s", strtotime($data["heure_f"])) <= $check2->heure_f)
                //         ) {
                //             return response(json_encode(["type" => "error", "message" => "Il y a un conflit du temps ! Un autre caissier a déjà cloturé sa journée dans ce temps !"]), 500);
                //         }
                //     }
                // }
                foreach ($request->input("titles") as $v) {
                    $carb = [];
                    $titleLowercase = strtolower($v);
                    $qte = "qte_" . $titleLowercase;
                    $prix = "prix_" . $titleLowercase;
                    $montant = "montant_" . $titleLowercase;
                    $carb = array($v => ["qte" => $request->input($qte), "prix" => $request->input($prix), "montant" => $request->input($montant)]);
                    array_push($final, $carb);
                    $this->updateCarburant($v, $data[$qte], $request->input($prix));
                }
                foreach ($request->input("types") as $type) {
                    $cigar = Cigarette::where("type", $type)->first();
                    $cigars = [];

                    $qte = "qteC_" . $cigar->id;
                    $prix = "prixVC_" . $cigar->id;
                    $montant = "montantC_" . $cigar->id;
                    $cigars = array($type => ["qte" => $request->input($qte), "prix" => $request->input($prix), "montant" => $request->input($montant)]);
                    array_push($final_cigars, $cigars);
                    $this->updateCigarette($type, $request->input($qte));
                }
                $compte = Compte::where("id", "!=", null)->first();
                if ($compte) {
                    $compte->montant += $request->totalPdf;
                    $compte->tva_encaisse += $request->totalPdf * 0.2;
                    $compte->save();
                } else {
                    Compte::create(["montant" => $request->totalPdf]);
                }
                $data["vente"] = json_encode($final);
                $data["tva"] = $request->totalPdf * 0.2;
                $data["vente_cigarette"] = json_encode($final_cigars);
                Releve::create($data);
                return response(json_encode(["type" => "success", "message" => "Bien ajouté !"]), 200);

                // return response(json_encode(["type" => "success", "message" => date("H:i:s", strtotime($data["heure_d"])) . "  " . $check2->heure_d]), 200);
            } else {
                return response(json_encode(["type" => "error", "message" => "Vous avez déjà ajouter aujourd'hui !"]), 500);
            }
        } catch (\Throwable $th) {
            return response(json_encode(["type" => "error", "message" => $th->getMessage()]), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Releve  $releve
     * @return \Illuminate\Http\Response
     */
    public function show(Releve $releve)
    {
        $carburants = Carburant::all();
        $cigarettes = Cigarette::all();

        return view("dashboard.pages.releve.show", ["releve" => $releve, "carburants" => $carburants, "cigarettes" => $cigarettes]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Releve  $releve
     * @return \Illuminate\Http\Response
     */
    public function edit(Releve $releve)
    {
        //

        return "test";
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Releve  $releve
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Releve $releve)
    {
        //
        try {
            $data = $request->all();
            $oldTotal = $releve->totalSaisie;
            $oldTotalPdf = $releve->totalPdf;
            $compte = Compte::latest()->first();
            if ($oldTotalPdf != $data["totalPdf"]) {
                $compte->montant -= $oldTotalPdf;
                if ($compte->tva_encaisse != 0) {
                    $compte->tva_encaisse -= $oldTotalPdf * 0.2;
                }
                $compte->montant += $data["totalPdf"];
                $compte->tva_encaisse += $data["totalPdf"] * 0.2;

                $compte->save();
            }

            $ventes = json_decode($releve->vente);
            $ventes_cigars = $releve->vente_cigarette == null ? [] :  json_decode($releve->vente_cigarette);


            $qte = "";
            $prix = "";
            $montant = "";
            $final = [];
            $final_ventes = [];

            foreach ($request->input("titles") as $titre) {
                $carb = [];
                $titleLowercase = strtolower($titre);
                $qte = "qte_" . $titleLowercase;
                $prix = "prix_" . $titleLowercase;
                $montant = "montant_" . $titleLowercase;

                foreach ($ventes as $vente) {
                    $title = strtolower($titre);
                    foreach ($vente as $k => $v) {
                        if ($k == $titre) {
                            if ($v->qte !== $request->input($qte)) {
                                $this->editCarburant($k, $v->qte);
                                $this->updateCarburant($k, $request->input($qte));
                            }
                        }
                    }
                }
                $carb = array($titre => ["qte" => $request->input($qte), "prix" => $request->input($prix), "montant" => $request->input($montant)]);
                array_push($final, $carb);
            }
            if ($request->has("types")) {


                foreach ($request->input("types") as $type) {
                    $types = [];
                    $cigar = Cigarette::where("type", $type)->first();

                    $qte = "qteC_" . $cigar->id;
                    $prix = "prixC_" . $cigar->id;
                    $montant = "montantC_" . $cigar->id;
                    if (count($ventes_cigars) > 0) {


                        foreach ($ventes_cigars as $vente) {

                            foreach ($vente as $k => $v) {
                                if ($k == $type) {
                                    if ($v->qte !== $request->input($qte)) {
                                        $this->editCigarette($k, $v->qte);
                                        $this->updateCigarette($k, $request->input($qte));
                                    }
                                }
                            }
                        }
                        $types = array($type => ["qte" => $request->input($qte), "prix" => $request->input($prix), "montant" => $request->input($montant)]);
                        array_push($final_ventes, $types);
                    }
                }
            }
            $data["vente"] = json_encode($final);
            $data["vente_cigarette"] = count($final_ventes) > 0 ? json_encode($final_ventes) : null;
            $data["date_systeme"] = $releve->date_systeme;
            $releve->update($data);

            return response(json_encode(["type" => "success", "message" => "Bien modifié !"]), 200);
        } catch (\Throwable $th) {
            return response(json_encode(["type" => "error", "message" => $th->getMessage()]), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Releve  $releve
     * @return \Illuminate\Http\Response
     */
    public function destroy(Releve $releve)
    {
        $releve->delete();

        return redirect()->route('releve.index')
            ->with('success', 'Relevee bien supprimé');
    }

    public function parCaissier()
    {
        $releves = Releve::with("caissier")->get();

        return view("dashboard.pages.releve.par_caissier", ["releves" => $releves]);
    }
}
