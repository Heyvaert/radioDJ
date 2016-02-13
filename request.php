
Oorspronkelijke tekst
define("REQ_DESCRIPTION", "Please enter request details bellow");
Een betere vertaling bijdragen
<? Php

/ * ================================================ ============================= * /
// EDIT HIERONDER
/ * ================================================ ============================= * /

$ PageTitle = "Song Verzoeken"; //Pagina titel
$ Limit = 25; // Hoeveel aankomende tracks om te laten zien?
$ Doelpagina = $ _SERVER [ 'SCRIPT_NAME']; // Link naar deze pagina
$ ReqLimit = 5; // Beperk aantal aanvragen per IP

// Berichten:
define ( "ERROR_TRACKID", "Selecteer een track om het verzoek te verzenden! <br /> <A HREF=\"javascript:javascript:history.go(-1)\"> Go Back </A>") ;
define ( "ERROR_USERNAME", "Voer uw naam in om het verzoek te verzenden! <br /> <A HREF=\"javascript:javascript:history.go(-1)\"> Go Back </A>") ;
define ( "ERROR_TRACKREQ", "De geselecteerde track is reeds aangevraagd <br /> Probeer het later opnieuw, of selecteer een andere track.!");
define ( "ERROR_LIMITREACHED", "Sorry, maar je hebt het verzoek limiet voor een dag bereikt.");
define ( "ERROR_UNKNOWN", "Onbekende fout opgetreden Probeer het opnieuw ...!");

define ( "MSG_REQSUCCESS", "Uw aanvraag is succesvol geplaatst!");
define ( "MSG_NORESULTS", "Er zijn geen resultaten om weer te geven ...");

define ( "REQ_DESCRIPTION", "Geef verzoek gegevens te loeien");
define ( "REQ_NAME", "Uw Naam:");
define ( "REQ_MESSAGE", "Bericht (optioneel):");
define ( "REQ_BUTTON", "Submit Your Request");

define ( "NAV_NEXT", "NEXT");
define ( "NAV_PREV", "VORIGE");

define ( "SEARCH_TXT", "Zoek artiest of titel:");
define ( "SEARCH_BUTTON", "Search");

define ( "COL_ARTIST", "Artist");
define ( "COL_TITLE", "Titel");
define ( "COL_DURATION", "Duur");
define ( "COL_REQ", "Req");
define ( "ALT_REQ", "Vraag deze track");

/ * ================================================ ============================= * /
// END EDIT
/ * ================================================ ============================= * /

