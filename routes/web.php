<?php

use App\Http\Controllers\AlboController;
use App\Http\Controllers\RelStoriaAlboController;
use App\Http\Controllers\StoriaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('ruolo/create', 'RuoloController@create')->name('ruolo.create');
Route::post('ruolo/store', 'RuoloController@store')->name('ruolo.store');
Route::get('ruolo', 'RuoloController@index')->name('ruolo.index');
Route::get('ruolo/{id_ruolo}/edit', 'RuoloController@edit')->name('ruolo.edit');
Route::post('ruolo/{id_ruolo}/update', 'RuoloController@update')->name('ruolo.update');
Route::get('ruolo/{id_ruolo}/elimina-form', 'RuoloController@ruoloEliminaForm')->name('ruolo.elimina_form');
Route::post('ruolo/{id_ruolo}/elimina-execute', 'RuoloController@ruoloEliminaExecute')->name('ruolo.elimina_execute');

Route::get('editore/create', 'EditoreController@create')->name('editore.create');
Route::post('editore/store', 'EditoreController@store')->name('editore.store');
Route::get('editore', 'EditoreController@index')->name('editore.index');
Route::get('editore/{id_editore}/edit', 'EditoreController@edit')->name('editore.edit');
Route::post('editore/{id_editore}/update', 'EditoreController@update')->name('editore.update');
Route::get('editore/{id_editore}/elimina-form', 'EditoreController@editoreEliminaForm')->name('editore.elimina_form');
Route::post('editore/{id_editore}/elimina-execute', 'EditoreController@editoreEliminaExecute')->name('editore.elimina_execute');

Route::get('autore/create', 'AutoreController@create')->name('autore.create');
Route::post('autore/store', 'AutoreController@store')->name('autore.store');
Route::get('autore', 'AutoreController@index')->name('autore.index');
Route::get('autore/{id_autore}/edit', 'AutoreController@edit')->name('autore.edit');
Route::post('autore/{id_autore}/update', 'AutoreController@update')->name('autore.update');
Route::get('autore/{id_autore}/elimina-form', 'AutoreController@autoreEliminaForm')->name('autore.elimina_form');
Route::post('autore/{id_autore}/elimina-execute', 'AutoreController@autoreEliminaExecute')->name('autore.elimina_execute');

Route::get('storia/{id_storia}/aggiungi-autore', 'RelStoriaAutoreRuoloController@aggiungiAutore')->name('storia.aggiungi_autore');
Route::post('storia/{id_storia}/store-autore', 'RelStoriaAutoreRuoloController@storeAutore')->name('storia.store_autore');
Route::get('storia/{id_storia}/autori', 'RelStoriaAutoreRuoloController@index')->name('storia.autore');
Route::get('autore/{id_autore}/storie', 'RelStoriaAutoreRuoloController@storie_list')->name('autore.storia');
Route::get('storia/{id_storia}/{id_autore}/services/get-ruoli-json', 'RelStoriaAutoreRuoloController@getRuoliJson')->name('storia.get_ruoli_json');
Route::get('storia/{id_autore}/services/get-only-ruoli-json', 'RelStoriaAutoreRuoloController@getOnlyRuoliJson')->name('storia.get_only_ruoli_json');
Route::get('storia/elimina-autore-form/{id_storia}/{id_autore}', 'RelStoriaAutoreRuoloController@eliminaAutoreForm')->name('storia.elimina_autore_form');
Route::post('storia/elimina-autore-execute/{id_storia}/{id_autore}', 'RelStoriaAutoreRuoloController@eliminaAutoreExecute')->name('storia.elimina_autore_execute');

Route::get('storia/create', 'StoriaController@create')->name('storia.create');
Route::post('storia/store', 'StoriaController@store')->name('storia.store');
Route::get('storia', 'StoriaController@index')->name('storia.index');
Route::get('storia/list', 'StoriaController@index_list')->name('storia.list');
Route::get('storia/{id_storia}/details', 'StoriaController@detailsStoria')->name('storia.details');
Route::get('storia/{id_storia}/edit', 'StoriaController@edit')->name('storia.edit');
Route::post('storia/{id_storia}/update', 'StoriaController@update')->name('storia.update');
Route::get('storia/{id_storia}/show-albi', 'StoriaController@showAlbiFromStoria')->name('storia.show_from_storia');
Route::get('storia/{id_storia}/elimina-form', 'StoriaController@storiaEliminaForm')->name('storia.elimina_form');
Route::post('storia/{id_storia}/elimina-execute', 'StoriaController@storiaEliminaExecute')->name('storia.elimina_execute');
Route::get('storia/{id_storia}/services/get-trame', 'StoriaController@getTrame')->name('storia.get_trame_json');
Route::post('storia/{id_storia}/set-read-date', 'StoriaController@storiaSetReadDate')->name('storia.set_read_date');

