<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DevisController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\IndicateurController;
use App\Http\Controllers\NormeController;
use App\Http\Controllers\ParametrageController;
use App\Http\Controllers\PlanActionController;
use App\Http\Controllers\PlanAuditController;
use App\Http\Controllers\PrestationController;
use App\Http\Controllers\ProjetController;
use App\Http\Controllers\RapportAuditController;
use App\Http\Controllers\RecouvrementController;
use App\Http\Controllers\SuiviProjetController;
use App\Http\Controllers\TacheController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Cabinet QHSE (accompagnement, audit, formation)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('projets.index');
});

Auth::routes();

Route::middleware('auth')->group(function () {

    // --- Module 1 : Facturation ---
    Route::resource('clients', ClientController::class)->except(['show']);
    Route::get('clients/export/excel', [ClientController::class, 'export'])->name('clients.export');

    Route::get('devis', [DevisController::class, 'index'])->name('devis.index');
    Route::get('devis/creer', [DevisController::class, 'create'])->name('devis.create');
    Route::post('devis', [DevisController::class, 'store'])->name('devis.store');
    Route::get('devis/{devis}', [DevisController::class, 'show'])->name('devis.show');
    Route::get('devis/{devis}/modifier', [DevisController::class, 'edit'])->name('devis.edit');
    Route::put('devis/{devis}', [DevisController::class, 'update'])->name('devis.update');
    Route::delete('devis/{devis}', [DevisController::class, 'destroy'])->name('devis.destroy');
    Route::get('devis/{devis}/imprimer', [DevisController::class, 'imprimer'])->name('devis.imprimer');
    Route::post('devis/{devis}/convertir', [DevisController::class, 'convertirEnFacture'])->name('devis.convertir');
    Route::get('devis/export/excel', [DevisController::class, 'export'])->name('devis.export');

    Route::get('factures', [FactureController::class, 'index'])->name('factures.index');
    Route::get('factures/creer', [FactureController::class, 'create'])->name('factures.create');
    Route::post('factures', [FactureController::class, 'store'])->name('factures.store');
    Route::get('factures/{facture}', [FactureController::class, 'show'])->name('factures.show');
    Route::get('factures/{facture}/modifier', [FactureController::class, 'edit'])->name('factures.edit');
    Route::put('factures/{facture}', [FactureController::class, 'update'])->name('factures.update');
    Route::delete('factures/{facture}', [FactureController::class, 'destroy'])->name('factures.destroy');
    Route::get('factures/{facture}/imprimer', [FactureController::class, 'imprimer'])->name('factures.imprimer');
    Route::post('factures/{facture}/avoir', [FactureController::class, 'genererAvoir'])->name('factures.avoir');
    Route::get('factures/export/excel', [FactureController::class, 'export'])->name('factures.export');

    Route::get('recouvrement', [RecouvrementController::class, 'index'])->name('recouvrement.index');
    Route::post('recouvrement', [RecouvrementController::class, 'store'])->name('recouvrement.store');
    Route::post('recouvrement/{echeance}/reglement', [RecouvrementController::class, 'enregistrerReglement'])->name('recouvrement.reglement');
    Route::delete('recouvrement/{echeance}', [RecouvrementController::class, 'destroy'])->name('recouvrement.destroy');
    Route::get('recouvrement/export/excel', [RecouvrementController::class, 'export'])->name('recouvrement.export');

    // --- Module 2 : Projet ---
    Route::get('projets', [ProjetController::class, 'index'])->name('projets.index');
    Route::get('projets/creer', [ProjetController::class, 'create'])->name('projets.create');
    Route::post('projets', [ProjetController::class, 'store'])->name('projets.store');
    Route::get('projets/{projet}', [ProjetController::class, 'show'])->name('projets.show');
    Route::get('projets/{projet}/modifier', [ProjetController::class, 'edit'])->name('projets.edit');
    Route::put('projets/{projet}', [ProjetController::class, 'update'])->name('projets.update');
    Route::delete('projets/{projet}', [ProjetController::class, 'destroy'])->name('projets.destroy');
    Route::get('projets/export/excel', [ProjetController::class, 'export'])->name('projets.export');

    Route::post('projets/{projet}/plans-action', [PlanActionController::class, 'store'])->name('projets.plans-action.store');
    Route::post('plans-action/{planAction}/actions', [PlanActionController::class, 'storeAction'])->name('plans-action.actions.store');
    Route::put('actions/{action}/statut', [PlanActionController::class, 'updateStatutAction'])->name('actions.statut');

    Route::post('projets/{projet}/notes', [SuiviProjetController::class, 'storeNote'])->name('projets.notes.store');
    Route::post('projets/{projet}/rapports-reunion', [SuiviProjetController::class, 'storeRapportReunion'])->name('projets.rapports-reunion.store');
    Route::delete('pieces-jointes/{pieceJointe}', [SuiviProjetController::class, 'destroyPieceJointe'])->name('pieces-jointes.destroy');

    // --- Module 3 : Audits ---
    Route::get('normes', [NormeController::class, 'index'])->name('normes.index');
    Route::post('normes', [NormeController::class, 'store'])->name('normes.store');
    Route::post('normes/{norme}/exigences', [NormeController::class, 'storeExigence'])->name('normes.exigences.store');

    Route::get('audits', [AuditController::class, 'index'])->name('audits.index');
    Route::get('audits/creer', [AuditController::class, 'create'])->name('audits.create');
    Route::post('audits', [AuditController::class, 'store'])->name('audits.store');
    Route::get('audits/{audit}', [AuditController::class, 'show'])->name('audits.show');
    Route::post('audits/{audit}/realise', [AuditController::class, 'marquerRealise'])->name('audits.realise');
    Route::get('audits/export/excel', [AuditController::class, 'export'])->name('audits.export');

    Route::post('normes/{norme}/plan-modele', [PlanAuditController::class, 'storeModele'])->name('normes.plan-modele.store');
    Route::post('plans-audit/{planAudit}/lignes', [PlanAuditController::class, 'storeLigne'])->name('plans-audit.lignes.store');

    Route::post('audits/{audit}/rapport', [RapportAuditController::class, 'store'])->name('audits.rapport.store');
    Route::post('rapports-audit/{rapport}/constats', [RapportAuditController::class, 'storeConstat'])->name('rapports-audit.constats.store');
    Route::post('rapports-audit/{rapport}/finaliser', [RapportAuditController::class, 'finaliser'])->name('rapports-audit.finaliser');

    // --- Module 4 : Formation ---
    Route::get('formations', [FormationController::class, 'index'])->name('formations.index');
    Route::get('formations/creer', [FormationController::class, 'create'])->name('formations.create');
    Route::post('formations', [FormationController::class, 'store'])->name('formations.store');
    Route::get('formations/{formation}', [FormationController::class, 'show'])->name('formations.show');
    Route::post('formations/{formation}/participants', [FormationController::class, 'storeParticipant'])->name('participants.store');
    Route::post('formations/{formation}/cloturer', [FormationController::class, 'marquerRealisee'])->name('formations.cloturer');
    Route::post('participants/{participant}/certificat', [FormationController::class, 'genererCertificat'])->name('participants.certificat');
    Route::get('formations/export/excel', [FormationController::class, 'export'])->name('formations.export');

    // --- Module 5 : Documents ---
    Route::get('documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::post('documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::post('documents/{document}/nouvelle-version', [DocumentController::class, 'nouvelleVersion'])->name('documents.nouvelle-version');
    Route::get('documents/{document}/telecharger', [DocumentController::class, 'telecharger'])->name('documents.telecharger');

    // --- Module 6 : Tableau de bord ---
    Route::get('indicateurs', [IndicateurController::class, 'index'])->name('indicateurs.index');
    Route::post('indicateurs', [IndicateurController::class, 'store'])->name('indicateurs.store');
    Route::post('indicateurs/{indicateur}/valeurs', [IndicateurController::class, 'storeValeur'])->name('indicateurs.valeurs.store');
    Route::post('indicateurs/{indicateur}/actualiser', [IndicateurController::class, 'actualiserDepuisBase'])->name('indicateurs.actualiser');

    // --- Module 7 : Tâches ---
    Route::get('taches', [TacheController::class, 'index'])->name('taches.index');
    Route::post('taches', [TacheController::class, 'store'])->name('taches.store');
    Route::put('taches/{tache}/statut', [TacheController::class, 'updateStatut'])->name('taches.statut');
    Route::delete('taches/{tache}', [TacheController::class, 'destroy'])->name('taches.destroy');
    Route::get('taches/export/excel', [TacheController::class, 'export'])->name('taches.export');

    // --- Module 8 : Paramétrage — protégé par le middleware de permissions ---
    Route::middleware('permission:parametrage,voir')->group(function () {
        Route::get('parametrage', [ParametrageController::class, 'index'])->name('parametrage.index');
        Route::get('prestations', [PrestationController::class, 'index'])->name('prestations.index');
    });

    Route::middleware('permission:parametrage,ajouter')->group(function () {
        Route::post('parametrage/phases', [ParametrageController::class, 'storePhase'])->name('parametrage.phases.store');
        Route::post('parametrage/phases/{phase}/actions', [ParametrageController::class, 'storeActionPreetablie'])->name('parametrage.phases.actions.store');
        Route::post('prestations', [PrestationController::class, 'store'])->name('prestations.store');
    });

    Route::middleware('permission:parametrage,modifier')->group(function () {
        Route::post('parametrage/permissions', [ParametrageController::class, 'updatePermission'])->name('parametrage.permissions.update');
        Route::post('parametrage/interface', [ParametrageController::class, 'updateInterface'])->name('parametrage.interface.update');
        Route::put('prestations/{prestation}', [PrestationController::class, 'update'])->name('prestations.update');
    });

    Route::middleware('permission:parametrage,supprimer')->group(function () {
        Route::delete('prestations/{prestation}', [PrestationController::class, 'destroy'])->name('prestations.destroy');
    });
});