require_once (serv_inc.php ');
require_once ( 'header.php');

date_default_timezone_set ($ def_timezone);

// ============ FUNCTIES ============== //

// Bouwen duur snaar van seconden
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

functioneren getRealIpAddr () {
    if (! leeg ($ _ SERVER [ 'HTTP_CLIENT_IP'])) {
      // Controleren ip-aandeel internet
	  $ Ip = $ _ SERVER [ 'HTTP_CLIENT_IP'];
    }
    elseif (! leeg ($ _ SERVER [ 'HTTP_X_FORWARDED_FOR'])) {
      // Ip controleren is overgaan van proxy
	  $ Ip = $ _ SERVER [ 'HTTP_X_FORWARDED_FOR'];
    } Else {
      $ Ip = $ _ SERVER [ 'REMOTE_ADDR'];
    }
    terug $ ip;
}

// ============ EINDE FUNCTIE ============== //

$ SRCH = ""; // Zoekterm waarde houder
$ Srchpath = ""; // Search houder pad
$ Srcquery = ""; // Zoekopdracht houder
$ Stadia = 3; // Hoe de paginering splitsen
$ Page = 1; // Standaardpagina
$ Reqid = "";

if (isset ($ _ GET [ 'Zoekterm'])) {
	if ($ _ GET [ 'Zoekterm']! = "") {
	
		$ SRCH = mysql_escape_string ($ _ GET [ 'Zoekterm']);
		$ Srchpath = "& searchterm = $ SRCH";
		$ Srcquery = "AND (` artist` LIKE '% $ srch%' of `title` LIKE '% $ srch%')"; // Zoeken artiest of titel
	}
}

// Klik hier voor de pagina als het gevraagde
if (isset ($ _ GET [ 'page'])) {
	$ Page = mysql_escape_string ($ _ GET [ 'page']);
}

if ($ pagina) {
	$ Start = ($ pagina - 1) * $ limiet;
}anders{
	$ Start = 0;
}

if (isset ($ _ POST [ 'reqsubmit'])) {

	/ *
	FOUTCODES:
	0 = geen fout
	1 = geen gebruikersnaam
	2 = nee gevraagd spoor
	3 = spoor al in de wachtrij
	4 = aanvraag limiet bereikt
	* /

	$ Reqname = mysql_escape_string ($ _ POST [ 'requsername']);
	$ Reqmsg = mysql_escape_string ($ _ POST [ 'reqmessage']);
	$ ReqsongID = mysql_escape_string ($ _ POST [ 'songID']);
	$ ReqIP = getRealIpAddr ();
	
	$ Fout = 0;
	$ Reccount = 0;
		
	if (! $ reqname) {$ fout = 1;}
	if (! $ reqsongID) {$ error = 2;}
	
	if ($ error == 0) {
	
		db_conn ();
		
		// Track is reeds aangevraagd?
		$ Controleer = "select count (*) AS num FROM` requests` WHERE `songID` = '$ reqsongID' en` played` = 0; ";
		$ Total_req = mysql_fetch_array (mysql_query ($ hercontrole));
		
		if ($ total_req [ 'num']> 0) {
			$ Fout = 3;
		}
		
		mysql_free_result ($ total_req);
		
		if ($ error == 0) {
			// Gebruiker heeft het verzoek grens bereikt?
			$ Controleer = "select count (*) AS num FROM` requests` WHERE `userIP` = '$ reqIP' AND DATE (` requested`) = DATE (NOW ()); ";
			$ Total_req = mysql_fetch_array (mysql_query ($ hercontrole));
			
			if ($ total_req [ 'num']> = $ reqLimit) {
				$ Fout = 4;
				$ Reccount = $ total_req [ 'num'];
			}
			
			mysql_free_result ($ total_req);
			db_close ($ opened_db);
		}
		
	}
	
	switch ($ error) {
		case 0:
			db_conn ();
			
			$ PatroonX = "INSERT INTO` requests` SET `songID` = '$ reqsongID',` username` = '$ reqname', `userIP` = '$ reqIP',` message` = '$ reqmsg', `requested` = nu();";
			$ Resultx = mysql_query ($ patroonX);
				
			if ($ resultx> 0) {
				echo "<div class = \" noticediv \ ">". MSG_REQSUCCESS. "</ Div> <br />";
			} Else {
				echo "<div class = \" errordiv \ ">". ERROR_UNKNOWN. "</ Div> <br />";
			}
				
			mysql_free_result ($ resultx);
			db_close ($ opened_db);
			
			breken;
		case 1:
			echo "<div class = \" errordiv \ ">". ERROR_USERNAME. "</ Div> <br />";
			breken;
		case 2:
			echo "<div class = \" errordiv \ ">". ERROR_TRACKID. "</ Div> <br />";
			breken;
		case 3:
			echo "<div class = \" errordiv \ ">". ERROR_TRACKREQ. "</ Div> <br />";
			breken;
		case 4:
			echo "<div class = \" errordiv \ ">". ERROR_LIMITREACHED. "(". $ Reccount. '/'. $ ReqLimit. ') ". "</ Div> <br />";
			breken;
	}
	
	
	$ Reqid = "";
}

// Klik hier voor de pagina als het gevraagde
if (isset ($ _ GET [ 'RequestID'])) {

	if ($ _ GET [ 'RequestID']! = "") {
		
			$ Reqid = mysql_escape_string ($ _ GET [ 'RequestID']);
		
			echo "<div class = \" requestcontainer \ "> \ n";
			echo "<form id = \" formrequest \ "name = \" formrequest \ "method = \" post \ "action = \" $ Doelpagina page = $ pagina $ srchpath \? "> \ n";
			echo "<table align = \" center \ "width = \" 500 \ "border = \" 0 \ "cellspacing = \" 0 \ "cellpadding = \" 3 \ "> \ n";
			echo "<tr> \ n";
			echo "<td colspan = \" 2 \ "> <div align = \" center \ "> <p>". REQ_DESCRIPTION. "</ P> </ div> </ td> \ n";
			echo "</ tr> \ n";
			echo "<tr> \ n";
			echo "<td>". REQ_NAME. "</ Td> \ n";
			echo "<td> <input type = \" text \ "name = \" requsername \ "/> </ td> \ n";
			echo "</ tr> \ n";
			echo "<tr> \ n";
			echo "<td valign = \" top \ ">". REQ_MESSAGE. "</ Td> \ n";
			echo "<td> <textarea name = \" reqmessage \ "cols = \" 43 \ "rows = \" 5 \ "> </ textarea> </ td> \ n";
			echo "</ tr> \ n";
			echo "<tr> \ n";
			echo "<td colspan = \" 2 \ "> <div align = \" center \ "> <input type = \ 'Verzenden \" name = \ "reqsubmit \" value = \ "". REQ_BUTTON. "\" / > </ div> </ td> \ n ";
			echo "</ tr> \ n";
			echo "</ table> \ n";
			echo "<INPUT TYPE = \" verborgen \ "name = \" songID \ "value = \" $ reqid \ "> \ n";
			echo "</ form> \ n";
			echo "</ div> \ n";
	}
}

// // ================

if ($ reqid == '') {
	db_conn ();

	// Haal het aantal items
	$ Vraag = "select count (*) als num FROM` nummers `WHERE` enabled` = '1' $ srcquery AND `song_type` = 0";
	$ TOTAL_PAGES = mysql_fetch_array (mysql_query ($ vraag));
	$ TOTAL_PAGES = $ TOTAL_PAGES [ 'num'];

	// Get page data
	$ Query1 = "SELECT` ID`, `artist`,` title`, `duration`,` date_played`, `artist_played` FROM` nummers `WHERE` enabled` = '1' $ srcquery AND`song_type` = 0 ORDER BY `artist` ASC lIMIT $ start, $ limit";
	$ Result = mysql_query ($ query1);
		
	// Eerste pagina num setup
	if ($ pagina == 0) {$ page = 1;}
	$ Prev = $ pagina - 1;
	$ Next = $ pagina + 1;
	$ LaatstePagina = ceil ($ TOTAL_PAGES / $ limiet);
	$ LastPagem1 = $ LaatstePagina - 1;

	$ Paginate = '';

	if ($ LaatstePagina> 1) {	
		$ Paginate = "<div class = 'paginate'>.";
		// Vorige
		if ($ pagina> 1) {
			$ Paginate. = "<a Href='$targetpage?page=$prev$srchpath'>". NAV_NEXT. "</a>";
		}anders{
			$ Paginate. = "<Span class = 'disabled'> '. NAV_PREV. "</ Span>";
		}
		
		// Pages
	
		if ($ LaatstePagina <7 + ($ stadia * 2)) {	
			voor ($ counter = 1; $ teller <= $ LaatstePagina; $ teller ++) {
				if ($ teller == $ pagina) {
					$ Paginate = "<span class = 'huidige'> $ teller </ span>.";
				}anders{
					$ Paginate = "<a href='$targetpage?page=$counter$srchpath'> $ teller </a>.";
				}					
			}
		} Elseif ($ LaatstePagina> 5 + ($ stadia * 2)) {
		
		// Begin alleen te verbergen later pagina's
			if ($ pagina <1 + ($ stadia * 2)) {
				voor ($ counter = 1; $ teller <4 + ($ stadia * 2); $ teller ++) {
					if ($ teller == $ pagina) {
						$ Paginate = "<span class = 'huidige'> $ teller </ span>.";
					}anders{
						$ Paginate = "<a href='$targetpage?page=$counter$srchpath'> $ teller </a>.";
					}					
				}
			
				$ Paginate = "...".
				$ Paginate = "<a href='$targetpage?page=$LastPagem1$srchpath'> $ LastPagem1 </a>.";
				$ Paginate = "<a href='$targetpage?page=$lastpage$srchpath'> $ LaatstePagina </a>.";
				
			} Elseif ($ LaatstePagina - ($ etappes * 2)> $ pagina && $ pagina> ($ stadia * 2)) {
			
				$ Paginate = "<a href='$targetpage?page=1$srchpath'> 1 </a>.";
				$ Paginate = "<a href='$targetpage?page=2$srchpath'> 2 </a>.";
				$ Paginate = "...".
				
				voor ($ counter = $ pagina - $ fasen; $ teller <= $ pagina + $ fasen; $ teller ++) {
					if ($ teller == $ pagina) {
						$ Paginate = "<span class = 'huidige'> $ teller </ span>.";
					}anders{
						$ Paginate = "<a href='$targetpage?page=$counter$srchpath'> $ teller </a>.";
					}
				}
				
				$ Paginate = "...".
				$ Paginate = "<a href='$targetpage?page=$LastPagem1$srchpath'> $ LastPagem1 </a>.";
				$ Paginate = "<a href='$targetpage?page=$lastpage$srchpath'> $ LaatstePagina </a>.";

			} Else {
			
				$ Paginate = "<a href='$targetpage?page=1$srchpath'> 1 </a>.";
				$ Paginate = "<a href='$targetpage?page=2$srchpath'> 2 </a>.";
				$ Paginate = "...".
				
				voor ($ counter = $ LaatstePagina - (2 + ($ stadia * 2)); $ teller <= $ LaatstePagina; $ teller ++) {
					if ($ teller == $ pagina) {
						$ Paginate = "<span class = 'huidige'> $ teller </ span>.";
					}anders{
						$ Paginate = "<a href='$targetpage?page=$counter$srchpath'> $ teller </a>.";
					}
				}
			}
		}
					
		// Next
		if ($ pagina <$ teller - 1) { 
			$ Paginate. = "<a Href='$targetpage?page=$next$srchpath'>". NAV_NEXT. "</a>";
		}anders{
			$ Paginate. = "<Span class = 'disabled'> '. NAV_NEXT. "</ Span>";
		}	
		$ Paginate = "</ div>.";
	}

	// Zoekvak
	echo '<div align = "center">';
	echo "<form name = \" input \ "action = \" $ Doelpagina \ "method = \" get \ ">";
	echo SEARCH_TXT. "<Input type = \" text \ "value = \" $ SRCH \ "name = \" searchterm \ "> <input type = \" submit \ "value = \" "SEARCH_BUTTON" \ "..>";
	echo '<br />';
	echo '</ form>';
	echo '</ div>';

	if ($ TOTAL_PAGES> 0) {

		// Voeg de paginering
		echo "<div align = \" center \ "> $ paginate </ div> <br />";

		// Resultaten tafel
		echo '<table class = "main_table" border = "0" cellspacing = "0" cellpadding = "5">';
		echo "<tr>". "\ N";
		echo "<td class = \" header_no \ "> # </ td> \ n";
		echo "<td class = \" header_live \ ">". COL_ARTIST. "</ Td> \ n";
		echo "<td class = \" header_live \ ">". COL_TITLE. "</ Td> \ n";
		echo "<td class = \" header_live \ ">". COL_DURATION. "</ Td> \ n";
		echo "<td class = \" header_live \ ">". COL_REQ. "</ Td> \ n";
		echo "</ tr>". "\ N";

		$ Cnt = 1 + ($ limit * $ pagina) - $ limiet; // Resultaten teller

		// Resultaten toe aan de tafel
		while ($ row = mysql_fetch_assoc ($ result)) {
			echo "<tr>". "\ N";
			echo "<td class = \" entry_no \ "> $ CNT </ td> \ n.";
			echo "<td>". $ Row [ 'artist']. "</ Td> \ n";
			echo "<td>". $ Row [ 'title']. "</ Td> \ n";
			echo "<td class = \" entry_no \ ">". convertTime ($ row [ 'duur']). "</ Td> \ n";

			if (track_can_play ($ row [ 'date_played'], $ row [ 'artist_played']) == true) {
				echo "<td class = \" entry_no \ "> <a href = \" $ Doelpagina? page = $ pagina & RequestID = ". $ row [ 'ID']." \ "title = \" ". ALT_REQ." \ " >
				<Img src = \ "images / add.png \" alt = \ "". ALT_REQ. "\" /> </a> </ Td> \ n ";
			}anders{
				echo "<td class = \" entry_no \ "> <img src = \" images / delete.png \ "/> </ td> \ n";
			}
			
			echo "</ tr>". "\ N";
			$ Cnt ++;
		}
		
		mysql_free_result ($ result);
		db_close ($ opened_db);
?>

</ Table>
<br />

<? Php

		// Voeg de bodem paginering
		echo '<div align = "center">'. $ Paginate. '</ Div>';
	}anders{
		echo "<div class = \" errordiv \ ">". MSG_NORESULTS. "</ Div>";
	}
}
	require_once ( 'footer.php');
?>
