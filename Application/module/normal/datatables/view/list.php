<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/s/dt/dt-1.10.10/datatables.min.css"/>

<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
 
<script type="text/javascript" src="https://cdn.datatables.net/s/dt/dt-1.10.10/datatables.min.js"></script>

<table id="<?php echo $this->idTable?>" class="display" cellspacing="0" width="100%">
	<thead>
	<tr>
		<?php foreach($this->tHeader as $tDetail):?>
		<th><?php echo $tDetail['label']?></th>
		<?php endforeach;?>
	</tr>
	</thead>
	<tbody>

	</tbody>
</table>


<script>

$(document).ready(function() {
    $('#<?php echo $this->idTable?>').DataTable( {
	"pageLength":<?php echo $this->iLimit?>,
        "processing": true,
        "serverSide": true,
        "ajax": "<?php echo _root::getLink($this->sJsonLink)?>",
        "lengthMenu":<?php echo json_encode($this->tLimit)?>,
        'searching':false,
    } );
} );


</script>