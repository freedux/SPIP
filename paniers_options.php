<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// À chaque hit en partie publique, on va chercher le panier du visiteur actuel si il en a un
// on ne fait rien sur les hits visiteurs anonymes, bots, cron, etc...
if (isset($_COOKIE[$GLOBALS['cookie_prefix'].'_panier'])
  OR (isset($GLOBALS['visiteur_session']['id_panier']) AND $GLOBALS['visiteur_session']['id_panier'])
  OR (isset($GLOBALS['visiteur_session']['id_auteur']) AND $GLOBALS['visiteur_session']['id_auteur'])){

	// verifier/mettre a jour l'existence d'un panier en cours
	include_spip('inc/paniers');
	$id_panier = paniers_id_panier_encours();

}


/**
 * Calculer rapidement le nombre de produits dans un panier
 * @param $id_panier
 * @return int|number
 */
function paniers_nombre_produits($id_panier){
	$quantite = intval(sql_getfetsel("SUM(quantite)","spip_paniers_liens","id_panier=".intval($id_panier)));
	return $quantite;
}