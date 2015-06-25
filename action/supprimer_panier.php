<?php

/*
 * Plugin Panier
 * Distribue sous licence GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function action_supprimer_panier_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	
	// suppression
	if ($id_panier = intval($arg)) {
		sql_delete('spip_paniers', 'id_panier=' . intval($id_panier));
		sql_delete('spip_paniers_liens', 'id_panier=' . intval($id_panier));

		// supprimer le panier de la session si c'est le meme
		// ca permet potentiellement de retrouver une session vide (perf issue)
		include_spip('inc/session');
		if ($id_panier==session_get('id_panier')){
			include_spip('inc/paniers');
			paniers_supprimer_panier_en_cours();
		}
	}
}


