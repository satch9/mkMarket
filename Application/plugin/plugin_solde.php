<?php
/*
This file is part of Mkframework.

Mkframework is free software: you can redistribute it and/or modify
it under the terms of the GNU Lesser General Public License as published by
the Free Software Foundation, either version 3 of the License.

Mkframework is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public License
along with Mkframework.  If not, see <http://www.gnu.org/licenses/>.

*/
/** 
* plugin_solde classe pour generer un solde
* @author Jue vincent
* @link 
*/

class plugin_solde{

	public function getSolde($tObject,$soldeInitiale){

		for ($i=0;$i<count($tObject);$i++){
			unset($tObject[$i][4]);
		}

		$nbreLigne = count($tObject);
		$dernierNumLigne = count($tObject) - 1;		
		$numLigne = $dernierNumLigne - 1;
		$soldeFinal = array();


		//Pour Stpaul il y avait un solde de depart de 11	
		$soldeInitiale = $this->getSoldeInit($soldeInitiale);
		
		$SF = $soldeInitiale + $tObject[$dernierNumLigne][2] - $tObject[$dernierNumLigne][3];
		$tObject[$dernierNumLigne][4] = $SF;
		
		$tObject[$numLigne][4] = $SF;

		
		while($numLigne >= 0){
			
			$SF = $SF + $tObject[$numLigne][2] - $tObject[$numLigne][3];
			$tObject[$numLigne][4] = $SF;
			$numLigne--;
			
		}
		 return $tObject;

	}
	
	private function getSoldeInit($soldeInitiale=null){
		
		if(!$soldeInitiale){
			return null;
		}
		
		$sSI=null;

		if (count($soldeInitiale) == 1) {
			foreach($soldeInitiale as $sKey => $sValue){
			$sSI.=$sKey.'="'.$sValue.'" ';
			
			}

			return $sSI;
		} else {
			$sSI = array_sum($soldeInitiale);
			return $sSI;
		}

		
		
	}
}
?>
