<?php 
$oForm=new plugin_form($this->#oExamplemodel#);
$oForm->setMessage($this->tMessage);
?>
<form action="" method="POST" #enctype#>

<table class="tb_new">
	#ici#
	
	<tr>
		<th></th>
		<td>
			<p>
				<input type="submit" value="Ajouter" /> <a href="<?php echo $this->getLink('#examplemodule#::list')?>">Annuler</a>
			</p>
		</td>
	</tr>
</table>

<?php echo $oForm->getToken('token',$this->token)?>

</form>
