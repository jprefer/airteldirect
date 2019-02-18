<?php
$rows = 6;
$str  = '';
while (list($k, $v) = each($_GET)) {
	$str .= $k . '=' . $v . ', ';
}
?>
<table>
<tbody>
<?php
for ($i=0; $i<$rows; $i++) {
?>
<tr>
<td><?= $_GET['pg']; ?>:<?= $i; ?>:1 [GETs: <?= $str; ?>]</td>
<td><?= $_GET['pg']; ?>:<?= $i; ?>:2</td>
<td><?= $_GET['pg']; ?>:<?= $i; ?>:3</td>
<td><?= $_GET['pg']; ?>:<?= $i; ?>:4</td>
<td><?= $_GET['pg']; ?>:<?= $i; ?>:5</td>
<td><?= $_GET['pg']; ?>:<?= $i; ?>:6</td>
<td><?= $_GET['pg']; ?>:<?= $i; ?>:7</td>
</tr>
<?php
}
?>
</tbody>
</table>