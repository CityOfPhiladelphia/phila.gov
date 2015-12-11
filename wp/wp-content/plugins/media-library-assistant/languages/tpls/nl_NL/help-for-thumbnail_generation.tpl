<!-- loaded in class-mla-main.php function mla_add_help_tab for the Media/Assistant submenu screen -->
<!-- invoked as /wp-admin/upload.php?page=mla-menu -->
<!-- template="mla-thumbnail-generation" -->
<!-- title="Genereer Thumbnails" order="95" -->
<p>Via Media Library Assistant kunt een "Gekoppelde Afbeelding" aan uw Media Bibliotheek items toekennen. Voor niet-afbeelding items als bijvoorbeeld PDF documenten kan deze afbeelding gebruikt worden als de <code>mla_viewer</code> thumbnail afbeelding, waarmee de overhead vermeden wordt dat deze afbeelding telkens gegenereerd moet worden wanneer de galerij wordt behandeld. De "Thumbnail" Actie maakt het makkelijk om thumbnails te genereren en deze toe te kennen aan hun bijbehorende niet-afbeelding Media Bibliotheek items.</p>
<p>U kunt de volgende velden gebruiken om invloed uit te oefenen op het aanmaakproces:</p>
<table>
<tr>
<td style="padding-right: 10px; vertical-align: top; font-weight:bold">Breedte</td>
<td>de maximale breedte in pixels (default "150") van de thumbnail afbeelding. De hoogte (tenzij ook geselecteerd) zal aangepast worden opdat de pagina proporties gehandhaafd blijven.</td>
</tr>
<tr>
<td style="padding-right: 10px; vertical-align: top; font-weight:bold">Hoogte</td>
<td>de maximale hoogte in pixels (default "0") van de thumbnail afbeelding. De breedte (tenzij ook geselecteerd) zal aangepast worden opdate de  pagina proporties gehandhaafd blijven.</td>
</tr>
<tr>
<td style="padding-right: 10px; vertical-align: top; font-weight:bold">Best Passend</td>
<td>behoud pagina proporties als zowel hoogte als breedte opgegeven zijn. Wanneer niet geselecteerd zal de afbeelding uitgerekt worden tot precies de hoogte en breedte die opgegeven zijn. Wanneer geselecteerd zal de afbeelding kleiner gemaakt worden om binnen de opgegeven waarden te blijven waarbij de proporties behouden blijven. Bijvoorbeeld, een typische pagina is 612 pixels breed en 792 pixels hoog. Als u de breedte en hoogte op 300 zet and best passend kiest, zal de thumbnail verkleind worden naar 231 pixels breed en 300 pixels hoog.</td>
</tr>
<tr>
<td style="padding-right: 10px; vertical-align: top; font-weight:bold">Pagina</td>
<td>het paginanummer (default "1") voor de thumbnail afbeelding. Als de pagina niet bestaat zal the allereerste pagina gebruikt worden.</td>
</tr>
<tr>
<td style="padding-right: 10px; vertical-align: top; font-weight:bold">Resolutie</td>
<td>de pixels/inch resolutie (default 72) van de pagina voor het verkleinen. Als u dit een hogere waarde geeft, zoals 300, verbetert u de kwaliteit van de thumbnail ten koste van een verhoging van de verwerkingstijd.</td>
</tr>
<tr>
<td style="padding-right: 10px; vertical-align: top; font-weight:bold">Kwaliteit</td>
<td>de compressie kwaliteit (default 90) van de uiteindelijke pagina. U kunt dit een waarde tussen 1 en 100 geven om kleinere bestanden te krijgen ten koste van afbeeldingskwaliteit; 1 is kleinst/slechtst en 100 is grootst/beste.</td>
</tr>
<tr>
<td style="padding-right: 10px; vertical-align: top; font-weight:bold">Type</td>
<td>het MIME type, "JPG" (image/jpeg, default) of "PNG" (image/png), van de uiteindelijke thumbnail. U kunt dit, bijvoorbeeld, de waarde "PNG" geven om een transparante achtergrond the behouden in plaats van een witte jpeg achtergrond.</td>
</tr>
<tr>
<td style="padding-right: 10px; vertical-align: top; font-weight:bold">Bestaande Items</td>
<td>de actie die genomen moet worden als een item al een thumbnail heeft. Selecteer "<strong>Behoud</strong>" om de thumbnail te behouden en niets te genereren. Selecteer "<strong>Negeer</strong>" om gewoon te genereren en de nieuwe thumbnail te koppelen, waarbij het oude item ongewijzigd blijft. Selecteer "<strong>Prullenbak</strong>" om een nieuwe thumnbail te genereren en het oude item in de Media Prullenbak (wanneer gedefinieerd) te zetten of in zijn geheel te verwijderen. Selecteer "<strong>Verwijder</strong>" om een nieuwe thumbnail te genereren en te koppelen terwijl het oude item volledig verwijderd wordt.
</td>
</tr>
<tr>
<td style="padding-right: 10px; vertical-align: top; font-weight:bold">Suffix</td>
<td>de suffix (achtervoegsel) welke toegevoegd wordt aan de Titel van de bron om de Titel van de thumbnail te maken.</td>
</tr>
<tr>
<td style="padding-right: 10px; vertical-align: top; font-weight:bold">Schoon Filter</td>
<td>verwijder de "Filter" criteria als de display ververst wordt. Het laten staan van criteria als jaar/maand of Zoek Media waarden kan het tonen van nieuwe gegenereerde items blokkeren.</td>
</tr>
</table>
<p>Nadat u de Genereer Thumbnails knop geklikt heeft, wordt het Media/Assistant submenu ververst om alle nieuwe items te tonen die zijn gegenereerd en toegevoegd aan de Media Bibliotheek. U kunt Snel Wijzigen en Acties gebruiken om aanvullende wijzigingen aan te brengen aan de nieuwe items.</p>
<!-- template="sidebar" -->
<p><a href="[+settingsURL+]?page=mla-settings-menu-documentation&mla_tab=documentation#thumbnail_substitution" target="_blank">MLA Documentatie over Thumbnail Substitutie Ondersteuning, mla_viewer</a></p>
