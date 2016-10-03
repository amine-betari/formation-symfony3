<?php
// src/OC/PlatformBundle/ParamConverter/JsonParamConverter.php

namespace OC\PlatformBundle\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Request;

class JsonParamConverter implements ParamConverterInterface
{
  // Cette m�thode 	doit retourner true lorsque le convertisseur souhaite convertir le param en question, sinon false
  // $configuration->getClass() : le typage de l'argument dans la m�thode du contr�leur
  // $configuration->getName() : le nom de l'argument dans la m�thode du contr�leur
  // $configuration->getOptions() : les options de l'annotation, si elles sont explicit�es (vide bien sur lorsqu'il n'y a pas l'annotation
  function supports(ParamConverter $configuration)
  {
	// Si le nom de l'argument du contr�leur n'est pas "json", on n'applique pas le convertisseur
	if ('json' !== $configuration->getName()) {
		return false;
	}
	return true;
  }

  
  // Cette m�thode doit cr�er un attribut de requ�te, qui sera inject� dans l'argument de la m�thode du contr�leur
  function apply(Request $request, ParamConverter $configuration)
  {
	
	// On r�cup�re la valeur actuelle de l'attribut
	$json = $request->attributes->get('json');
	// On effectue notre action : le d�coer
	$json = json_decode($json, true);
	if (json_last_error() == JSON_ERROR_NONE) {
		// on met � jour la nouvelle valeur de l'attribut
		$request->attributes->set('json', $json);
	} else {
		throw new BadRequestHttpException('La valeur du paramter n\'est pas au format JSON');
	}
	
  }
}
