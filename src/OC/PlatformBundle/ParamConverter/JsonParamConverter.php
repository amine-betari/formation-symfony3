<?php
// src/OC/PlatformBundle/ParamConverter/JsonParamConverter.php

namespace OC\PlatformBundle\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Request;

class JsonParamConverter implements ParamConverterInterface
{
  // Cette méthode 	doit retourner true lorsque le convertisseur souhaite convertir le param en question, sinon false
  // $configuration->getClass() : le typage de l'argument dans la méthode du contrôleur
  // $configuration->getName() : le nom de l'argument dans la méthode du contrôleur
  // $configuration->getOptions() : les options de l'annotation, si elles sont explicitées (vide bien sur lorsqu'il n'y a pas l'annotation
  function supports(ParamConverter $configuration)
  {
	// Si le nom de l'argument du contrôleur n'est pas "json", on n'applique pas le convertisseur
	if ('json' !== $configuration->getName()) {
		return false;
	}
	return true;
  }

  
  // Cette méthode doit créer un attribut de requête, qui sera injecté dans l'argument de la méthode du contrôleur
  function apply(Request $request, ParamConverter $configuration)
  {
	
	// On récupère la valeur actuelle de l'attribut
	$json = $request->attributes->get('json');
	// On effectue notre action : le décoer
	$json = json_decode($json, true);
	if (json_last_error() == JSON_ERROR_NONE) {
		// on met à jour la nouvelle valeur de l'attribut
		$request->attributes->set('json', $json);
	} else {
		throw new BadRequestHttpException('La valeur du paramter n\'est pas au format JSON');
	}
	
  }
}
