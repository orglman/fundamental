<?php 
/**
 * @package orglman/fundamental
 * @link    https://github.com/orglman/fundamental/
 * @author  Tobias Jonson <git@orgelman.systems>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (GNU GPL)
 */
 
namespace orgelman\fundamental\traits {
  trait phones {
      function printPhone($input) {
         $input = cleanPhone($input);
         $returns= array();
         $phones = explode(',', $input);
         foreach($phones as $key => $phone) {
            if($phone!='') {
               if((!$this->startsWith($phone, '+')) && ($this->startsWith($phone, '46'))) {
                  $phone = '+'.$phone;
               } 
               if(($this->startsWith($phone, '+467')) && (strlen($phone) == 12)) {
                  //+467XXXXXXXX
                  //+46 7XX-XX XX XX
                  $phone = array('number' => substr($phone, 0, 3) . ' ' . substr($phone, 3, 3) . '-' . substr($phone, 6, 2) . ' ' . substr($phone, 8, 2) . ' ' . substr($phone, 10, 2),
                                 'city'   => '',
                                 'region' => '');

               } elseif(($this->startsWith($phone, '+468')) && (strlen($phone) == 12)) {
                  //+468XXXXXXXX
                  //+46 X-XXX XXX XX
                  $phone = array('number' => substr($phone, 0, 3) . ' ' . substr($phone, 3, 1) . '-' . substr($phone, 4, 3) . ' ' . substr($phone, 7, 3) . ' ' . substr($phone, 10, 2),
                                 'city'   => 'Stockholm',
                                 'region' => 'Stockholms län');

               } elseif(($this->startsWith($phone, '+468')) && (strlen($phone) == 11)) {
                  //+468XXXXXXX
                  //+46 X-XXX XX XX
                  $phone = array('number' => substr($phone, 0, 3) . ' ' . substr($phone, 3, 1) . '-' . substr($phone, 4, 3) . ' ' . substr($phone, 7, 2) . ' ' . substr($phone, 9, 2),
                                 'city'   => 'Stockholm',
                                 'region' => 'Stockholms län');

               } elseif(($this->startsWith($phone, '+468')) && (strlen($phone) == 10)) {
                  //+468XXXXXX
                  //+46 X-XX XX XX
                  $phone = array('number' => substr($phone, 0, 3) . ' ' . substr($phone, 3, 1) . '-' . substr($phone, 4, 2) . ' ' . substr($phone, 6, 2) . ' ' . substr($phone, 8, 10),
                                 'city'   => 'Stockholm',
                                 'region' => 'Stockholms län');

               } else {
                  $phone = array('number' => $phone,'city'   => '','region' => '');
               } 

               /* Lista över riktnummer i Sverige
               08	Stockholm	Stockholms län
               011	Norrköping	Östergötlands län
               013	Linköping	Östergötlands län
               016	Eskilstuna-Torshälla	Södermanlands län
               018	Uppsala	Uppsala län
               019	Örebro-Kumla	Örebro län
               021	Västerås	Västmanlands län
               023	Falun	Dalarnas län
               026	Gävle-Sandviken	Gävleborgs län
               031	Göteborg	Västra Götalands län
               033	Borås	Västra Götalands län
               035	Halmstad	Hallands län
               036	Jönköping-Huskvarna	Jönköpings län
               040	Malmö	Skåne län
               042	Helsingborg-Höganäs	Skåne län
               044	Kristianstad	Skåne län
               046	Lund	Skåne län
               054	Karlstad	Värmlands län
               060	Sundsvall-Timrå	Västernorrlands län
               063	Östersund	Jämtlands län
               090	Umeå	Västerbottens län
               0120	Åtvidaberg	Östergötlands län
               0121	Söderköping	Östergötlands län
               0122	Finspång	Östergötlands län
               0123	Valdemarsvik	Östergötlands län
               0125	Vikbolandet	Östergötlands län
               0140	Tranås	Jönköpings län
               0141	Motala	Östergötlands län
               0142	Mjölby-Skänninge-Boxholm	Östergötlands län
               0143	Vadstena	Östergötlands län
               0144	Ödeshög	Östergötlands län
               0150	Katrineholm	Södermanlands län
               0151	Vingåker	Södermanlands län
               0152	Strängnäs	Södermanlands län
               0155	Nyköping-Oxelösund	Södermanlands län
               0156	Trosa-Vagnhärad	Södermanlands län
               0157	Flen-Malmköping	Södermanlands län
               0158	Gnesta	Södermanlands län
               0159	Mariefred	Södermanlands län
               0171	Enköping	Uppsala län
               0173	Öregrund-Östhammar	Uppsala län
               0174	Alunda	Uppsala län
               0175	Hallstavik-Rimbo	Stockholms län
               0176	Norrtälje	Stockholms län
               0220	Hallstahammar-Surahammar	Västmanlands län
               0221	Köping	Västmanlands län
               0222	Skinnskatteberg	Västmanlands län
               0223	Fagersta-Norberg	Västmanlands län
               0224	Sala-Heby	Västmanlands län
               0225	Hedemora-Säter	Dalarnas län
               0226	Avesta-Krylbo	Dalarnas län
               0227	Kungsör	Västmanlands län
               0240	Ludvika-Smedjebacken	Dalarnas län
               0241	Gagnef-Floda	Dalarnas län
               0243	Borlänge	Dalarnas län
               0246	Svärdsjö-Enviken	Dalarnas län
               0247	Leksand-Insjön	Dalarnas län
               0248	Rättvik	Dalarnas län
               0250	Mora-Orsa	Dalarnas län
               0251	Älvdalen	Dalarnas län
               0253	Idre-Särna	Dalarnas län
               0258	Furudal	Dalarnas län
               0270	Söderhamn	Gävleborgs län
               0271	Alfta-Edsbyn	Gävleborgs län
               0278	Bollnäs	Gävleborgs län
               0280	Malung	Dalarnas län
               0281	Vansbro	Dalarnas län
               0290	Hofors-Storvik	Gävleborgs län
               0291	Hedesunda-Österfärnebo	Gävleborgs län
               0292	Tärnsjö-Östervåla	Uppsala län
               0293	Tierp-Söderfors	Uppsala län
               0294	Karlsholmsbruk-Skärplinge	Uppsala län
               0295	Örbyhus-Dannemora	Uppsala län
               0297	Ockelbo-Hamrånge	Gävleborgs län
               0300	Kungsbacka	Hallands län
               0301	Hindås	Västra Götalands län
               0302	Lerum	Västra Götalands län
               0303	Kungälv	Västra Götalands län
               0304	Orust-Tjörn	Västra Götalands län
               0320	Kinna	Västra Götalands län
               0321	Ulricehamn	Västra Götalands län
               0322	Alingsås-Vårgårda	Västra Götalands län
               0325	Svenljunga-Tranemo	Västra Götalands län
               0340	Varberg	Hallands län
               0345	Hyltebruk-Torup	Hallands län
               0346	Falkenberg	Hallands län
               0346	Glommen	Hallands län
               0370	Värnamo	Jönköpings län
               0371	Gislaved-Anderstorp	Jönköpings län
               0372	Ljungby	Kronobergs län
               0380	Nässjö	Jönköpings län
               0381	Eksjö	Jönköpings län
               0382	Sävsjö	Jönköpings län
               0383	Vetlanda	Jönköpings län
               0390	Gränna	Jönköpings län
               0392	Mullsjö	Jönköpings län
               0393	Vaggeryd	Jönköpings län
               0410	Trelleborg	Skåne län
               0411	Ystad	Skåne län
               0413	Eslöv-Höör	Skåne län
               0414	Simrishamn	Skåne län
               0415	Hörby	Skåne län
               0416	Sjöbo	Skåne län
               0417	Tomelilla	Skåne län
               0418	Landskrona-Svalöv	Skåne län
               0430	Laholm	Hallands län
               0431	Ängelholm-Båstad	Skåne län
               0433	Markaryd-Strömnäsbruk	Kronobergs län
               0435	Klippan-Perstorp	Skåne län
               0451	Hässleholm	Skåne län
               0454	Karlshamn-Olofström	Blekinge län
               0455	Karlskrona	Blekinge län
               0456	Sölvesborg-Bromölla	Blekinge län
               0457	Ronneby	Blekinge län
               0459	Ryd	Kronobergs län
               0470	Växjö	Kronobergs län
               0471	Emmaboda	Kalmar län
               0472	Alvesta-Rydaholm	Kronobergs län
               0474	Åseda-Lenhovda	Kronobergs län
               0476	Älmhult	Kronobergs län
               0477	Tingsryd	Kronobergs län
               0478	Lessebo	Kronobergs län
               0479	Osby	Skåne län
               0480	Kalmar	Kalmar län
               0481	Nybro	Kalmar län
               0485	Öland	Kalmar län
               0486	Torsås	Kalmar län
               0490	Västervik	Kalmar län
               0491	Oskarshamn-Högsby	Kalmar län
               0492	Vimmerby	Kalmar län
               0493	Gamleby	Kalmar län
               0494	Kisa	Östergötalands län
               0495	Hultsfred-Virserum	Kalmar län
               0496	Mariannelund	Jönköpings län
               0498	Gotland	Gotlands län
               0499	Mönsterås	Kalmar län
               0500	Skövde	Västra Götalands län
               0501	Mariestad	Västra Götalands län
               0502	Tidaholm	Västra Götalands län
               0503	Hjo	Västra Götalands län
               0504	Tibro	Västra Götalands län
               0505	Karlsborg	Västra Götalands län
               0506	Töreboda-Hova	Västra Götalands län
               0510	Lidköping	Västra Götalands län
               0511	Skara-Götene	Västra Götalands län
               0512	Vara-Nossebro	Västra Götalands län
               0513	Herrljunga	Västra Götalands län
               0514	Grästorp	Västra Götalands län
               0515	Falköping	Västra Götalands län
               0520	Trollhättan	Västra Götalands län
               0521	Vänersborg	Västra Götalands län
               0522	Uddevalla	Västra Götalands län
               0523	Lysekil	Västra Götalands län
               0524	Munkedal	Västra Götalands län
               0525	Grebbestad	Västra Götalands län
               0526	Strömstad	Västra Götalands län
               0528	Färgelanda	Västra Götalands län
               0530	Mellerud	Västra Götalands län
               0531	Bengtsfors	Västra Götalands län
               0532	Åmål	Västra Götalands län
               0533	Säffle	Värmlands län
               0534	Ed	Östergötlands län
               0550	Kristinehamn	Värmlands län
               0551	Gullspång	Västra Götalands län
               0552	Deje	Värmlands län
               0553	Molkolm	Värmlands län
               0554	Kil	Värmlands län
               0555	Grums	Värmlands län
               0560	Torsby	Värmlands län
               0563	Hagfors-Munkfors	Värmlands län
               0564	Sysslebäck	Värmlands län
               0565	Sunne	Värmlands län
               0570	Arvika	Värmlands län
               0571	Charlottenberg-Åmotfors	Värmlands län
               0573	Årjäng	Värmlands län
               0580	Kopparberg	Örebro län
               0581	Lindesberg	Örebro län
               0582	Hallsberg	Örebro län
               0583	Askersund	Örebro län
               0584	Laxå	Örebro län
               0585	Fjugesta-Svartå	Örebro län
               0586	Karlskoga	Örebro län
               0587	Nora	Örebro län
               0589	Arboga	Västmanlands län
               0590	Filipstad	Värmlands län
               0591	Hällefors-Grythyttan	Örebro län
               0611	Härnösand	Västernorrlands län
               0612	Kramfors	Västernorrlands län
               0613	Ullånger	Västernorrlands län
               0620	Sollefteå	Västernorrlands län
               0621	Junsele	Västernorrlands län
               0622	Näsåker	Västernorrlands län
               0623	Ramsele	Västernorrlands län
               0624	Backe	Jämtlands län
               0640	Krokom	Jämtlands län
               0642	Lit	Jämtlands län
               0643	Hallen-Oviken	Jämtlands län
               0644	Hammerdal	Jämtlands län
               0645	Föllinge	Jämtlands län
               0647	Åre-Järpen	Jämtlands län
               0650	Hudiksvall	Gävleborgs län
               0651	Ljusdal	Gävleborgs län
               0652	Bergsjö	Gävleborgs län
               0653	Delsbo	Gävleborgs län
               0657	Los	Gävleborgs län
               0660	Örnsköldsvik	Västernorrlands län
               0661	Bredbyn	Västernorrlands län
               0662	Björna	Västernorrlands län
               0663	Husum	Västernorrlands län
               0670	Strömsund	Jämtlands län
               0671	Hoting	Jämtlands län
               0672	Gäddede	Jämtlands län
               0680	Sveg	Jämtlands län
               0682	Rätan	Jämtlands län
               0684	Hede-Funäsdalen	Jämtlands län
               0687	Svenstavik	Jämtlands län
               0690	Ånge	Västernorrlands län
               0691	Torpshammar	Västernorrlands län
               0692	Liden	Västernorrlands län
               0693	Bräcke-Gällö	Jämtlands län
               0695	Stugun	Jämtlands län
               0696	Hammarstrand	Jämtlands län
               0910	Skellefteå	Västerbottens län
               0911	Piteå	Norrbottens län
               0912	Byske	Västerbottens län
               0913	Lövånger	Västerbottens län
               0914	Burträsk	Västerbottens län
               0915	Bastuträsk	Västerbottens län
               0916	Jörn	Västerbottens län
               0918	Norsjö	Västerbottens län
               0920	Luleå	Norrbottens län
               0921	Boden	Norrbottens län
               0922	Haparanda	Norrbottens län
               0923	Kalix	Norrbottens län
               0924	Råneå	Norrbottens län
               0925	Lakaträsk	Norrbottens län
               0926	Överkalix	Norrbottens län
               0927	Övertorneå	Norrbottens län
               0928	Harads	Norrbottens län
               0929	Älvsbyn	Norrbottens län
               0930	Nordmaling	Västerbottens län
               0932	Bjurholm	Västerbottens län
               0933	Vindeln	Västerbottens län
               0934	Robertsfors	Västerbottens län
               0935	Vännäs	Västerbottens län
               0940	Vilhelmina	Västerbottens län
               0941	Åsele	Västerbottens län
               0942	Dorotea	Västerbottens län
               0943	Fredrika	Västerbottens län
               0950	Lycksele	Västerbottens län
               0951	Storuman	Västerbottens län
               0952	Sorsele	Västerbottens län
               0953	Malå	Västerbottens län
               0954	Tärnaby	Västerbottens län
               0960	Arvidsjaur	Norrbottens län
               0961	Arjeplog	Norrbottens län
               0970	Gällivare	Norrbottens län
               0971	Jokkmokk	Norrbottens län
               0973	Porjus	Norrbottens län
               0975	Hakkas	Norrbottens län
               0976	Vuollerim	Norrbottens län
               0977	Korpilombolo	Norrbottens län
               0978	Pajala	Norrbottens län
               0980	Kiruna	Norrbottens län
               0981	Vittangi	Norrbottens län
               */

               $returns[$key] = $phone;
            }
         }
         return json_encode($returns);
      }
      function cleanPhone($input) {
         $returns= array();
         $input = str_replace(';',',',$input);
         $phones = explode(',', $input);

         foreach($phones as $phone) {
            $phone = preg_replace('/[^0-9.]+/', '', $phone);
            if($phone!='') {
               if($this->startsWith($phone, '00')) {
                  $phone = '+'.ltrim($phone, '0');
               } else {
                  if($this->startsWith($phone, '0')) {
                     $phone = ltrim($phone, '0');
                  }
                  if(!$this->startsWith($phone, '46')) {
                     $phone = '+'.'46'.$phone;
                  }
               }
               $returns[] = $phone;
            }
         }

         return implode(',', $returns);
      }
  }
}
