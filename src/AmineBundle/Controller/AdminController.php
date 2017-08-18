<?php

namespace AmineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;



class AdminController extends BaseAdminController
{

    public function createNewUserEntity()
    {
       return $this->get('fos_user.user_manager')->createUser();
	exit;
    }


    /**
     *
     */
    public function prePersistUserEntity($user)
    {
        $this->get('fos_user.user_manager')->updateUser($user, false);
exit;
    }

    
     /**
      *
      */
     public function preUpdateUserEntity($user)
     {
	$this->get('fos_user.user_manager')->updateUser($user, false);
	exit;
     }
}
