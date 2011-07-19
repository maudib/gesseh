<?php


class GessehEtudiantTable extends Doctrine_Table
{

    /* Magic Method : Récupère tous les étudiants */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('GessehEtudiant');
    }

    /* Récupère tous les étudiants */
    public function retrieveEtudiant(Doctrine_Query $q)
    {
      $rootAlias = $q->getRootAlias();
      $q->leftJoin($rootAlias.'.GessehPromo c')
        ->leftJoin($rootAlias.'.sfGuardUser d')
        ->orderBy('c.ordre asc, d.last_name asc, d.first_name asc');

      return $q;
    }

    /* Depreciated : Vérification du jeton de validation d'adresse email */
    public function validTokenMail($user, $mailtmp)
    {
      $q = Doctrine_Query::create()
        ->from('GessehEtudiant a')
        ->select('a.token_mail')
        ->where('a.id = ?', $user)
        ->fetchOne();

      if ($mailtmp === sha1($user.$q->getTokenMail()))
      {
        $q2 = Doctrine_Query::create()
          ->update('GessehEtudiant a')
          ->set('a.token_mail', '?', '')
          ->set('a.email', '?', $q->getTokenMail())
          ->where('a.id = ?', $user);
        return $q2->execute();
      }
      else
        return false;
    }

    /* Récupère les étudiants des promos actives */
    public function retrieveActiveEtudiant(Doctrine_Query $q)
    {
      $r = $this->retrieveEtudiant($q);
      $rootAlias = $r->getRootAlias();
      $r->where('c.active = ?', '1');

      return $r;
    }

    /* Depreciated : Vérification de la validation de l'adresse email */
    public function checkValidMail($user)
    {
      $q = Doctrine_Query::create()
        ->from('GessehEtudiant a')
        ->select('a.email, a.token_mail')
        ->where('a.id = ?', $user)
        ->limit(1)
        ->fetchOne();

      if (!$q->getEmail() or $q->getTokenMail())
        return false;
      else
        return true;
    }

    /* Change la promo de tous les étudiants d'une promo */
    public static function changePromo($promo_depart, $promo_arrivee)
    {
      $q = Doctrine_Query::create()
        ->update('GessehEtudiant a')
        ->set('a.promo_id', '?', $promo_arrivee)
        ->where('a.promo_id = ?', $promo_depart);

      return $q->execute();
    }

    /* Change la promo d'un étudiant pour 'hors promo' */
    public function changeHorsPromo($etudiant)
    {
      $q = Doctrine_Query::create()
        ->update('GessehEtudiant a')
        ->leftJoin('a.sfGuardUser b')
        ->set('a.promo_id', '?', Doctrine::getTable('GessehPromo')->findOneByActive(false))
        ->set('b.is_active', '?', false)
        ->where('a.id = ?', $etudiant);

      return $q->execute();
    }

    /* Depreciated : Import d'un étudiant depuis un fichier MsExcel */
    public static function importFichier($fichier)
    {
      $promo = Doctrine_query::create()
        ->from('GessehPromo a')
        ->where('a.ordre = ?', '1')
        ->limit(1)
        ->fetchOne();

      $data = new sfExcelReader($fichier);

//      echo $data->dump(true, true);
      for($i = 1 ; $i <= $data->rowcount($sheet_index=0) ; $i++)
      {
        $etudiant = new GessehEtudiant();
        $date = explode('/', $data->val($i, csSettings::get('excelrownumber_promo_naissance')));
        $etudiant->setNaissance('19'.$date[2].'-'.$date[1].'-'.$date[0]);
        $etudiant->setPromoId($promo->getId());
        $etudiant->save();

        $user = new sfGuardUser();
        $user->setUsername($etudiant->getId());
        $user->setFirstName($data->getCellByColumnAndRow(csSettings::get('etudiant_prenom'), $i));
        $user->setLastName($data->getCellByColumnAndRow(csSettings::get('etudiant_nom'), $i));
        $user->setEmailAddress($data->getCellByColumnAndRow(csSettings::get('etudiant_mail'), $i));
        $user->setPassword($date[0].$date[1].'19'.$date[2]);
        $user->setIsActive(1);
        $user->setIsSuperAdmin(0);
//      $user->save();
        $user->addGroupByName('etudiant');
        $user->addPermissionByName('etudiant');
        $user->save();
      }

      return $data->rowcount($sheet_index=0);
    }

    /* Retourne la liste d'étudiants avec les résultats de la simulation */
    public function getListeQuery($promo)
    {
      $q = Doctrine_Query::create()
        ->from('CopisimEtudiant a')
        ->leftJoin('a.CopisimPromo b')
        ->leftJoin('a.CopisimSimulation c')
        ->leftJoin('c.CopisimTerrain d')
//        ->leftJoin('d.CopisimHopital e')
        ->leftJoin('a.sfGuardUser g')
        ->where('a.promo = ?', $promo)
        ->orderBy('a.classement asc');

      return $q;
    }

    /* Retourne la requête pour la liste des étudiants avec leur promo */
    public function getListeAdmin(Doctrine_Query $q)
    {
      $rootAlias = $q->getRootAlias();
      $q->leftJoin($rootAlias . '.CopisimPromo c');

      return $q;
    }

    /* Retourne la liste des étudiants par ordre de classement */
    public function getEtudiantsOrderByClassement()
    {
      $q = Doctrine_Query::create()
        ->from('GessehEtudiant a')
        ->leftJoin('a.GessehPromo b')
        ->where('b.active = ?', true)
        ->orderBy('b.ordre desc, a.annee_promo asc, a.classement asc');

      return $q->execute();
    }

    /* Retourne la liste des étudiants des promos actives par ordre alphabétique */
    public function getActiveEtudiantsOrderByName()
    {
      $q = Doctrine_Query::create()
        ->from('GessehEtudiant a')
        ->leftJoin('a.GessehPromo b')
        ->leftJoin('a.sfGuardUser c')
        ->where('b.active = ?', true)
        ->orderBy('c.last_name asc, c.first_name asc');

      return $q;
    }
}