Route::post('storia/{id_storia}/set-read-date', 'StoriaLettureController@AggiungiLettura')->name('storia.set_read_date');
Route::get('storia/{id_storia}/{data}/remove-read-date', 'StoriaLettureController@RimuoviLettura')->name('storia.remove_read_date');

Route::get('collana/create', 'CollanaController@create')->name('collana.create');
Route::post('collana/store', 'CollanaController@store')->name('collana.store');
Route::get('collana', 'CollanaController@index')->name('collana.index');
Route::get('collana/{id_collana}/edit', 'CollanaController@edit')->name('collana.edit');
Route::post('collana/{id_collana}/update', 'CollanaController@update')->name('collana.update');
Route::get('collana/{id_collana}/elimina-form', 'CollanaController@collanaEliminaForm')->name('collana.elimina_form');
Route::post('collana/{id_collana}/elimina-execute', 'CollanaController@collanaEliminaExecute')->name('collana.elimina_execute');

Route::get('albo/create', 'AlboController@create')->name('albo.create');
Route::post('albo/store', 'AlboController@store')->name('albo.store');
Route::get('albo', 'AlboController@index')->name('albo.index');
Route::get('albo/{id_albo}/', 'AlboController@showAlbo')->name('albo.show');
Route::get('albo/{id_albo}/details', 'AlboController@detailsAlbo')->name('albo.details');
Route::get('albo/{id_albo}/edit', 'AlboController@edit')->name('albo.edit');
Route::post('albo/{id_albo}/update', 'AlboController@update')->name('albo.update');
Route::get('albo/{id_albo}/elimina-form', 'AlboController@alboEliminaForm')->name('albo.elimina_form');
Route::post('albo/{id_albo}/elimina-execute', 'AlboController@alboEliminaExecute')->name('albo.elimina_execute');

Route::post('albo/{id_albo}/set-read-date', 'AlboLettureController@AggiungiLettura')->name('albo.set_read_date');
Route::get('albo/{id_albo}/{data}/remove-read-date', 'AlboLettureController@RimuoviLettura')->name('albo.remove_read_date');

Route::get('albo/{id_albo}/aggiungi-storia', 'RelStoriaAlboController@aggiungiStoria')->name('albo.aggiungi_storia');
Route::post('albo/{id_albo}/store-storia', 'RelStoriaAlboController@storeStoria')->name('albo.store_storia');
Route::get('albo/{id_albo}/storia', 'RelStoriaAlboController@index')->name('albo.storia');
Route::get('albo/{id_albo}/elimina-storia-form/{id_storia}', 'RelStoriaAlboController@eliminaStoriaForm')->name('albo.elimina_storia_form');
Route::post('albo/{id_albo}/elimina-autore-execute/{id_storia}', 'RelStoriaAlboController@eliminaStoriaExecute')->name('albo.elimina_storia_execute');
Route::get('albo/{id_albo}/services/get-storie', 'RelStoriaAlboController@getStorie')->name('albo.get_storie_json');

Route::get('statistiche', 'StatisticheController@index')->name('statistiche.index');
Route::get('statistiche/generali', 'StatisticheController@generali')->name('statistiche.generali');
Route::get('statistiche/albi-pubblicati-anno/{anno}', 'StatisticheController@albiPerMese')->name('statistiche.albi_mese');
Route::Get('statistiche/albi-pubblicati', 'StatisticheController@albiPerAnno')->name('statistiche.albi_anno');
Route::Get('statistiche/services/get-anni', 'StatisticheController@getAnni')->name('statistiche.get_anni_json');
Route::Get('statistiche/albi-pubblicati-mese-anno/{mese}/{anno}', 'StatisticheController@ListaAlbiMeseAnno')->name('statistiche.get_albi_mese_anno');

Route::get('ricerca', 'RicercaController@index')->name('ricerca.index');
Route::get('ricerca/search', 'RicercaController@search')->name('cerca.search');

Route::get('inserisci', 'InserisciController@index')->name('inserisci.index');