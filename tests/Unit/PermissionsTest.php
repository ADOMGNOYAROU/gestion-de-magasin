<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Magasin;
use App\Models\Boutique;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;

class PermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer des utilisateurs de test
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->gestionnaire = User::factory()->create(['role' => 'gestionnaire']);
        $this->vendeur = User::factory()->create(['role' => 'vendeur']);
        
        // Créer des ressources de test
        $this->magasin = Magasin::factory()->create();
        $this->autreMagasin = Magasin::factory()->create();
        $this->boutique = Boutique::factory()->create(['magasin_id' => $this->magasin->id]);
        $this->autreBoutique = Boutique::factory()->create(['magasin_id' => $this->autreMagasin->id]);
        
        // Associer le gestionnaire à son magasin
        $this->gestionnaire->magasinResponsable()->associate($this->magasin)->save();
        
        // Associer le vendeur à sa boutique
        $this->vendeur->boutique()->associate($this->boutique)->save();
    }

    /** @test */
    public function admin_peut_gérer_tous_les_magasins()
    {
        $this->assertTrue(Gate::forUser($this->admin)->allows('manage-magasin', $this->magasin));
        $this->assertTrue(Gate::forUser($this->admin)->allows('manage-magasin', $this->autreMagasin));
    }

    /** @test */
    public function gestionnaire_peut_gérer_seulement_son_magasin()
    {
        $this->assertTrue(Gate::forUser($this->gestionnaire)->allows('manage-magasin', $this->magasin));
        $this->assertFalse(Gate::forUser($this->gestionnaire)->allows('manage-magasin', $this->autreMagasin));
    }

    /** @test */
    public function vendeur_ne_peut_gérer_aucun_magasin()
    {
        $this->assertFalse(Gate::forUser($this->vendeur)->allows('manage-magasin', $this->magasin));
        $this->assertFalse(Gate::forUser($this->vendeur)->allows('manage-magasin', $this->autreMagasin));
    }

    /** @test */
    public function admin_peut_gérer_toutes_les_boutiques()
    {
        $this->assertTrue(Gate::forUser($this->admin)->allows('manage-boutique', $this->boutique));
        $this->assertTrue(Gate::forUser($this->admin)->allows('manage-boutique', $this->autreBoutique));
    }

    /** @test */
    public function gestionnaire_peut_gérer_les_boutiques_de_son_magasin()
    {
        $this->assertTrue(Gate::forUser($this->gestionnaire)->allows('manage-boutique', $this->boutique));
        $this->assertFalse(Gate::forUser($this->gestionnaire)->allows('manage-boutique', $this->autreBoutique));
    }

    /** @test */
    public function vendeur_peut_gérer_seulement_sa_boutique()
    {
        $this->assertTrue(Gate::forUser($this->vendeur)->allows('manage-boutique', $this->boutique));
        $this->assertFalse(Gate::forUser($this->vendeur)->allows('manage-boutique', $this->autreBoutique));
    }

    /** @test */
    public function admin_peut_vendre_partout()
    {
        $this->assertTrue(Gate::forUser($this->admin)->allows('sell-in-boutique', $this->boutique));
        $this->assertTrue(Gate::forUser($this->admin)->allows('sell-in-boutique', $this->autreBoutique));
    }

    /** @test */
    public function gestionnaire_peut_vendre_dans_les_boutiques_de_son_magasin()
    {
        $this->assertTrue(Gate::forUser($this->gestionnaire)->allows('sell-in-boutique', $this->boutique));
        $this->assertFalse(Gate::forUser($this->gestionnaire)->allows('sell-in-boutique', $this->autreBoutique));
    }

    /** @test */
    public function vendeur_peut_vendre_seulement_dans_sa_boutique()
    {
        $this->assertTrue(Gate::forUser($this->vendeur)->allows('sell-in-boutique', $this->boutique));
        $this->assertFalse(Gate::forUser($this->vendeur)->allows('sell-in-boutique', $this->autreBoutique));
    }

    /** @test */
    public function admin_peut_voir_statistiques_globales()
    {
        $this->assertTrue(Gate::forUser($this->admin)->allows('view-global-stats'));
    }

    /** @test */
    public function gestionnaire_ne_peut_pas_voir_statistiques_globales()
    {
        $this->assertFalse(Gate::forUser($this->gestionnaire)->allows('view-global-stats'));
    }

    /** @test */
    public function vendeur_ne_peut_pas_voir_statistiques_globales()
    {
        $this->assertFalse(Gate::forUser($this->vendeur)->allows('view-global-stats'));
    }

    /** @test */
    public function admin_et_gestionnaire_peuvent_voir_rapports_partenaires()
    {
        $this->assertTrue(Gate::forUser($this->admin)->allows('view-rapports-partenaires'));
        $this->assertTrue(Gate::forUser($this->gestionnaire)->allows('view-rapports-partenaires'));
    }

    /** @test */
    public function vendeur_ne_peut_pas_voir_rapports_partenaires()
    {
        $this->assertFalse(Gate::forUser($this->vendeur)->allows('view-rapports-partenaires'));
    }

    /** @test */
    public function admin_peut_gérer_tous_les_types_ressources()
    {
        $this->assertTrue(Gate::forUser($this->admin)->allows('manage-produits'));
        $this->assertTrue(Gate::forUser($this->admin)->allows('manage-entrees-stock'));
        $this->assertTrue(Gate::forUser($this->admin)->allows('manage-transferts'));
        $this->assertTrue(Gate::forUser($this->admin)->allows('manage-ventes'));
        $this->assertTrue(Gate::forUser($this->admin)->allows('manage-rapports'));
    }

    /** @test */
    public function gestionnaire_peut_gérer_ressources_appropriées()
    {
        $this->assertTrue(Gate::forUser($this->gestionnaire)->allows('manage-produits'));
        $this->assertTrue(Gate::forUser($this->gestionnaire)->allows('manage-entrees-stock'));
        $this->assertTrue(Gate::forUser($this->gestionnaire)->allows('manage-transferts'));
        $this->assertTrue(Gate::forUser($this->gestionnaire)->allows('manage-ventes'));
        $this->assertTrue(Gate::forUser($this->gestionnaire)->allows('manage-rapports'));
    }

    /** @test */
    public function vendeur_peut_seulement_gérer_les_ventes()
    {
        $this->assertFalse(Gate::forUser($this->vendeur)->allows('manage-produits'));
        $this->assertFalse(Gate::forUser($this->vendeur)->allows('manage-entrees-stock'));
        $this->assertFalse(Gate::forUser($this->vendeur)->allows('manage-transferts'));
        $this->assertTrue(Gate::forUser($this->vendeur)->allows('manage-ventes'));
        $this->assertFalse(Gate::forUser($this->vendeur)->allows('manage-rapports'));
    }

    /** @test */
    public function admin_peut_voir_tous_les_stocks()
    {
        $this->assertTrue(Gate::forUser($this->admin)->allows('view-stock-magasin', $this->magasin));
        $this->assertTrue(Gate::forUser($this->admin)->allows('view-stock-magasin', $this->autreMagasin));
        $this->assertTrue(Gate::forUser($this->admin)->allows('view-stock-boutique', $this->boutique));
        $this->assertTrue(Gate::forUser($this->admin)->allows('view-stock-boutique', $this->autreBoutique));
    }

    /** @test */
    public function gestionnaire_peut_voir_stocks_de_son_magasin_seulement()
    {
        $this->assertTrue(Gate::forUser($this->gestionnaire)->allows('view-stock-magasin', $this->magasin));
        $this->assertFalse(Gate::forUser($this->gestionnaire)->allows('view-stock-magasin', $this->autreMagasin));
        $this->assertTrue(Gate::forUser($this->gestionnaire)->allows('view-stock-boutique', $this->boutique));
        $this->assertFalse(Gate::forUser($this->gestionnaire)->allows('view-stock-boutique', $this->autreBoutique));
    }

    /** @test */
    public function vendeur_peut_voir_stock_de_sa_boutique_seulement()
    {
        $this->assertFalse(Gate::forUser($this->vendeur)->allows('view-stock-magasin', $this->magasin));
        $this->assertFalse(Gate::forUser($this->vendeur)->allows('view-stock-magasin', $this->autreMagasin));
        $this->assertTrue(Gate::forUser($this->vendeur)->allows('view-stock-boutique', $this->boutique));
        $this->assertFalse(Gate::forUser($this->vendeur)->allows('view-stock-boutique', $this->autreBoutique));
    }
}
