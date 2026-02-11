<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Produit;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MiddlewarePermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer des utilisateurs de test
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->gestionnaire = User::factory()->create(['role' => 'gestionnaire']);
        $this->vendeur = User::factory()->create(['role' => 'vendeur']);
    }

    /** @test */
    public function admin_peut_accéder_aux_routes_protégées_par_admin()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/dashboard');
        
        $response->assertStatus(200);
    }

    /** @test */
    public function gestionnaire_ne_peut_pas_accéder_aux_routes_admin()
    {
        $response = $this->actingAs($this->gestionnaire)
            ->get('/admin/dashboard');
        
        $response->assertStatus(403);
    }

    /** @test */
    public function vendeur_ne_peut_pas_accéder_aux_routes_admin()
    {
        $response = $this->actingAs($this->vendeur)
            ->get('/admin/dashboard');
        
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_peut_accéder_aux_routes_protégées_par_gestionnaire()
    {
        $response = $this->actingAs($this->admin)
            ->get('/gestionnaire/dashboard');
        
        $response->assertStatus(200);
    }

    /** @test */
    public function gestionnaire_peut_accéder_aux_routes_protégées_par_gestionnaire()
    {
        $response = $this->actingAs($this->gestionnaire)
            ->get('/gestionnaire/dashboard');
        
        $response->assertStatus(200);
    }

    /** @test */
    public function vendeur_ne_peut_pas_accéder_aux_routes_gestionnaire()
    {
        $response = $this->actingAs($this->vendeur)
            ->get('/gestionnaire/dashboard');
        
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_peut_accéder_aux_routes_protégées_par_vendeur()
    {
        $response = $this->actingAs($this->admin)
            ->get('/vendeur/dashboard');
        
        $response->assertStatus(200);
    }

    /** @test */
    public function gestionnaire_peut_accéder_aux_routes_protégées_par_vendeur()
    {
        $response = $this->actingAs($this->gestionnaire)
            ->get('/vendeur/dashboard');
        
        $response->assertStatus(200);
    }

    /** @test */
    public function vendeur_peut_accéder_aux_routes_protégées_par_vendeur()
    {
        $response = $this->actingAs($this->vendeur)
            ->get('/vendeur/dashboard');
        
        $response->assertStatus(200);
    }

    /** @test */
    public function utilisateur_non_authentifié_ne_peut_pas_accéder_aux_routes_protégées()
    {
        $response = $this->get('/produits');
        $response->assertRedirect('/login');
        
        $response = $this->get('/entrees-stock');
        $response->assertRedirect('/login');
        
        $response = $this->get('/transferts');
        $response->assertRedirect('/login');
        
        $response = $this->get('/ventes');
        $response->assertRedirect('/login');
        
        $response = $this->get('/rapports');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function admin_peut_accéder_aux_routes_des_ressources()
    {
        // Routes produits
        $response = $this->actingAs($this->admin)->get('/produits');
        $response->assertStatus(200);
        
        // Routes entrées stock
        $response = $this->actingAs($this->admin)->get('/entrees-stock');
        $response->assertStatus(200);
        
        // Routes transferts
        $response = $this->actingAs($this->admin)->get('/transferts');
        $response->assertStatus(200);
        
        // Routes ventes
        $response = $this->actingAs($this->admin)->get('/ventes');
        $response->assertStatus(200);
        
        // Routes rapports
        $response = $this->actingAs($this->admin)->get('/rapports');
        $response->assertStatus(200);
    }

    /** @test */
    public function gestionnaire_peut_accéder_aux_routes_des_ressources_autorisisées()
    {
        // Routes produits
        $response = $this->actingAs($this->gestionnaire)->get('/produits');
        $response->assertStatus(200);
        
        // Routes entrées stock
        $response = $this->actingAs($this->gestionnaire)->get('/entrees-stock');
        $response->assertStatus(200);
        
        // Routes transferts
        $response = $this->actingAs($this->gestionnaire)->get('/transferts');
        $response->assertStatus(200);
        
        // Routes ventes
        $response = $this->actingAs($this->gestionnaire)->get('/ventes');
        $response->assertStatus(200);
        
        // Routes rapports
        $response = $this->actingAs($this->gestionnaire)->get('/rapports');
        $response->assertStatus(200);
    }

    /** @test */
    public function vendeur_peut_seulement_accéder_aux_routes_ventes()
    {
        // Routes produits - interdit
        $response = $this->actingAs($this->vendeur)->get('/produits');
        $response->assertStatus(403);
        
        // Routes entrées stock - interdit
        $response = $this->actingAs($this->vendeur)->get('/entrees-stock');
        $response->assertStatus(403);
        
        // Routes transferts - interdit
        $response = $this->actingAs($this->vendeur)->get('/transferts');
        $response->assertStatus(403);
        
        // Routes ventes - autorisé
        $response = $this->actingAs($this->vendeur)->get('/ventes');
        $response->assertStatus(200);
        
        // Routes rapports - interdit
        $response = $this->actingAs($this->vendeur)->get('/rapports');
        $response->assertStatus(403);
    }

    /** @test */
    public function api_routes_sont_protégées_correctement()
    {
        // API transferts - admin et gestionnaire
        $response = $this->actingAs($this->admin)->get('/api/stock-disponible');
        $response->assertStatus(200);
        
        $response = $this->actingAs($this->gestionnaire)->get('/api/stock-disponible');
        $response->assertStatus(200);
        
        $response = $this->actingAs($this->vendeur)->get('/api/stock-disponible');
        $response->assertStatus(403);
        
        // API panier - tous les rôles
        $response = $this->actingAs($this->admin)->get('/api/stock-boutique');
        $response->assertStatus(200);
        
        $response = $this->actingAs($this->gestionnaire)->get('/api/stock-boutique');
        $response->assertStatus(200);
        
        $response = $this->actingAs($this->vendeur)->get('/api/stock-boutique');
        $response->assertStatus(200);
    }

    /** @test */
    public function dashboard_principal_est_accessible_par_tous()
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);
        
        $response = $this->actingAs($this->gestionnaire)->get('/dashboard');
        $response->assertStatus(200);
        
        $response = $this->actingAs($this->vendeur)->get('/dashboard');
        $response->assertStatus(200);
    }

    /** @test */
    public function routes_de_crud_sont_protégées()
    {
        $produit = Produit::factory()->create();
        
        // Test routes produits
        $this->actingAs($this->admin)->get("/produits/{$produit->id}/edit")->assertStatus(200);
        $this->actingAs($this->gestionnaire)->get("/produits/{$produit->id}/edit")->assertStatus(200);
        $this->actingAs($this->vendeur)->get("/produits/{$produit->id}/edit")->assertStatus(403);
        
        // Test routes ventes
        $this->actingAs($this->admin)->get('/ventes/create')->assertStatus(200);
        $this->actingAs($this->gestionnaire)->get('/ventes/create')->assertStatus(200);
        $this->actingAs($this->vendeur)->get('/ventes/create')->assertStatus(200);
    }
}
