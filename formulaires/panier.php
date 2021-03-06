<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


function formulaires_panier_charger($id_panier=0){

	// On commence par chercher le panier du visiteur actuel s'il n'est pas fourni a l'appel du formulaire
	include_spip('inc/paniers');
	if (!$id_panier) $id_panier = paniers_id_panier_encours();
	
	$contexte = array(
		'_id_panier' => $id_panier,
		'quantites' => array()
	);
	
	return $contexte;
}

function formulaires_panier_verifier($id_panier=0){
	$erreurs = array();

	if (!_request('vider')){
		$quantites = _request('quantites');

		if (is_array($quantites)){
			foreach ($quantites as $objet => $objets_de_ce_type){
				foreach ($objets_de_ce_type as $id_objet => $quantite){
					if (!is_numeric($quantite) or $quantite!=intval($quantite) or (is_numeric($quantite) and $quantite<0)){
						$erreurs['message_erreur'] = _T('paniers:panier_erreur_quantites');
						$erreurs['quantites'][$objet][$id_objet] = 'erreur';
					}
				}
			}
		}
	}

	return $erreurs;
}

function formulaires_panier_traiter($id_panier=0){
	$retours = array();
	
	// On commence par chercher le panier du visiteur actuel s'il n'est pas fourni a l'appel du formulaire
	include_spip('inc/paniers');
	if (!$id_panier) $id_panier = paniers_id_panier_encours();

	if (_request('vider')){
		$supprimer_panier = charger_fonction("supprimer_panier","action");
		$supprimer_panier($id_panier);
	}
	else {
		$quantites = _request('quantites');
		$ok = 0;

		if (is_array($quantites))
			foreach($quantites as $objet => $objets_de_ce_type)
				foreach($objets_de_ce_type as $id_objet => $quantite){
					$quantite = intval($quantite);
					// Si la quantite est 0, on supprime du panier
					if (!$quantite)
						$ok += sql_delete(
							'spip_paniers_liens',
							'id_panier = '.intval($id_panier).' and objet = '.sql_quote($objet).' and id_objet = '.intval($id_objet)
						);
					// Sinon on met à jour
					else{
						if ($quantite!=sql_getfetsel("quantite","spip_paniers_liens",'id_panier = '.intval($id_panier).' and objet = '.sql_quote($objet).' and id_objet = '.intval($id_objet))){
							$ok += sql_updateq(
								'spip_paniers_liens',
								array('quantite' => $quantite),
								'id_panier = '.intval($id_panier).' and objet = '.sql_quote($objet).' and id_objet = '.intval($id_objet)
							);
						}
					}
				}
		// Mais dans tous les cas on met la date du panier à jour
		sql_updateq(
			'spip_paniers',
			array('date'=>date('Y-m-d H:i:s')),
			'id_panier = '.intval($id_panier)
		);
		if ($ok){
			$retours['message_ok'] = _T('paniers:panier_quantite_ok');
		}

	}

	return $retours;
}

?>
