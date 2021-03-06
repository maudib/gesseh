/**
 * This file is part of GESSEH project
 *
 * @author: Pierre-François Angrand <gesseh@medlibre.fr>
 * @copyright: Copyright 2013 Pierre-François Angrand
 * @license: GPLv3
 * See LICENSE file or http://www.gnu.org/licenses/gpl.html
*/

jQuery(document).ready(function() {
  function add_sub($container_id) {
    var $container = $($container_id);
    index = $container.children().length;
    $container.append(
      $($container.attr('data-prototype').replace(/__name__/g, index))
    );
  }

  function rm_sub($container_id) {
    var $container = $($container_id);
    index = $container.children().length;
    $container.children().last().remove();
  }

  $('#add_dpt').click(function() {
    add_sub('#gesseh_corebundle_hospitaltype_departments');
  });
  $('#rm_dpt').click(function() {
    rm_sub('#gesseh_corebundle_hospitaltype_departments');
  })

  $('#add_crit').click(function() {
    add_sub('#gesseh_evaluationbundle_evalformtype_criterias');
  });
  $('#rm_crit').click(function() {
    rm_sub('#gesseh_evaluationbundle_evalformtype_criterias');
  });
});
