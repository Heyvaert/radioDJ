<? Php
/ * ================================================ ============================= * /
// EDIT HIERONDER
/ * ================================================ ============================= * /
$ PageTitle = "Now Playing";

$ NextLimit = 3; // Hoeveel aankomende tracks om te laten zien?
$ ShufleUpcoming = True; // Gebruik de juiste volgorde van de komende nummers niet tonen

$ ResLimit = 5; // Hoeveel geschiedenis tracks om te laten zien?

/ * ================================================ ============================= * /
// END EDIT
/ * ================================================ ============================= * /

require_once (serv_inc.php ');
require_once ( 'header.php');
	
functie convertTime ($ seconden) {
	$ Sec = $ seconden;
    // Time conversie
    $ Uur = intval (intval ($ sec) / 3600);
    $ PadHours = True;
    $ HMS = ($ padHours)
        ? str_pad ($ uren, 2, "0", STR_PAD_LEFT). ':'
        : $ Uur. ':';
    $ Minuten = intval (($ sec / 60)% 60);
    $ HMS. = Str_pad ($ minuten, 2, "0", STR_PAD_LEFT). ':';
    $ Seconden = intval ($ sec% 60);
    $ Hms = str_pad ($ seconden, 2, "0", STR_PAD_LEFT).;

	terug $ HMS;
}

?>

<Table class = "main_table" border = "0" cellspacing = "0" cellpadding = "5">

<? Php

db_conn ();
$ ShuffleQuery = null;

If ($ shufleUpcoming == true) {
	$ ShuffleQuery = "ORDER BY RAND ()";
}

$ Nextquery = "SELECT songs.ID, songs.artist, queuelist.songID van Liederen, queuelist WAAR songs.song_type = 0 en songs.ID = queuelist.songID". $ ShuffleQuery. "LIMIT 0,". $ NextLimit;
$ Resultx = mysql_query ($ nextquery);

if (! $ resultx) {
	echo mysql_error ();
	exit;
}

if (mysql_num_rows ($ resultx)> 0) {
	
	// Als er sporen in de afspeellijst, we laten zien
	$ Inc = 0;

	echo "<tr>". "\ N";
	echo "<td class = \" header_live \ "> Binnenkort op RADIODJ </ td> \ n";
	echo "</ tr>". "\ N";

	echo "<tr>". "\ N";
	echo "<td>";

	while ($ rijx = mysql_fetch_array ($ resultx)) {
		echo htmlspecialchars ($ rijx [ 'kunstenaar'], ENT_QUOTES);
		
		// Als de huidige nummer is niet de laatste, zetten we een separator
		if ($ inc <(mysql_num_rows ($ resultx) -1)) {
			echo ",";
		}
		
		$ Inc + = 1;
	}

	echo "</ td>". "\ N";
	echo "</ tr>". "\ N";
	
} 
/ * 
// Uncomment dit als u graag een bericht te tonen als er geen nummer wordt voorbereid.
else {

	echo "<tr>". "\ N";
	echo "<td class = \" header_live \ "> Binnenkort op RADIODJ </ td> \ n";
	echo "</ tr>". "\ N";
	
	echo "<tr>". "\ N";
	echo "<td> Nothing comming ... </ td> \ n";
	echo "</ tr>". "\ N";
	
}
* /

// // ========================

$-Query = "SELECT` ID`, `date_played`,` artist`, `title`,` duration` FROM `history` WHERE` song_type` = 0 ORDER BY `date_played` DESC LIMIT 0,". ($ ResLimit + 1);

$ Result = mysql_query ($ vraag);

if (! $ result) {
	echo mysql_error ();
	exit;
}

if (mysql_num_rows ($ result) == 0) {
	exit;
}

$ Inc = 0;

while ($ row = mysql_fetch_assoc ($ result)) {
	if ($ inc == 0) {
		echo "<tr>". "\ N";
		echo "<td class = \" header_live \ "> Afspelen </ td> \ n";
		echo "</ tr>". "\ N";

		echo "<tr>". "\ N";
		echo "<td class = \" playing_track \ "> <strong>". htmlspecialchars ($ row [ 'kunstenaar'], ENT_QUOTES). "-". htmlspecialchars ($ row [ 'title'], ENT_QUOTES). "[". convertTime ($ row [ 'duur']). "] </ Strong> </ td> \ n";
		echo "</ tr>". "\ N";

		if ($ resLimit> 0) {
			echo "<tr>". "\ N";
			echo "<td class = \" header_live \ "> recent afgespeelde nummers </ td> \ n";
			echo "</ tr>". "\ N";
		}

	} Else {

		if ($ resLimit> 0) {
			echo "<tr>". "\ N";
			echo "<td>". date ( 'H: i: s', strtotime ($ row [ 'date_played'])). "-". htmlspecialchars ($ row [ 'kunstenaar'], ENT_QUOTES). "-". htmlspecialchars ($ row [ 'title'], ENT_QUOTES). "[". convertTime ($ row [ 'duur']). "] </ Td> \ n";
			echo "</ tr>". "\ N";
		}
	}
	$ Inc + = 1;
}

mysql_free_result ($ result);
db_close ($ opened_db);

?>
</ Table>
<? Php require_once ( 'footer.php'); ?>
