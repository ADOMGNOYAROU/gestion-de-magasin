<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Credit;
use App\Models\CreditPayment;
use App\Models\VenteProduit;
use App\Models\StockBoutique;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class CreditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('manage-ventes');

        $credits = Credit::with(['client', 'vente.boutique'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('credits.index', compact('credits'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Credits are created automatically with sales
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort(404);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        Gate::authorize('manage-ventes');

        $credit = Credit::with(['client', 'vente.boutique', 'creditPayments'])
            ->findOrFail($id);

        return view('credits.show', compact('credit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        abort(404);
    }

    /**
     * Show form to add payment to credit.
     */
    public function createPayment(string $id)
    {
        Gate::authorize('manage-ventes');

        $credit = Credit::with(['client', 'vente.boutique'])
            ->findOrFail($id);

        if ($credit->status === 'paid') {
            return redirect()->route('credits.show', $credit)
                ->with('error', 'Ce crédit est déjà payé.');
        }

        return view('credits.add_payment', compact('credit'));
    }

    /**
     * Store payment for credit.
     */
    public function storePayment(Request $request, string $id)
    {
        Gate::authorize('manage-ventes');

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $credit = Credit::findOrFail($id);

        if ($credit->status === 'paid') {
            return redirect()->route('credits.show', $credit)
                ->with('error', 'Ce crédit est déjà payé.');
        }

        if ($request->amount > $credit->remaining_balance) {
            return back()->withErrors(['amount' => 'Le montant ne peut pas dépasser le solde restant.'])
                ->withInput();
        }

        DB::transaction(function () use ($request, $credit) {
            // Create payment
            CreditPayment::create([
                'credit_id' => $credit->id,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'notes' => $request->notes,
            ]);

            // Update credit
            $credit->remaining_balance -= $request->amount;
            if ($credit->remaining_balance <= 0) {
                $credit->status = 'paid';
                $credit->remaining_balance = 0;
            }
            $credit->save();
        });

        // Decrease stock proportionally
        $venteProduits = $credit->vente->venteProduits;
        foreach ($venteProduits as $vp) {
            $decrease = round(($request->amount / $credit->total_amount) * $vp->quantite);
            if ($decrease > 0) {
                $stock = StockBoutique::where('produit_id', $vp->produit_id)
                                      ->where('boutique_id', $credit->vente->boutique_id)
                                      ->first();
                if ($stock) {
                    $stock->quantite -= $decrease;
                    if ($stock->quantite < 0) {
                        $stock->quantite = 0;
                    }
                    $stock->save();
                }
            }
        }

        return redirect()->route('credits.show', $credit)
            ->with('success', 'Paiement enregistré avec succès.');
    }
}
