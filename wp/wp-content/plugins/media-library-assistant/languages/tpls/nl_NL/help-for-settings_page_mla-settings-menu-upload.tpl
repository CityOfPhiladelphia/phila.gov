<!-- loaded in class-mla-settings.php function mla_add_help_tab_action for the Settings/Media Library Assistant submenu Uploads tab -->
<!-- invoked as /wp-admin/options-general.php?page=mla-settings-menu-upload&mla_tab=upload -->
<!-- template="mla-overview" -->
<!-- title="Overzicht" order="10" -->
<p>Via de Uploads tab kunt u de lijst met de "Upload MIME Typen" beheren. Deze lijst wordt gebruikt om de upload van bestandsformaten naar de Media Bibliotheek te valideren en MIME typen toe te kennen gebaseerd op bestandsextensies.</p>
<p>Alle Upload MIME Typen zijn opgesomd aan de rechterkant van de tabel, gesorteerd op het bestandsextensie veld. U kunt de volgorde wijzigen door op &eacute;&eacute;n van de blauwe kolomnamen te klikken.</p>
<p>U kunt de Scherminstellingen tab gebruiken om het tonen van het scherm te be&iuml;nvloeden. U kunt elke combinatie van kolommen kiezen die getoond worden. U kunt ook kiezen hoeveel items verschijnen op elke pagina van het scherm.</p>
<p>De tabel kan gefilterd worden door het klikken van &eacute;&eacute;n van de "views" getoond boven de Acties selectie. U kunt kiezen uit Alle items, Actieve of Inactieve items of de bron (WordPress, MLA of Custom (=Aangepast)) van het item.</p>
<p>U kunt ook de lijst verkleinen door een sleutelwoord of deeltekst in de tekst box in de rechterbovenhoek te toetsen en vervolgens "Zoek Uploads" te klikken. <strong>OPMERKING:</strong> Het "Zoek Uploads" filter is &#8220;sticky&#8221;, d.w.z., het zal blijven bestaan als u het scherm hersorteert, items edit, etc. Om de waarde te schonen, verwijder de tekst en klik "Zoek Uploads" of klik op de "Uploads" tab.</p>
<!-- template="mla-icon-types" -->
<!-- title="Iconen en Icon Typen" order="20" -->
<p>WordPress houdt een lijst van "file types" (bestandstypen) bij van koppeling van bestandsextensies naar type namen welke gebruikt kunnen worden om een icon afbeelding te selecteren. Bijvoorbeeld, een "audio" file type is geassocieerd met een afbeelding van een muzieknoten. Er zijn negen van deze typen: archief, audio, code, default, document, interactief, spreadsheet, tekst en video. MLA kent een veel langere lijst; 112 icon typen/afbeeldingen in totaal. Als de "Activeer MLA Bestandstype Symbolen Ondersteuning" keuze-optie onderaan het scherm gekozen is, dan zullen de uitgebreide icon afbeeldingen worden gebruikt in plaats van de WordPress afbeeldingen.</p>
<p>U kunt de icon afbeelding die geassocieerd is met een bestandsextensie wijzigen door een nieuwe waarde te selecteren in de dropdown list op het Bewerk Upload MIME Type scherm of via Snel Wijzigen. U kunt de icon afbeelding voor verschillende extensies tegelijk wijzigen via de Acties selectie.</p>
<p>Als u een andere plugin of mechanisme heeft voor de ondersteuning van Upload MIME Type items, dan kunt u de MLA support in zijn geheel uit zetten. Maak de keuze-optie aan de linkeronderkant van het scherm leeg en klik "Sla Wijzigingen Op".</p>
<!-- template="mla-source-status" -->
<!-- title="Bron en Status" order="30" -->
<p>De "Bron" van een Upload MIME Type geeft aan waar de extensie/MIME Type associatie vandaan komt:</p>
<ul>
<li><strong>core</strong>: WordPress definieert een kernset van extensies en geassocieerde MIME typen, en deze lijst wijzigt met nieuwe WordPress versies. Dit zijn de "offici&euml;le" items. U kunt ze niet verwijderen, maar u kunt ze inactiveren zodat zij niet gebruikt worden om file uploads te valideren.</li>
<li><strong>mla</strong>: Media Library Assistant voegt verschillende extensie/type items toe, hierdoor geleid vanuit de meest populaire items gevonden in andere plugins en websites. Zij worden ge&iuml;nitialiseerd als "inactieve" items, dus u moet expliciet beslissen om ze te activeren voor gebruik in file upload validatie.</li>
<li><strong>custom</strong>: Gedefinieerd door een andere plugin of code, of via een manuele actie. Als MLA voor het eerst zijn lijst opbouwt dan zal het automatisch alles wat het vindt toevoegen aan uw actuele lijst als zijnde een nieuw, actief custom item. Daarna kunt u MLA gebruiken om deze te beheren.</li>
</ul>
<p>De "Status" van een item bepaalt of het door WordPress wordt gebruikt om file uploads te valideren en het toekennen van MIME typen aan attachments in uw Media Bibliotheek. Alleen "actieve" items worden op deze manier gebruikt; een item "inactief" maken blokkeert verdere uploads met deze extensie maar zal bestaande attachments in uw Media Bibliotheek NIET be&iuml;nvloeden.</p>
<!-- template="mla-bulk-actions" -->
<!-- title="'Bulk' Acties" order="40" -->
<p>De &#8220;Acties&#8221; dropdown list werkt samen met de keuze-optie kolom zodat u wijzigingen kunt aanbrengen aan meerdere items tegelijk. Klik de keuze-optie in de kolom titel rij om alle items op de pagina te selecteren, of klik de keuze-optie in een rij om items individueel te selecteren.</p>
<p>Als u eenmaa de items geselecteerd heeft die u wenst, kies een actie van de dropdown list en klik Uitvoeren om de actie op de geselecteerde items uit te voeren.</p>
<p>Als u hier de Wijzig optie gebruikt, kunt u de Actief/Inactief status voor alle geselecteerde items tegelijk wijzigen. Om een item uit de geselecteerde groep te halen, klik simpelweg op de x naast zijn naam in de linkerkolom van het Bulk Edit gebied.</p>
<p>De "Verwijder/Terug naar Custom" actie betreft alleen items met een "custom" bron. Het zal items verwijderen waarvoor geen standaard bron is of het zal de custom informatie vervangen door standaard informatie voor items met een standaard bron.</p>
<!-- template="mla-available-actions" -->
<!-- title="Beschikbare Acties" order="50" -->
<p>Met de muis over een rij in de Extensie kolom gaan onthult actie links zoals Wijzig, Snel Wijzig, Terug naar Standaard en Verwijder Permanent. Klikken op Wijzig toont een eenvoudig scherm om individuele metadata van een item te wijzigen. Klikken op Snel Wijzig toont een formulier om metadata van een item te wijzigen zonder het scherm te verlaten.</p>
<p>Als de bron van het gekozen item bron "custom" heeft, zal &eacute;&eacute;n van twee keuzen verschijnen. Als het item een standaard bron heeft (core of mla), zal klikken van Terug naar Standaard de custom informatie vervangen door de bijbehorende standaard bron informatie. Als het item  <strong>GEEN</strong> standaard bron heeft, zal klikken van Verwijder Permanent het custom item van de Uploads lijst verwijderen.</p>
<!-- template="mla-add-new" -->
<!-- title="Voeg Nieuw Type Toe" order="60" -->
<p>De linkerkant van het scherm bevat alle velden die u nodig heeft om een nieuw item voor de lijst te defini&euml;ren. Extensie en MIME Type zijn verplicht; de andere velden zijn dat niet en hebben default waarden. Er bevindt zich meer informatie over elk veld in de tekst onder het waardeveld.</p>
<p><strong>OPMERKING:</strong> Om uw werk op te slaan en het item toe te voegen, moet u naar beneden scrollen en "Voeg Upload MIME Type Toe" klikken.</p>
<!-- template="mla-search" -->
<!-- title="Zoek Bekende Typen" order="70" -->
<p>U kunt in een lijst met meer dan 1.500 bekende bestandsextensie naar MIME type associaties zoeken die samengesteld is uit verschillende Internet bronnen. De lijst toont alternatieve MIME typen voor de core en mla items maar ook vele andere bestandsextensies en MIME typen die u kunt toevoegen als custom items. Klik de "Zoek Bekende Typen" knop onderaan het formulier.</p>
<!-- template="mla-save-changes" -->
<!-- title="Deactiveer/Activeer Uploads" order="80" -->
<p>Als u een andere plugin of mechanisme heeft voor het behandelen van Upload MIME Type items, kunt u de MLA ondersteuning in zijn geheel deactiveren. Maak de keuze-optie in de linkeronderhoek van het scherm leeg en klik "Sla Wijzigingen Op". De Uploads tabel zal worden vervangen door een "inactief" scherm en een keuze-optie waarmee u MLA ondersteuning weer kunt activeren wanneer u dat wenst.</p>
<p><strong>OPMERKING:</strong> Deze optie activeert of deactiveert <em><strong>NIET</strong></em> het uploaden van bestanden; het schakelt slechts de MLA ondersteuning van het beheer van de lijst van extensies en MIME typen uit.</p>
<!-- template="sidebar" -->
<p><strong>Voor meer informatie:</strong></p>
<p><a href="[+settingsURL+]?page=mla-settings-menu-documentation&mla_tab=documentation#mla_uploads" target="_blank">MLA Documentatie over Upload MIME Typen</a></p>
<p><a href="http://wordpress.org/support/plugin/media-library-assistant" target="_blank">MLA Support Forum</a></p>
