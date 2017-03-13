<?php
	$new_otchet = '';
	if (file_exists('1C/Export1C.xml')) {
		$otchet = simplexml_load_file("1C/Export1C.xml");
		$get_file = @file('1C/inv_.txt');

		if($get_file){
			foreach($get_file as $info_sklad){
				$ostatoc = explode( '|' , $info_sklad);
				foreach ( $otchet->{'Номенклатура'} as $key=>$value_product ) {
					if( $ostatoc[1] == iconv('utf-8', 'CP1251', $value_product->{'НоменклатураКодПоставщика'} ) ){
						$new_otchet .= iconv('utf-8', 'CP1251', $value_product->{'НоменклатураНаСкладе'} ) . '|' . $ostatoc[1] .'|' . $ostatoc[2] . '|' . $ostatoc[3] . '|' . $ostatoc[4] . '|' . $ostatoc[5] . PHP_EOL ;
					}else{
						$new_otchet .= $ostatoc[0] . '|' . $ostatoc[1] .'|' . $ostatoc[2] . '|' . $ostatoc[3] . '|' . $ostatoc[4] . '|' . $ostatoc[5] . PHP_EOL ;
					}
				}
			}
		}
	}

	$new_file = fopen('1C/inv_.txt',"w");
	fputs( $new_file, $new_otchet );
	fclose( $new_file );

	header('Content-type: charset=UTF-8');
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
</head>
<body>
<style>
	table{
		font-family:Tahoma;
		font-size:12px;
	}
	table td{
		border:1px solid gray;
		background:#F9F9F9;
		padding:3px;
	}
	.hd{
		background:#D1D1D1;
		font-weight:bold;
	}
	.sv{
		background:yellow;
	}
	#infto{
		background:white;
		border:1px solid black
	}
</style>
<table align="center">
	<tr>
		<td colspan="11" align="center">
			<h1>1C выгрузка 
				<?php
					if (file_exists('1C/Export1C.xml')) {
						$str = simplexml_load_file("1C/Export1C.xml");
						echo '( ' . $str['generated'] . ' )';
					}
				?>	
			</h1>
			Отбор по фабрике: 
				<form class="" action="" method="POST">
					<select onChange="location.href=this.options[this.selectedIndex].value;">
						<option
							<?php
								$all_fabric = array();
								if(isset($_GET['fabrica'])){
									echo '>' . $_GET['fabrica'];
									array_push($all_collection, $_GET['fabrica']);
									echo '</option>';
									echo '<option value="?">';
								}else{
									echo ' value="?">';
								}
							?>
							Все фабрики</option>
						<?php
							foreach ($str->{'Номенклатура'} as $key => $value):
								if(!in_array(strval($value->{'НоменклатураФабрика'}), $all_fabric)):
									array_push($all_fabric, $value->{'НоменклатураФабрика'});
									echo '<option value="?fabrica=' . $value->{'НоменклатураФабрика'} . '">';
									echo $value->{'НоменклатураФабрика'};
									echo '</option>';
								endif;
							endforeach;
						?>
					</select>
				</form>
		<?php if (isset($_GET['fabrica']) && $_GET['fabrica'] !== 'all'): ?>

</br>
</br>

			Отбор по коллекции:
				<form class="" action="" method="POST">
					<select onChange="location.href=this.options[this.selectedIndex].value;">
						<option 
						<?php
							$all_collection = array();
							if(isset($_GET['collection'])){
								echo '>' . $_GET['collection'];
								array_push($all_collection, $_GET['collection']);
								echo '</option>';
								echo '<option value="?fabrica='. $_GET['fabrica'] .'">';
							}else{
								echo ' value="?fabrica='. $_GET['fabrica'] .'">';
							}
						?>
						Все коллекции</option>
						<?php
								foreach ($str->{'Номенклатура'} as $key => $value):
									if(strval($value->{'НоменклатураФабрика'}) == $_GET['fabrica']):
									if(!in_array(strval($value->{'НоменклатураКоллекция'}), $all_collection)):
										array_push($all_collection, $value->{'НоменклатураКоллекция'});
										echo '<option value="?fabrica=' . $_GET['fabrica'] . '&collection=' . $value->{'НоменклатураКоллекция'} .'">';
										echo $value->{'НоменклатураКоллекция'};
										echo '</option>';
									endif;
									endif;
								endforeach;
						?>
					</select>
				</form>
		<?php endif; ?>

