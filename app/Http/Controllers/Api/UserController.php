<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->role === 'admin', 403);

        return response()->json(
            User::with(['magasinResponsable', 'boutique'])->orderBy('name')->get()->map(fn (User $u) => $this->format($u))
        );
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->role === 'admin', 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,gestionnaire,vendeur'],
            'magasin_id' => ['nullable', 'exists:magasins,id'],
            'boutique_id' => ['nullable', 'exists:boutiques,id'],
        ]);

        $user = User::create([
            ...$data,
            'password' => Hash::make($data['password']),
        ]);

        return response()->json($this->format($user->load(['magasinResponsable', 'boutique'])), 201);
    }

    public function update(Request $request, User $user)
    {
        abort_unless($request->user()->role === 'admin', 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'in:admin,gestionnaire,vendeur'],
            'magasin_id' => ['nullable', 'exists:magasins,id'],
            'boutique_id' => ['nullable', 'exists:boutiques,id'],
        ]);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json($this->format($user->load(['magasinResponsable', 'boutique'])));
    }

    public function destroy(Request $request, User $user)
    {
        abort_unless($request->user()->role === 'admin', 403);
        abort_if($user->id === $request->user()->id, 422, 'Vous ne pouvez pas supprimer votre propre compte.');

        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé.']);
    }

    private function format(User $u): array
    {
        return [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'role' => $u->role,
            'magasin_id' => $u->magasin_id,
            'magasin_nom' => $u->magasinResponsable?->nom,
            'boutique_id' => $u->boutique_id,
            'boutique_nom' => $u->boutique?->nom,
        ];
    }
}
