<?php

namespace Database\Seeders;

use App\Models\Registry;
use Illuminate\Database\Seeder;

class RegistrySeeder extends Seeder
{
    public function create(string $name, string $description, int $validity_period): Registry
    {
        $registry = new Registry();
        $registry->name = $name;
        $registry->description = $description;
        $registry->validity_period = $validity_period;
        $registry->save();

        return $registry;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->create(
            'Kontrola okresowa stanu technicznego obiektu',
            'Obiekty budowlane powinny być w czasie ich użytkowania poddawane przez właściciela lub zarządcę kontroli okresowej, co najmniej raz w roku, polegającej na sprawdzeniu stanu technicznego:
            elementów budynku, budowli i instalacji narażonych na szkodliwe wpływy atmosferyczne i niszczące działania czynników występujących podczas użytkowania obiektu,
            instalacji i urządzeń służących ochronie środowiska,
            instalacji gazowych oraz przewodów kominowych  (dymowych, spalinowych i wentylacyjnych) –  art. 62 ust. 1 pkt 1 ustawy – Prawo budowlane.
            https://www.gunb.gov.pl/strona/kontrole-stanu-technicznego-obiektow
            ',
            12);
        $this->create(
            'Kontrola okresowa stanu technicznego obiektu wykonywana',
            'Co najmniej raz na 5 lat należy przeprowadzać kontrolę polegającą na sprawdzeniu stanu technicznego:
            elementów budynku, budowli i instalacji narażonych na szkodliwe wpływy atmosferyczne i niszczące działania czynników występujących podczas użytkowania obiektu,
            estetyki obiektu budowlanego oraz jego otoczenia,
            instalacji elektrycznej i piorunochronnej.
            https://www.gunb.gov.pl/strona/kontrole-stanu-technicznego-obiektow
            ',
            60);
        $this->create(
            'Kontrola okresowa stanu technicznego obiektu wielkopowierzchniowego',
            'Obiekty budowlane powinny być w czasie ich użytkowania poddawane przez właściciela lub zarządcę kontroli okresowej, co najmniej raz w roku, polegającej na sprawdzeniu stanu technicznego:
            elementów budynku, budowli i instalacji narażonych na szkodliwe wpływy atmosferyczne i niszczące działania czynników występujących podczas użytkowania obiektu,
            instalacji i urządzeń służących ochronie środowiska,
            instalacji gazowych oraz przewodów kominowych  (dymowych, spalinowych i wentylacyjnych) –  art. 62 ust. 1 pkt 1 ustawy – Prawo budowlane.
            https://www.gunb.gov.pl/strona/kontrole-stanu-technicznego-obiektow
            ',
            6);
        $this->create(
            'Przegląd instalacji gazowej ',
            'Obiekty budowlane powinny być w czasie ich użytkowania poddawane przez właściciela lub zarządcę kontroli okresowej, co najmniej raz w roku, polegającej na sprawdzeniu stanu technicznego:
            elementów budynku, budowli i instalacji narażonych na szkodliwe wpływy atmosferyczne i niszczące działania czynników występujących podczas użytkowania obiektu,
            instalacji i urządzeń służących ochronie środowiska,
            instalacji gazowych oraz przewodów kominowych  (dymowych, spalinowych i wentylacyjnych) –  art. 62 ust. 1 pkt 1 ustawy – Prawo budowlane.
            ',
            12);
        $this->create(
            'Przegląd przewodów kominowych (dymowych, spalinowych i  wentylacyjnych) ',
            'OObiekty budowlane powinny być w czasie ich użytkowania poddawane przez właściciela lub zarządcę kontroli okresowej, co najmniej raz w roku, polegającej na sprawdzeniu stanu technicznego:
            elementów budynku, budowli i instalacji narażonych na szkodliwe wpływy atmosferyczne i niszczące działania czynników występujących podczas użytkowania obiektu,
            instalacji i urządzeń służących ochronie środowiska,
            instalacji gazowych oraz przewodów kominowych  (dymowych, spalinowych i wentylacyjnych) –  art. 62 ust. 1 pkt 1 ustawy – Prawo budowlane.
            ',
            12);
        $this->create(
            'Przegląd instalacji gazowej ',
            'Obiekty budowlane powinny być w czasie ich użytkowania poddawane przez właściciela lub zarządcę kontroli okresowej, co najmniej raz w roku, polegającej na sprawdzeniu stanu technicznego:
            elementów budynku, budowli i instalacji narażonych na szkodliwe wpływy atmosferyczne i niszczące działania czynników występujących podczas użytkowania obiektu,
            instalacji i urządzeń służących ochronie środowiska,
            instalacji gazowych oraz przewodów kominowych  (dymowych, spalinowych i wentylacyjnych) –  art. 62 ust. 1 pkt 1 ustawy – Prawo budowlane.
            ',
            12);
        $this->create(
            'Przegląd i konserwacja systemu oddymiania',
            'Przegląd systemu oddymiania będzie polegał na sprawdzeniu wszystkich elementów i podzespołów systemu, przeprowadzeniu niezbędnych testów, poprawności reakcji systemu na sygnały wysyłane z centrali, przycisków oddymiania i innych komponentów. Urządzenia są także sprawdzane pod kątem ewentualnych uszkodzeń. Utrzymanie całego systemu w gotowości i pełnej sprawności jest możliwe tylko dzięki systematycznym przeglądom i wykonywanym naprawom. Co ważne, każda kontrola i przeprowadzenie testów wymaga odpowiedniego wpisu w książce eksploatacji urządzenia. Dokumentacja potwierdzająca dokonywanie terminowych konserwacji jest obligatoryjna dla właściciela czy zarządcy obiektu.
        https://isap.sejm.gov.pl/isap.nsf/DocDetails.xsp?id=WDU20060800563
        ',
            12);
        $this->create(
            'Przegląd techniczny i czynności konserwacyjne gaśnic',
            'Gaśnice powinny być poddawane przeglądom technicznym i czynnościom konserwacyjnym, zgodnie z zasadami i w sposób określony w Polskich Normach dotyczących urządzeń przeciwpożarowych i gaśnic w dokumentacji techniczno-ruchowej oraz instrukcjach obsługi, opracowanych przez ich producentów.
        ',
            12);
        $this->create(
            'Przegląd systemów audiowizualnych',
            'Zgodnie z instrukcja producenta. Autoryzowana firma zewnętrzna.
',
            12);
        $this->create(
            'Przegląd instalacji elektrycznej i piorunochronnej',
            'Obiekty budowlane powinny być w czasie ich użytkowania poddawane przez właściciela lub zarządcę kontroli okresowej, co najmniej raz w roku, polegającej na sprawdzeniu stanu technicznego:
        elementów budynku, budowli i instalacji narażonych na szkodliwe wpływy atmosferyczne i niszczące działania czynników występujących podczas użytkowania obiektu,
        instalacji i urządzeń służących ochronie środowiska,
        instalacji gazowych oraz przewodów kominowych  (dymowych, spalinowych i wentylacyjnych) –  art. 62 ust. 1 pkt 1 ustawy – Prawo budowlane.
        https://www.gunb.gov.pl/strona/kontrole-stanu-technicznego-obiektow
        Co najmniej raz na 5 lat należy przeprowadzać kontrolę obejmującą badanie instalacji elektrycznej i piorunochronnej w zakresie stanu sprawności połączeń, osprzętu, zabezpieczeń i środków ochrony od porażeń, oporności izolacji przewodów oraz uziemień instalacji i aparatów (art. 62 ust. 1 pkt 2 ustawy – Prawo budowlane).
        https://www.gunb.gov.pl/strona/kontrole-stanu-technicznego-obiektow
        https://www.prawo.pl/kadry/przeglady-instalacji-elektrycznej-jak-czesto-przepisy,187702.html
        ',
            12);
        $this->create(
            'Pomiar rezystancji izolacji instalacji elektrycznej',
            'Norma PN-HD 60364-6 wymaga, aby częstość okresowego sprawdzania instalacji była ustalana z uwzględnieniem rodzaju instalacji i wyposażenia, jej zastosowania i działania, częstości i jakości konserwacji oraz wpływów zewnętrznych na które jest narażona.
        https://sep.com.pl/regulaminy/opracowania.html
        Wykonywanie pomiarów odbiorczych i okresowych pomiarów ochronnych w instalacjach elektrycznych o napięciu znamionowym do 1 kV
        - mgr inż. Fryderyk Łasak, Członek Oddziału Nowohuckiego SEP
        Ostatnia aktualizacja: styczeń 2014 r. (5,2 MB)
        https://sep.com.pl/opracowania/opracowania_wykonywanie_pomiarow2009.pdf
        ',
            12);
        $this->create(
            'Przegląd systemu monitoringu CCTV',
            'Zgodnie z instrukcja producenta. Autoryzowana firma zewnętrzna.',
            12);
        $this->create(
            'Przegląd systemu zarządzania bezpieczeństwem',
            'Przegląd systemu zarządzania bezpieczeństwem',
            12);
        $this->create(
            'Kontrola Dźwiękowego Systemu Ostrzegawczego i Systemu Sygnalizacji Pożarowej (DSO i SSP)',
            'Urządzenia przeciwpożarowe oraz gaśnice przenośne i przewoźne zwane dalej “gaśnicami” powinny być poddawane przeglądom technicznym i czynnościom konserwacyjnym, zgodnie z zasadami i w sposób określony w Polskich Normach dotyczących urządzeń przeciwpożarowych i gaśnic w dokumentacji techniczno-ruchowej oraz instrukcjach obsługi, opracowanych przez ich producentów.
        https://www.cnbop.pl/pl/wydawnictwa/wytyczne
        CNBOP-PIB W-004:2017 Konserwacja dźwiękowych systemów ostrzegawczych
        ',
            12);
        $this->create(
            'Przegląd stacjonarnego systemu detekcji gazów',
            'Urządzenia przeciwpożarowe oraz gaśnice przenośne i przewoźne zwane dalej “gaśnicami” powinny być poddawane przeglądom technicznym i czynnościom konserwacyjnym, zgodnie z zasadami i w sposób określony w Polskich Normach dotyczących urządzeń przeciwpożarowych i gaśnic w dokumentacji techniczno-ruchowej oraz instrukcjach obsługi, opracowanych przez ich producentów.
        https://www.gazex.com/publikacje/zasady-stosowania-stacjonarnych-systemow-detekcji-gazow/
        ',
            12);
        $this->create(
            'Przegląd technologii basenowej',
            'https://www.gov.pl/web/psse-ostroda/wytyczne-glownego-inspektoratu-sanitarnego-w-sprawie-wymagan-jakosci-wody-oraz-warunkow-sanitarno-higienicznych-na-plywalniach',
            12);
        $this->create(
            'Przegląd stacji uzdatniania wody',
            'Zgodnie z instrukcja producenta. Autoryzowana firma zewnętrzna.',
            12);
        $this->create(
            'Przegląd drzwi obrotowych
',
            'Zgodnie z instrukcja producenta. Autoryzowana firma zewnętrzna.',
            12);
        $this->create(
            'Przegląd generatora dwutlenku chloru (zwalczanie legionelli)',
            'Zgodnie z instrukcja producenta.',
            12);
        $this->create(
            'Przegląd saun',
            'Zgodnie z instrukcja producenta.',
            12);
        $this->create(
            'Wymiana filtrów w centralach wentylacyjnych',
            'Zgodnie z instrukcja producenta.',
            12);
        $this->create(
            'Przegląd generatora pary',
            'Zgodnie z instrukcja producenta.',
            12);
        $this->create(
            'Przegląd wieży chłodniczej',
            'Zgodnie z instrukcja producenta.',
            12);
        $this->create(
            'Przegląd agregatów chłodniczych zawierające F-gazy',
            'Operatorzy urządzeń, które zawierają fluorowane gazy cieplarniane w ilości 5 ton ekwiwalentu CO2 lub większej i niezawarte w piankach, zapewniają, aby urządzenia były poddawane kontrolom szczelności.
        Kontrole szczelności przeprowadza się z następującą częstotliwością:
        w przypadku urządzeń, które zawierają fluorowane gazy cieplarniane w ilości 5 ton ekwiwalentu CO2 lub większej, ale mniejszej niż 50 ton ekwiwalentu CO2: co najmniej raz na 12 miesięcy lub co najmniej raz na 24 miesiące, jeżeli mają zainstalowany system wykrywania wycieków;
        w przypadku urządzeń, które zawierają fluorowane gazy cieplarniane w ilości 50 ton ekwiwalentu CO2 lub większej, ale mniejszej niż 500 ton ekwiwalentu CO2: co najmniej raz na sześć miesięcy lub co najmniej raz na 12 miesięcy, jeżeli mają zainstalowany system wykrywania wycieków;
        w przypadku urządzeń, które zawierają fluorowane gazy cieplarniane w ilości 500 ton ekwiwalentu CO2 lub większej: co najmniej raz na trzy miesiące lub co najmniej raz na sześć miesięcy, jeżeli mają zainstalowany system wykrywania wycieków.
        ',
            12);
        $this->create(
            'Przegląd zewnętrznych pomp ściekowych',
            'Zgodnie z instrukcja producenta.',
            12);
        $this->create(
            'Przegląd kręgielni',
            'Zgodnie z instrukcja producenta.',
            12);
        $this->create(
            'Przegląd bram pożarowych',
            'Urządzenia przeciwpożarowe oraz gaśnice przenośne i przewoźne zwane dalej “gaśnicami” powinny być poddawane przeglądom technicznym i czynnościom konserwacyjnym, zgodnie z zasadami i w sposób określony w Polskich Normach dotyczących urządzeń przeciwpożarowych i gaśnic w dokumentacji techniczno-ruchowej oraz instrukcjach obsługi, opracowanych przez ich producentów.
        Urządzenia przeciwpożarowe – należy przez to rozumieć urządzenia (stałe lub półstałe, uruchamiane ręcznie lub samoczynnie) służące do zapobiegania powstaniu, wykrywania, zwalczania pożaru lub ograniczania jego skutków, a w szczególności: stałe i półstałe urządzenia gaśnicze i zabezpieczające, urządzenia inertyzujące, urządzenia wchodzące w skład dźwiękowego systemu ostrzegawczego i systemu sygnalizacji pożarowej, w tym urządzenia sygnalizacyjno-alarmowe, urządzenia odbiorcze alarmów pożarowych i urządzenia odbiorcze sygnałów uszkodzeniowych, instalacje oświetlenia ewakuacyjnego, hydranty wewnętrzne i zawory hydrantowe, hydranty zewnętrzne, pompy w pompowniach przeciwpożarowych, przeciwpożarowe klapy odcinające, urządzenia oddymiające, urządzenia zabezpieczające przed powstaniem wybuchu i ograniczające jego skutki, kurtyny dymowe oraz drzwi, bramy przeciwpożarowe i inne zamknięcia przeciwpożarowe, jeżeli są wyposażone w systemy sterowania, przeciwpożarowe wyłączniki prądu oraz dźwigi dla ekip ratowniczych.
        W garażu podziemnym kondygnacje o powierzchni powyżej 1 500 m2 powinny, w razie pożaru, mieć możliwość oddzielenia ich od siebie i od kondygnacji nadziemnej budynku za pomocą drzwi, bram lub innych zamknięć o klasie odporności ogniowej nie mniejszej niż E I 30.
        Rozporządzenie Ministra Infrastruktury z dnia 12 kwietnia 2002 r. w sprawie warunków technicznych, jakim powinny odpowiadać budynki i ich usytuowanie.
        https://isap.sejm.gov.pl/isap.nsf/DocDetails.xsp?id=WDU20020750690',
            12);
        $this->create(
            'Przegląd systemu przeciwpożarowego ANSUL',
            'Urządzenia przeciwpożarowe oraz gaśnice przenośne i przewoźne zwane dalej “gaśnicami” powinny być poddawane przeglądom technicznym i czynnościom konserwacyjnym, zgodnie z zasadami i w sposób określony w Polskich Normach dotyczących urządzeń przeciwpożarowych i gaśnic w dokumentacji techniczno-ruchowej oraz instrukcjach obsługi, opracowanych przez ich producentów.',
            12);
        $this->create(
            'Przegląd drzwi pożarowych',
            'Urządzenia przeciwpożarowe oraz gaśnice przenośne i przewoźne zwane dalej “gaśnicami” powinny być poddawane przeglądom technicznym i czynnościom konserwacyjnym, zgodnie z zasadami i w sposób określony w Polskich Normach dotyczących urządzeń przeciwpożarowych i gaśnic w dokumentacji techniczno-ruchowej oraz instrukcjach obsługi, opracowanych przez ich producentów.
        Urządzenia przeciwpożarowe – należy przez to rozumieć urządzenia (stałe lub półstałe, uruchamiane ręcznie lub samoczynnie) służące do zapobiegania powstaniu, wykrywania, zwalczania pożaru lub ograniczania jego skutków, a w szczególności: stałe i półstałe urządzenia gaśnicze i zabezpieczające, urządzenia inertyzujące, urządzenia wchodzące w skład dźwiękowego systemu ostrzegawczego i systemu sygnalizacji pożarowej, w tym urządzenia sygnalizacyjno-alarmowe, urządzenia odbiorcze alarmów pożarowych i urządzenia odbiorcze sygnałów uszkodzeniowych, instalacje oświetlenia ewakuacyjnego, hydranty wewnętrzne i zawory hydrantowe, hydranty zewnętrzne, pompy w pompowniach przeciwpożarowych, przeciwpożarowe klapy odcinające, urządzenia oddymiające, urządzenia zabezpieczające przed powstaniem wybuchu i ograniczające jego skutki, kurtyny dymowe oraz drzwi, bramy przeciwpożarowe i inne zamknięcia przeciwpożarowe, jeżeli są wyposażone w systemy sterowania, przeciwpożarowe wyłączniki prądu oraz dźwigi dla ekip ratowniczych.
        ',
            12);
        $this->create(
            'Przegląd oświetlenia awaryjnego',
            'Urządzenia przeciwpożarowe oraz gaśnice przenośne i przewoźne zwane dalej “gaśnicami” powinny być poddawane przeglądom technicznym i czynnościom konserwacyjnym, zgodnie z zasadami i w sposób określony w Polskich Normach dotyczących urządzeń przeciwpożarowych i gaśnic w dokumentacji techniczno-ruchowej oraz instrukcjach obsługi, opracowanych przez ich producentów.
        Urządzenia przeciwpożarowe – należy przez to rozumieć urządzenia (stałe lub półstałe, uruchamiane ręcznie lub samoczynnie) służące do zapobiegania powstaniu, wykrywania, zwalczania pożaru lub ograniczania jego skutków, a w szczególności: stałe i półstałe urządzenia gaśnicze i zabezpieczające, urządzenia inertyzujące, urządzenia wchodzące w skład dźwiękowego systemu ostrzegawczego i systemu sygnalizacji pożarowej, w tym urządzenia sygnalizacyjno-alarmowe, urządzenia odbiorcze alarmów pożarowych i urządzenia odbiorcze sygnałów uszkodzeniowych, instalacje oświetlenia ewakuacyjnego, hydranty wewnętrzne i zawory hydrantowe, hydranty zewnętrzne, pompy w pompowniach przeciwpożarowych, przeciwpożarowe klapy odcinające, urządzenia oddymiające, urządzenia zabezpieczające przed powstaniem wybuchu i ograniczające jego skutki, kurtyny dymowe oraz drzwi, bramy przeciwpożarowe i inne zamknięcia przeciwpożarowe, jeżeli są wyposażone w systemy sterowania, przeciwpożarowe wyłączniki prądu oraz dźwigi dla ekip ratowniczych.
        ',
            12);
        $this->create(
            'Przegląd urządzenia transmisji alarmów pożarowych i sygnałów uszkodzeniowych',
            'Urządzenia przeciwpożarowe oraz gaśnice przenośne i przewoźne zwane dalej “gaśnicami” powinny być poddawane przeglądom technicznym i czynnościom konserwacyjnym, zgodnie z zasadami i w sposób określony w Polskich Normach dotyczących urządzeń przeciwpożarowych i gaśnic w dokumentacji techniczno-ruchowej oraz instrukcjach obsługi, opracowanych przez ich producentów.
            Urządzenia przeciwpożarowe – należy przez to rozumieć urządzenia (stałe lub półstałe, uruchamiane ręcznie lub samoczynnie) służące do zapobiegania powstaniu, wykrywania, zwalczania pożaru lub ograniczania jego skutków, a w szczególności: stałe i półstałe urządzenia gaśnicze i zabezpieczające, urządzenia inertyzujące, urządzenia wchodzące w skład dźwiękowego systemu ostrzegawczego i systemu sygnalizacji pożarowej, w tym urządzenia sygnalizacyjno-alarmowe, urządzenia odbiorcze alarmów pożarowych i urządzenia odbiorcze sygnałów uszkodzeniowych, instalacje oświetlenia ewakuacyjnego, hydranty wewnętrzne i zawory hydrantowe, hydranty zewnętrzne, pompy w pompowniach przeciwpożarowych, przeciwpożarowe klapy odcinające, urządzenia oddymiające, urządzenia zabezpieczające przed powstaniem wybuchu i ograniczające jego skutki, kurtyny dymowe oraz drzwi, bramy przeciwpożarowe i inne zamknięcia przeciwpożarowe, jeżeli są wyposażone w systemy sterowania, przeciwpożarowe wyłączniki prądu oraz dźwigi dla ekip ratowniczych.
            ',
            12);
        $this->create(
            'Przegląd przeciwpożarowego wyłącznika prądu',
            'Urządzenia przeciwpożarowe oraz gaśnice przenośne i przewoźne zwane dalej “gaśnicami” powinny być poddawane przeglądom technicznym i czynnościom konserwacyjnym, zgodnie z zasadami i w sposób określony w Polskich Normach dotyczących urządzeń przeciwpożarowych i gaśnic w dokumentacji techniczno-ruchowej oraz instrukcjach obsługi, opracowanych przez ich producentów.
            Urządzenia przeciwpożarowe – należy przez to rozumieć urządzenia (stałe lub półstałe, uruchamiane ręcznie lub samoczynnie) służące do zapobiegania powstaniu, wykrywania, zwalczania pożaru lub ograniczania jego skutków, a w szczególności: stałe i półstałe urządzenia gaśnicze i zabezpieczające, urządzenia inertyzujące, urządzenia wchodzące w skład dźwiękowego systemu ostrzegawczego i systemu sygnalizacji pożarowej, w tym urządzenia sygnalizacyjno-alarmowe, urządzenia odbiorcze alarmów pożarowych i urządzenia odbiorcze sygnałów uszkodzeniowych, instalacje oświetlenia ewakuacyjnego, hydranty wewnętrzne i zawory hydrantowe, hydranty zewnętrzne, pompy w pompowniach przeciwpożarowych, przeciwpożarowe klapy odcinające, urządzenia oddymiające, urządzenia zabezpieczające przed powstaniem wybuchu i ograniczające jego skutki, kurtyny dymowe oraz drzwi, bramy przeciwpożarowe i inne zamknięcia przeciwpożarowe, jeżeli są wyposażone w systemy sterowania, przeciwpożarowe wyłączniki prądu oraz dźwigi dla ekip ratowniczych.',
            12);
        $this->create(
            'Przegląd generatora prądotwórczego',
            'Zgodnie z instrukcja producenta.',
            12);
        $this->create(
            'Zasilacz UPS',
            'Zgodnie z instrukcja producenta.',
            12);
        $this->create(
            'Przegląd instalacji tryskaczowej',
            'Urządzenia przeciwpożarowe oraz gaśnice przenośne i przewoźne zwane dalej “gaśnicami” powinny być poddawane przeglądom technicznym i czynnościom konserwacyjnym, zgodnie z zasadami i w sposób określony w Polskich Normach dotyczących urządzeń przeciwpożarowych i gaśnic w dokumentacji techniczno-ruchowej oraz instrukcjach obsługi, opracowanych przez ich producentów.
            Urządzenia przeciwpożarowe – należy przez to rozumieć urządzenia (stałe lub półstałe, uruchamiane ręcznie lub samoczynnie) służące do zapobiegania powstaniu, wykrywania, zwalczania pożaru lub ograniczania jego skutków, a w szczególności: stałe i półstałe urządzenia gaśnicze i zabezpieczające, urządzenia inertyzujące, urządzenia wchodzące w skład dźwiękowego systemu ostrzegawczego i systemu sygnalizacji pożarowej, w tym urządzenia sygnalizacyjno-alarmowe, urządzenia odbiorcze alarmów pożarowych i urządzenia odbiorcze sygnałów uszkodzeniowych, instalacje oświetlenia ewakuacyjnego, hydranty wewnętrzne i zawory hydrantowe, hydranty zewnętrzne, pompy w pompowniach przeciwpożarowych, przeciwpożarowe klapy odcinające, urządzenia oddymiające, urządzenia zabezpieczające przed powstaniem wybuchu i ograniczające jego skutki, kurtyny dymowe oraz drzwi, bramy przeciwpożarowe i inne zamknięcia przeciwpożarowe, jeżeli są wyposażone w systemy sterowania, przeciwpożarowe wyłączniki prądu oraz dźwigi dla ekip ratowniczych.
            ',
            12);
        $this->create(
            'Przegląd stałego urządzenia gaśniczego',
            'Urządzenia przeciwpożarowe oraz gaśnice przenośne i przewoźne zwane dalej “gaśnicami” powinny być poddawane przeglądom technicznym i czynnościom konserwacyjnym, zgodnie z zasadami i w sposób określony w Polskich Normach dotyczących urządzeń przeciwpożarowych i gaśnic w dokumentacji techniczno-ruchowej oraz instrukcjach obsługi, opracowanych przez ich producentów.
            Urządzenia przeciwpożarowe – należy przez to rozumieć urządzenia (stałe lub półstałe, uruchamiane ręcznie lub samoczynnie) służące do zapobiegania powstaniu, wykrywania, zwalczania pożaru lub ograniczania jego skutków, a w szczególności: stałe i półstałe urządzenia gaśnicze i zabezpieczające, urządzenia inertyzujące, urządzenia wchodzące w skład dźwiękowego systemu ostrzegawczego i systemu sygnalizacji pożarowej, w tym urządzenia sygnalizacyjno-alarmowe, urządzenia odbiorcze alarmów pożarowych i urządzenia odbiorcze sygnałów uszkodzeniowych, instalacje oświetlenia ewakuacyjnego, hydranty wewnętrzne i zawory hydrantowe, hydranty zewnętrzne, pompy w pompowniach przeciwpożarowych, przeciwpożarowe klapy odcinające, urządzenia oddymiające, urządzenia zabezpieczające przed powstaniem wybuchu i ograniczające jego skutki, kurtyny dymowe oraz drzwi, bramy przeciwpożarowe i inne zamknięcia przeciwpożarowe, jeżeli są wyposażone w systemy sterowania, przeciwpożarowe wyłączniki prądu oraz dźwigi dla ekip ratowniczych.
            ',
            12);
        $this->create(
            'Przegląd konserwacyjny dźwigu do transportu osób lub ładunków',
            'Przeglądy konserwacyjne UTB (Urządzeń Transportu Bliskiego) wykonuje się w terminach określonych w załączniku nr 2 do rozporządzenia (Dz.U. 2018 poz. 2176), o ile nie zostały one określone w instrukcji eksploatacji.
             Rozporządzenie Ministra Przedsiębiorczości i Technologii z dnia 30 października 2018 r. w sprawie warunków technicznych dozoru technicznego w zakresie eksploatacji, napraw i modernizacji urządzeń transportu bliskiego.
             https://isap.sejm.gov.pl/isap.nsf/DocDetails.xsp?id=WDU20180002176',
            12);
        $this->create(
            'Badania okresowe dźwigu do transportu osób lub ładunków ',
            'Organ właściwej jednostki dozoru technicznego przeprowadza:
            badanie odbiorcze po zakończeniu wytwarzania UTB, w warunkach jego gotowości do pracy, przed wydaniem decyzji zezwalającej na eksploatację;
            badanie okresowe w toku eksploatacji UTB objętych pełnym dozorem;
            badanie doraźne:
            eksploatacyjne,
            kontrolne,
            powypadkowe lub poawaryjne.
            Rozporządzenie Ministra Przedsiębiorczości i Technologii z dnia 30 października 2018 r. w sprawie warunków technicznych dozoru technicznego w zakresie eksploatacji, napraw i modernizacji urządzeń transportu bliskiego.
            https://isap.sejm.gov.pl/isap.nsf/DocDetails.xsp?id=WDU20180002176
            ',
            12);
        $this->create(
            'Przegląd pomp ściekowych
',
            'Zgodnie z instrukcja producenta.',
            12);
        $this->create(
            'Przegląd automatów szorująco czyszczących',
            'Zgodnie z instrukcja producenta.',
            12);
        $this->create(
            'Przegląd infrastruktury parkingowej',
            'Zgodnie z instrukcja producenta.',
            12);
        $this->create(
            'Przegląd system stabilizacji ciśnienia',
            'Zgodnie z instrukcja producenta.',
            12);
        $this->create(
            'Monit szkodników zewnętrznych DDD',
            'Właściciel, posiadacz lub zarządzający nieruchomością są obowiązani utrzymać ją w należytym stanie higieniczno-sanitarnym w celu zapobiegania zakażeniom i chorobom zakaźnym, w szczególności:
                prowadzić prawidłową gospodarkę odpadami i ściekami;
                zwalczać gryzonie, insekty i szkodniki;
                usuwać padłe zwierzęta z nieruchomości;
                usuwać odchody zwierząt z nieruchomości.
                   ',
            12);
        $this->create(
            'Przegląd hydrantów wewnętrznych',
            'Urządzenia przeciwpożarowe oraz gaśnice przenośne i przewoźne zwane dalej “gaśnicami” powinny być poddawane przeglądom technicznym i czynnościom konserwacyjnym, zgodnie z zasadami i w sposób określony w Polskich Normach dotyczących urządzeń przeciwpożarowych i gaśnic w dokumentacji techniczno-ruchowej oraz instrukcjach obsługi, opracowanych przez ich producentów.
                 Urządzenia przeciwpożarowe – należy przez to rozumieć urządzenia (stałe lub półstałe, uruchamiane ręcznie lub samoczynnie) służące do zapobiegania powstaniu, wykrywania, zwalczania pożaru lub ograniczania jego skutków, a w szczególności: stałe i półstałe urządzenia gaśnicze i zabezpieczające, urządzenia inertyzujące, urządzenia wchodzące w skład dźwiękowego systemu ostrzegawczego i systemu sygnalizacji pożarowej, w tym urządzenia sygnalizacyjno-alarmowe, urządzenia odbiorcze alarmów pożarowych i urządzenia odbiorcze sygnałów uszkodzeniowych, instalacje oświetlenia ewakuacyjnego, hydranty wewnętrzne i zawory hydrantowe, hydranty zewnętrzne, pompy w pompowniach przeciwpożarowych, przeciwpożarowe klapy odcinające, urządzenia oddymiające, urządzenia zabezpieczające przed powstaniem wybuchu i ograniczające jego skutki, kurtyny dymowe oraz drzwi, bramy przeciwpożarowe i inne zamknięcia przeciwpożarowe, jeżeli są wyposażone w systemy sterowania, przeciwpożarowe wyłączniki prądu oraz dźwigi dla ekip ratowniczych.
                 ',
            12);
        $this->create(
            'Przegląd konserwacyjny wózka jezdniowego podnośnikowego z wysięgnikiem',
            'Przeglądy konserwacyjne UTB (Urządzeń Transportu Bliskiego) wykonuje się w terminach określonych w załączniku nr 2 do rozporządzenia (Dz.U. 2018 poz. 2176), o ile nie zostały one określone w instrukcji eksploatacji.
                Rozporządzenie Ministra Przedsiębiorczości i Technologii z dnia 30 października 2018 r. w sprawie warunków technicznych dozoru technicznego w zakresie eksploatacji, napraw i modernizacji urządzeń transportu bliskiego.
                https://isap.sejm.gov.pl/isap.nsf/DocDetails.xsp?id=WDU20180002176
                ',
            12);
        $this->create(
            'Badania okresowe wózka jezdniowego podnośnikowego z wysięgnikiem',
            'Organ właściwej jednostki dozoru technicznego przeprowadza:
                badanie odbiorcze po zakończeniu wytwarzania UTB, w warunkach jego gotowości do pracy, przed wydaniem decyzji zezwalającej na eksploatację;
                badanie okresowe w toku eksploatacji UTB objętych pełnym dozorem;
                badanie doraźne:
                eksploatacyjne,
                kontrolne,
                powypadkowe lub poawaryjne.
                Rozporządzenie Ministra Przedsiębiorczości i Technologii z dnia 30 października 2018 r. w sprawie warunków technicznych dozoru technicznego w zakresie eksploatacji, napraw i modernizacji urządzeń transportu bliskiego.
                https://isap.sejm.gov.pl/isap.nsf/DocDetails.xsp?id=WDU20180002176
                ',
            12);
    }
}