</br>
</br>

		</td>
	</tr>
	<tr>
		<td class="hd">Наименование</td>
		<td class="hd">Фабрика</td>
		<td class="hd">Коллекция</td>
		<td class="hd">Код поставщика</td>
		<td class="hd">Единица измерения</td>
		<td class="hd">На складе</td>
		<td class="hd">Резерв</td>
		<td class="hd">Свободно на складе</td>
		<td class="hd">В пути</td>
		<td class="hd">Забронировано в пути</td>
	</tr>
	<?php
		foreach ($str->{'Номенклатура'} as $key => $value):
			if(!isset($_GET[fabrica])){
				echo '<tr>';
				echo '<td>' . $value->{'НоменклатураНаименование'} . '</td>';
				echo '<td>' . $value->{'НоменклатураФабрика'} . '</td>';
				echo '<td>' . $value->{'НоменклатураКоллекция'} . '</td>';
				echo '<td>' . $value->{'НоменклатураКодПоставщика'} . '</td>';
				echo '<td>' . $value->{'НоменклатураЕдИзмерения'} . '</td>';
				echo '<td>' . $value->{'НоменклатураНаСкладе'} . '</td>';
				echo '<td>' . $value->{'НоменклатураРез'} . '</td>';
				echo '<td class="sv">' . $value->{'НоменклатураСвоб'} . '</td>';
				echo '<td class="sv">' . $value->{'НоменклатураИнв'} . '</td>';
				echo '<td>' . $value->{'НоменклатураРеинв'} . '</td>';
				echo '</tr>';
			}else{
				if(!isset($_GET['collection']) && $_GET['fabrica'] == strval($value->{'НоменклатураФабрика'})){
					echo '<tr>';
					echo '<td>' . $value->{'НоменклатураНаименование'} . '</td>';
					echo '<td>' . $value->{'НоменклатураФабрика'} . '</td>';
					echo '<td>' . $value->{'НоменклатураКоллекция'} . '</td>';
					echo '<td>' . $value->{'НоменклатураКодПоставщика'} . '</td>';
					echo '<td>' . $value->{'НоменклатураЕдИзмерения'} . '</td>';
					echo '<td>' . $value->{'НоменклатураНаСкладе'} . '</td>';
					echo '<td>' . $value->{'НоменклатураРез'} . '</td>';
					echo '<td class="sv">' . $value->{'НоменклатураСвоб'} . '</td>';
					echo '<td class="sv">' . $value->{'НоменклатураИнв'} . '</td>';
					echo '<td>' . $value->{'НоменклатураРеинв'} . '</td>';
					echo '</tr>';
				}
				elseif( $_GET['collection'] == strval($value->{'НоменклатураКоллекция'}) && $_GET['fabrica'] == strval($value->{'НоменклатураФабрика'})) {
					echo '<tr>';
					echo '<td>' . $value->{'НоменклатураНаименование'} . '</td>';
					echo '<td>' . $value->{'НоменклатураФабрика'} . '</td>';
					echo '<td>' . $value->{'НоменклатураКоллекция'} . '</td>';
					echo '<td>' . $value->{'НоменклатураКодПоставщика'} . '</td>';
					echo '<td>' . $value->{'НоменклатураЕдИзмерения'} . '</td>';
					echo '<td>' . $value->{'НоменклатураНаСкладе'} . '</td>';
					echo '<td>' . $value->{'НоменклатураРез'} . '</td>';
					echo '<td class="sv">' . $value->{'НоменклатураСвоб'} . '</td>';
					echo '<td class="sv">' . $value->{'НоменклатураИнв'} . '</td>';
					echo '<td>' . $value->{'НоменклатураРеинв'} . '</td>';
					echo '</tr>';
				}
			}
		endforeach;
	?>
</table>
</body>
</html>