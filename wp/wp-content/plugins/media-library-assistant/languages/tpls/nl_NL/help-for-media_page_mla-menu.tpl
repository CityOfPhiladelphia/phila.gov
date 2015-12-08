<!-- loaded in class-mla-main.php function mla_add_help_tab for the Media/Assistant submenu screen -->
<!-- invoked as /wp-admin/upload.php?page=mla-menu -->
<!-- template="mla-overview" -->
<!-- title="Overzicht" order="10" -->
<p>Alle bestanden die u heeft ge&uuml;pload worden getoond in het Media/Assistant submenu, gesorteerd op Titel. U kunt de sorteervolgorde wijzigen door op &eacute;&eacute;n van de blauwe kolomnamen te klikken. U kunt de default sorteervolgorde wijzigen op het Instellingen scherm.</p>
<p>U kunt de Scherminstellingen tab gebruiken om de de manier waarop dit scherm getoond wordt, te wijzigen. U kunt elke combinatie van kolommen kiezen. U kunt ook kiezen hoeveel items op elke pagina getoond worden.</p>
<p>U kunt de lijst verkleinen op bestandstype/status door de tekstlinkfilters te gebruiken bovenaan het scherm. U kunt ook de lijst verder verkleinen op maandbasis door het dropdown menu te gebruiken boven de media lijst.</p>
<p>Als u taxonomie ondersteuning geselecteerd heeft, bijv. &#8220;Gekopp. Categorie&euml;n&#8221;, kunt u de lijst filteren door &#8220;Alle Categorie&euml;n&#8221;, &#8220;Geen Categorie&euml;n&#8221; of een specifieke categorie in de drop down lijst te kiezen. Als u een categorie kiest die kinder categorie&euml;n onder zich heeft, zullen attachments in deze kinder categorie&euml;n ook verschijnen in de gefilterde lijst. U kunt de taxonomie waarop u wilt filteren selecteren op de Instellingen pagina.</p>
<p><strong>OPMERKING:</strong> Maand en categorie filters zijn &#8220;sticky&#8221;, d.w.z. dat zij actief blijven als u het scherm hersorteert of het bestandstype/status wijzigt.</p>
<!-- template="mla-where-used" -->
<!-- title="Waar-gebruikt Vermelding" order="20" -->
<p>De &#8220;waar-gebruikt&#8221; kolommen (Komt voor in, Toegevoegd aan, Galerij in, MLA Galerij in) zijn buitengewoon krachtig voor het beheer van uw attachments. Zij helpen u bij het vinden van Media Library items die niet langer gebruikt worden.
</p>
<p>
De "<strong>(FOUTIEVE PARENT)</strong>" notatie betekent dat het item gekoppeld is (i.e. het <code>post_parent</code> database veld is niet nul), maar of het parent ID is van een bericht of pagina welke verwijderd is of het parent ID bestaat maar de attachment wordt niet gebruikt als Gekoppelde Afbeelding, is niet toegevoegd aan de body van het bericht of de pagina en is niet gebruikt binnen een <code>[gallery]</code> of <code>[mla_gallery]</code> shortcode in dat bericht of pagina. Er bestaan enkele problemen met het <code>post_parent</code> veld; bijvoorbeeld, als u de  "Gekoppelde Afbeelding" voor een bericht wijzigt wordt de <code>post_parent</code> niet altijd bijgewerkt. Er zijn ook enkele andere manieren om een item te gebruiken waarnaar MLA nog niet zoekt. Bijvoorbeeld, een andere plugin die een andere shortcode gebruikt dan [gallery] of [mla_gallery].
</p>
<p>
De "<strong>(ONGEBRUIKT)</strong>" notatie betekent dat de MLA zoekopdracht een bestaande parent voor het item vond maar dat de parent het item niet als Gekoppelde Afbeelding gebruikt, het niet gebruikt in de body en niet aanwezig is in een <code>[gallery]</code> of <code>[mla_gallery]</code> shortcode.
</p>
<p>
De "<strong>(WEES)</strong>" notatie betekent dat de MLA zoekopdracht geen <strong>enkel</strong> bericht of pagina vond die dit item gebruikt als Gekoppelde Afbeelding, niet is gebruikt in de body van <strong>enig</strong> bericht of pagina en niet is gebruikt in <strong>enig</strong> <code>[gallery]</code> of <code>[mla_gallery]</code> shortcode.
</p>
<p>
Er zijn vier waar-gebruikt vermeldingen en u kunt deze aan of uit zetten op de Instellingen/Media Library Assistant Algemeen tab. Als &eacute;&eacute;n of meer van de vermeldingen uitgezet zijn, zult u "<strong>(ONGEBRUIKT?)</strong>" of "<strong>(WEES?)</strong>" zien in de vermeldingen. Het vraagteken betekent dat het item gebruikt kan zijn op een manier waarop nu niet getest wordt. Als alle vermeldingen uitgezet zijn dan zult u "<strong>(GEEN REFERENTIE TESTEN)</strong>" zien als een herinnering.
</p>
<!-- template="mla-featured-inserted" -->
<!-- title="Komt voor in/Toegevoegd aan" order="30" -->
<p>De &#8220;Komt voor in&#8221; en &#8220;Toegevoegd aan&#8221; kolommen zijn ook krachtig voor het beheer van uw attachments. Zij tonen u waar elke attachment is gebruikt in een bericht of pagina als een &#8220;Gekoppelde Afbeelding&#8221; of als een afbeelding of link in de tekst.</p>
<p>U kunt ook de informatie in de &#8220;Titel/Naam&#8221; kolom gebruiken om &#8220;Wees&#8221; items te vinden die niet gebruikt worden in een bericht of pagina en items te vinden met een &#8220;Slechte Parent&#8221; (een parent die geen enkele referentie bevat naar het item) of een &#8220;Foutieve Parent&#8221; (een parent die niet bestaat).</p>
<p>Als performance een issue is, kunt u op de Instellingen pagina deze kolommen uitschakelen.</p>
<!-- template="mla-gallery-in" -->
<!-- title="Galerij/MLA Galerij" order="40" -->
<p>De &#8220;Galerij in&#8221; en &#8220;MLA Galerij in&#8221; kolommen zijn tevens krachtig voor het beheer van uw attachments. Zij tonen u waar elke attachment is gebruikt in een <code>[gallery]</code> of <code>[mla_gallery]</code> shortcode in een bericht of pagina. Deze kolommen gebruiken <strong>niet</strong> de post_parent (gekoppeld aan) status van het item; zij voeren de shortcode werkelijk uit en beoordelen de attachments in het resultaat.</p>
<p>U kunt ook de informatie in de &#8220;Titel/Naam&#8221; kolom gebruiken om &#8220;Wees&#8221; items te vinden die niet gebruikt worden in een bericht of pagina en items te vinden met een &#8220;Slechte Parent&#8221; (een parent die geen enkele referentie bevat naar het item) of een &#8220;Foutieve Parent&#8221; (een parent die niet bestaat).</p>
<p>Als performance een issue is, kunt u op de Instellingen pagina deze kolommen uitschakelen. U kunt ook de instellingen aanpassen zodat de resultaten opgeslagen worden in de cache gedurende vijftien minuten tussen updates. De resultaten worden automatisch bijgewerkt als er een bericht, pagina of attachment wordt toegevoegd of bijgewerkt.</p>
<!-- template="mla-categories-tags" -->
<!-- title="Taxonomie Ondersteuning" order="50" -->
<p>De &#8220;taxonomie&#8221; kolommen helpen u de attachments te groeperen op onderwerp en sleutelwoord waarden. De kolommen tonen de categorie&euml;n en tags die gekoppeld zijn aan het item. U kunt op de getoonde waarden klikken om een lijst van alle items te krijgen die gekoppeld zijn aan die waarde.</p>
<p>De Media Library Assistant levert twee voor-gedefinieerde taxonomie&euml;n, &#8220;Gekopp. Categorie&euml;n&#8221; en &#8220;Gekopp. Tags&#8221; die geactiveerd zijn als default. U kunt ondersteuning uit- of aanzetten voor een geregistreerde taxonomie op het Instellingen scherm. De standaard WordPress Categorie&euml;n en Tags maar ook elke custom taxonomie kunnen worden ondersteund.</p>
<p>Als u ondersteuning toevoegt voor een taxonomie dan wordt deze zichtbaar op het hoofdscherm. Als u de kolom wilt verbergen kan dat eenvoudig via de Scherminstellingen en het deselecteren ervan.</p>
<p>Ondersteunde taxonomie&euml;n verschijnen ook als submenus onder het Media menu aan de linkerkant van het scherm. U kunt de taxonomie termen wijzigen door deze submenus te klikken. De taxonomie wijzig schermen bevatten een &#8220;Attachments&#8221; kolom welke het aantal attachment objecten aangeeft voor elke term. U kunt een gefilterde lijst tonen door het aantal in deze kolom te klikken.</p>
<!-- template="mla-custom-fields" -->
<!-- title="Custom Velden" order="60" -->
<p>U kunt sorteerbare kolommen toevoegen aan het Media/Assistant submenu via de Custom Velden tab op de Instellingen pagina. Daar kunt u regels defini&euml;ren die attachment metadata zoals bestandsgrootte koppelen aan WordPress custom velden. Met de &#8220;MLA Kolom&#8221; keuze optie bij elke regel kunt u kiezen welke regels actief zullen zijn op het scherm.</p>
<!-- template="mla-search-media" -->
<!-- title="Zoek Media" order="70" -->
<p>De &#8220;Zoek Media&#8221; optie ondersteunt een sleutelwoord zoekopdracht binnen verschillende attachment velden; typ woorden en/of zinsdelen in het veld, gescheiden door spaties. Klik de Zoek Media knop voor een schrijfwijze ongevoelige "SQL LIKE" zoekopdracht. Elk sleutelwoord in de totale zoekopdracht wordt apart getest, dus de volgorde van de zoekwoorden hoeft niet gelijk te zijn aan de volgorde in de tekst. Bijvoorbeeld, zoeken op "vriend" en "beste" zal ook "Beste Vriend" opleveren. Als u aanhalingstekens om de zoekgegevens zet dan is de woordvolgorde wel belangrijk voor het zoekresultaat (en spaties tussen woorden moeten dan ook gelijk zijn). U kunt ook delen van woorden zoeken, bijv., "rien" zal ook "vriend" opleveren.</p>
<p>Als u de termen ingetoetst heeft die u wilt, gebruik dan de opties onder het veld om uw zoekopdracht te verfijnen. U kunt kiezen welke combinatie gebruikt wordt; "of" betekent dat elke term gevonden mag worden, "en" betekent dat alle termen tegelijk gevonden moeten worden. Gebruik de keuze-opties om de zoekopdracht breder te maken met meer velden in de database. De "Termen" keuze-optie breidt de zoekopdracht uit met het naam veld van de taxonomie&euml;n die ingesteld zijn op de Instellingen/Media Library Assistant Algemeen tab.</p>
<p>Als u alleen een getal opgeeft. wordt dit ge&iuml;nterpreteerd als een zoekopdracht naar attachment ID of parent ID (post_parent). Dit is een toevoeging op de normale zoekmogelijkheden in de tekst velden, bijv., titel.</p>
<!-- template="mla-terms-search" -->
<!-- title="Termen Zoeken" order="80" -->
<p>De &#8220;Termen Zoeken&#8221; mogelijkheden laten u het Media/Assistant submenu en de Media Manager Modal Window filteren door het vinden van een of meer zinsdelen in het Naam veld of taxonomie termen. Er zijn twee manieren om van deze mogelijkheid gebruik te maken:
</p>
<ol>
<li>Kies de "Termen" optie onder de "Zoek Media" knop in het Media/Assistant submenu of de Media Manager toolbar. De zinsdelen die u intypt worden getoetst met zowel taxonomie term namen als elk ander veld dat u gekozen heeft.</li>
<li>Klik de "Termen Zoeken" knop naast de termen filter dropdown. Dit zorgt voor het tonen van de "Zoek Termen" popup met verschillende toegevoegde opties om uw zoekopdracht nog meer te verfijnen. Deze worden beschreven op de Instellingen/Media Library Assistant <a href="[+settingsURL+]?page=mla-settings-menu-documentation&mla_tab=documentation#terms_search" target="_blank">Documentatie tab</a>.</li>
</ol>
<p>U kunt selecteren welke taxonomie&euml;n gebruikt worden in de zoekopdracht door uw keuze(s) op te geven op de Instellingen/Media Library Assistant Algemeen tab.</p>
<!-- template="mla-bulk-actions" -->
<!-- title="&quot;Bulk&quot; Acties" order="90" -->
<p>De &#8220;Acties&#8221; dropdown werkt samen met de keuze-optie kolom en maakt het mogelijk om wijzigingen aan te brengen op meer dan &eacute;&eacute;n item tegelijk. Klik de keuze-optie in de kolom titel om alle items op de pagina te selecteren, of klik de keuze-optie voor een regel om de items individueel te selecteren.</p>
<p>Als u eenmaal de items geselecteerd heeft die u wilt, kies daarna een actie in de dropdown lijst en klik Uitvoeren om deze actie uit te voeren op de geselecteerde items. De mogelijke acties zullen vari&euml;ren al naar gelang het bestandstype/status dat u gekozen heeft.</p>
<p>Als u Prullenbak ondersteuning geactiveerd heeft voor media (definieer MEDIA_TRASH in wp-config.php) kunt u acties gebruiken om items te verschuiven van en naar de Prullenbak of het permanent verwijderen.</p>
<p>Als u Wijzig gebruikt, kunt u de metadata (auteur, parent, taxonomie termen) in &eacute;&eacute;n keer wijzigen voor alle geselecteerde attachments. Om een attachment te verwijderen uit de groepering, klik daarvoor de x direct naast de naam in de linker kolom van het Wijzig gebied.</p>
<p>Wijzig ondersteuning voor taxonomie termen maakt het mogelijk om termen <strong>toe te voegen, te verwijderen of volledig te vervangen</strong> voor de geselecteerde attachments. Onder elk taxonomie wijzig veld bevinden zich drie radio buttons waarmee de gewenste actie gekozen kan worden.</p>
<p>De taxonomie&euml;n die verschijnen in het Wijzig gebied kunnen een deelverzameling zijn van de taxonomie&euml;n ondersteund op het wijzig scherm van een enkel item. U kunt kiezen welke taxonomie&euml;n verschijnen door uw keuze(s) aan te geven op de Instellingen/Media Library Assistant Algemeen tab.</p>
<p>U kunt de Titel, Onderschrift, Omschrijving en ALT Tekst waarden wijzigen voor alle geselecteerde attachments. U kunt een <a href="[+settingsURL+]?page=mla-settings-menu-documentation&mla_tab=documentation#mla_template_parameters" target="_blank">Content Template</a> gebruiken om de waarden te customisen gebaseerd op attachment-specifieke data sources. U kunt ook de waarde van een veld verwijderen door gebruikmaking van de speciale <code>template:[+empty+]</code> waarde.</p>
<!-- template="mla-available-actions" -->
<!-- title="Mogelijke Acties" order="100" -->
<p>De muis boven een rij bewegen toont actie links zoals Wijzig, Snel Wijzig, Prullenbak en Verwijder Permanent. Klikken van Wijzig toont een eenvoudig scherm waarin de metadata van dit bestand gewijzigd kunnen worden. Klikken van Prullenbak stopt het bestand in de prullenbak maar zal de berichten of pagina's waaraan het is gekoppeld niet be&iuml;nvloeden. Klikken van Verwijder Permanent wist het bestand van de media bibliotheek (en ook van elke post waaraan het momenteel gekoppeld is). Klikken van Snel Wijzig toont een formulier voor het wijzigen van de metadata van het bestand zonder dat het menuscherm verlaten wordt.</p>
<p>De taxonomie&euml;n die verschijnen in het Wijzig gebied kunnen een deelverzameling zijn van de taxonomie&euml;n ondersteund op het wijzig scherm van een enkel item. U kunt kiezen welke taxonomie&euml;n verschijnen door uw keuze(s) aan te geven op de Instellingen/Media Library Assistant Algemeen tab.</p>
<!-- template="mla-attaching-files" -->
<!-- title="Koppelen Bestanden" order="110" -->
<p>Als een media bestand niet gekoppeld is aan een post, zult u (ongekoppeld) zien in de Gekoppeld Aan kolom. U kunt op "Zet Parent" klikken in de "Gekoppeld aan" kolom, of klikken op Wijzig of Snel Wijzig actie om het bestand te koppelen aan een Parent ID. De "Selecteer Parent" popup vereenvoudigt het vinden van de juiste parent voor uw attachment.</p>
<p>U kunt meer informatie over de Selecteer Parent popup op de Instellingen/Media Library Assistant <a href="[+settingsURL+]?page=mla-settings-menu-documentation&mla_tab=documentation#select_parent" target="_blank">Documentatie tab</a>.
<!-- template="sidebar" -->
<p><strong>Voor meer informatie:</strong></p>
<p><a href="[+settingsURL+]?page=mla-settings-menu-documentation&mla_tab=documentation#terms_search" target="_blank">MLA Documentatie voor Zoek Termen</a></p>
<p><a href="[+settingsURL+]?page=mla-settings-menu-documentation&mla_tab=documentation#select_parent" target="_blank">MLA Documentatie voor de Selecteer Parent popup</a></p>
<p><a href="[+settingsURL+]?page=mla-settings-menu-documentation&mla_tab=documentation#mla_template_parameters" target="_blank">MLA Documentatie voor Content Templates</a></p>
<p><a href="http://codex.wordpress.org/Media_Library_Screen" target="_blank">Codex documentatie over Media Bibliotheek</a></p>
<p><a href="http://wordpress.org/support/plugin/media-library-assistant" target="_blank">MLA Support Forum</a></p>