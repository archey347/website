<?php
if (isset($_POST['data'])) {
	$data = substr($_POST['data'], 0, 10);

	file_put_contents("data.txt", $data);

	print("Saved!");
}
?>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body>
<form method="POST">
<input style="font-size: 2em;" type="text" name="data">
<br>
<br>
<button style="font-size: 2em;" type="submit">Submit</button>
</form>
</body>
</html>
