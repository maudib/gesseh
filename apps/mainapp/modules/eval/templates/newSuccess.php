<div class="content">
  <strong>Légende :</strong>
  <ol class="infos" start="0">
    <li>0 : Non fait au cours du stage ; </li>
    <li>1 : Mauvais ; </li>
    <li>2 : Insuffisant ; </li>
    <li>3 : Correct ; </li>
    <li>4 : Bon ; </li>
    <li>5 : Excellent</li>
  </ol>
</div>

<form
  action="<?php echo url_for('eval/create?idstage='.$gesseh_stage->getId()) ?>" 
  method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>

  <table class="form">
    <tfoot>
      <tr>
        <td>
	  <a href="<?php echo url_for('etudiant/index') ?>">Retour à la liste des stages</a>
	</td>
	<td>
	  <input type="submit" value="Enregistrer" />
	</td>
      </tr>
    </tfoot>
    <tbody>
        <?php echo $form ?>
    </tbody>
  </table>
</form>
