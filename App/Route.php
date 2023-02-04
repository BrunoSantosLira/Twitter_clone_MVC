<?php

namespace App;

use MF\Init\Bootstrap;

class Route extends Bootstrap {

	protected function initRoutes() {

		$routes['home'] = array(
			'route' => '/',
			'controller' => 'indexController',
			'action' => 'index'
		);

		$routes['inscreverse'] = array(
			'route' => '/inscreverse',
			'controller' => 'indexController',
			'action' => 'inscreverse'
		);

		$routes['registrar'] = array(
			'route' => '/registrar',
			'controller' => 'indexController',
			'action' => 'registrar'
		);

		$routes['autenticar'] = array(
			'route' => '/autenticar',
			'controller' => 'AuthController',
			'action' => 'autenticar'
		);

		$routes['timeline'] = array(
			'route' => '/timeline',
			'controller' => 'AppController',
			'action' => 'timeline'
		);

		$routes['sair'] = array(
			'route' => '/sair',
			'controller' => 'AuthController',
			'action' => 'sair'
		);
		
		$routes['tweet'] = array(
			'route' => '/tweet',
			'controller' => 'AppController',
			'action' => 'tweet'
		);

		$routes['quemSeguir'] = array(
			'route' => '/quemSeguir',
			'controller' => 'AppController',
			'action' => 'quemSeguir'
		);

		$routes['acao'] = array(
			'route' => '/acao',
			'controller' => 'AppController',
			'action' => 'acao'
		);

		$routes['remover_tweet'] = array(
			'route' => '/remover_tweet',
			'controller' => 'AppController',
			'action' => 'remover_tweet'
		);

		$routes['foto_edit'] = array(
			'route' => '/foto_edit',
			'controller' => 'AppController',
			'action' => 'foto_edit'
		);

		$routes['foto_edit2'] = array(
			'route' => '/foto_edit_ajax',
			'controller' => 'AJAXController',
			'action' => 'foto_edit'
		);

		
		

	





		$this->setRoutes($routes);
	}

}

?>