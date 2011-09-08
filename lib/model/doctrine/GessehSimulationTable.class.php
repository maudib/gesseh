<?php

/**
 * GessehSimulationTable
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class GessehSimulationTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object GessehSimulationTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('GessehSimulation');
    }

    /* Simulation des choix en utilisant une table temporaire */
    public function simulChoixPager($debut, $fin)
    {
      $postes = $this->updateTerrains(Doctrine::getTable('GessehTerrain')->getActiveTerrainTbl(), $debut);

      $q = Doctrine_Query::create()
        ->from('GessehSimulation a')
        ->leftJoin('a.GessehEtudiant b')
        ->leftJoin('b.GessehChoix c')
//        ->leftJoin('c.GessehTerrain d')
        ->where('a.absent = ?', false)
        ->andWhere('a.id >= ?', $debut)
        ->andWhere('a.id <= ?', $fin)
        ->andWhere('c.ordre > ?', '0')
        ->orderBy('a.id asc, c.ordre asc');

      $resultats = $q->execute();
//       $resultats = $q->fetchArray();

      foreach ($resultats as $resultat) {
        $etudiant = $resultat->getGessehEtudiant();
        $resultat->setPoste(null);
        $resultat->setReste(null);
        if (null !== $etudiant->getGessehChoix()) {
          foreach ($etudiant->getGessehChoix() as $choix) {
            if ($resultat->getPoste() == null and $postes[$choix->getPoste()] > 0){
              $postes[$choix->getPoste()]--;
              $resultat->setPoste($choix->getPoste());
              $resultat->setReste($postes[$choix->getPoste()]);
            }
          }
        }
      }

      $resultats->save();
    }

    /* Mise à jour du tableau de postes restants avec les étapes précédentes de la simulation */
    public function updateTerrains($postes, $limite = null)
    {
      foreach ($postes as $poste=>$total) {
        $q = Doctrine_Query::create()
          ->from('GessehSimulation a')
          ->select('a.reste')
          ->where('a.poste = ?', $poste)
          ->orderBy('a.reste asc')
          ->limit(1);

        if ($limite)
          $q->andWhere('a.id < ?', $limite);

        if ($reste = $q->fetchOne())
          $postes[$poste] = $reste->getReste();
      }

      return $postes;
    }

    /* Retourne le rang du dernier étudiant de la simulation */
    public function getMaxEtudiant()
    {
      $max_id = Doctrine_Query::create()
        ->from('GessehSimulation a')
        ->select('a.id')
        ->where('a.absent = ?', false)
        ->orderBy('a.id desc')
        ->limit(1)
        ->fetchOne();

      return $max_id->getId();
    }

    /* Retourne le résultat de la simulation pour l'étudiant */
    public function getSimulEtudiant($etudiant)
    {
      $q = Doctrine_Query::create()
        ->from('GessehSimulation a')
        ->leftJoin('a.GessehTerrain b')
        ->where('a.etudiant = ?', $etudiant)
        ->limit(1);

      return $q->fetchOne();
    }

    /* Retourne le nombre d'étudiant n'ayant pas un choix validé */
    public function getAbsents($etudiant = null)
    {
      $q = Doctrine_Query::create()
        ->from('GessehSimulation a')
        ->where('a.poste = ?', null)
        ->andWhere('a.absent = ?', false);

      if($etudiant)
        $q->andWhere('a.etudiant < ?', $etudiant); // Attention : a.id < etudiant->simulation->id

      $absents = $q->execute();

      return $absents->count();
    }

    /* Vide la table de simulation */
    public function cleanSimulTable()
    {
      $q = Doctrine_Query::create()
        ->delete()
        ->from('GessehSimulation')
        ->execute();

      return $q;
    }

    /* Définit la table de simulation */
    public function setSimulOrder()
    {
      $etudiants = Doctrine::getTable('GessehEtudiant')->getEtudiantsOrderByClassement();
      $count = 1;

      foreach ($etudiants as $etudiant) {
        $simulation = new GessehSimulation();
        $simulation->setId($count);
        $simulation->setEtudiant($etudiant->getId());
        $simulation->save();
        $count++;
      }

      $count--;
      return $count;
    }

    /* Copie la table de simulation dans la table de stage */
    public function saveSimulTable($periode)
    {
      $simulations = Doctrine_Query::create()
        ->from('GessehSimulation a')
        ->where('a.poste != ?', 'null')
        ->fetchArray();

      foreach($simulations as $simulation) {
//        die(print_r($simulation));
        $stage = new GessehStage();
        $stage->setEtudiantId($simulation['etudiant']);
        $stage->setPeriodeId($periode);
        $stage->setTerrainId($simulation['poste']);
        $stage->setForm(1);
        $stage->save();
      }
    }
}
