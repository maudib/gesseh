<?php


class GessehCritereTable extends Doctrine_Table
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('GessehCritere');
    }

    public function getCriteres($formulaire)
    {
      $q = Doctrine::getTable('GessehCritere')
      ->createQuery('a')
      ->where('form = ?', $formulaire);
      
      return $q->execute();
    }

}

?>
