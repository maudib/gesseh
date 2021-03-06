/**
 * This file is part of GESSEH project
 *
 * @author: Pierre-François Angrand <gesseh@medlibre.fr>
 * @copyright: Copyright 2013 Pierre-François Angrand
 * @license: GPLv3
 * See LICENSE file or http://www.gnu.org/licenses/gpl.html
*/

$(document).ready(function() {
  $('.confirm').click(function() {
    var msg;
    if($(this).attr("confirm")){ msg=$(this).attr("confirm"); }else{ msg="Attention cette opération va supprimer définitivement l'élément ainsi que ceux qui en dépendent !\n\nSi vous rencontrez un message d'erreur, c'est probablement que certains de ces éléments liés ne peuvent être supprimés automatiquement. Vous devrez le faire manuellement.\n\nVoulez-vous vraiment continuer ?"; }
    if(confirm(msg)) {
      return true;
    } else {
      return false;
    }
  });
});
