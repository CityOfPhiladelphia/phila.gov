<?php 
function phila_translate_date($date, $language = 'english', $format = 'month-day-year'){

  $languageToMonth = [
    'arabic' => [
      '01' => 'كانون الثاني',
      '02' => 'شباط',
      '03' => 'آذار',
      '04' => 'نيسان',
      '05' => 'أيار',
      '06' => 'حزيران',
      '07' => 'تموز',
      '08' => 'آب',
      '09' => 'أيلول',
      '10' => 'تشرين الأول',
      '11' => 'تشرين الثاني',
      '12' => 'كانون الأول',
    ],
    'chinese' => [
      '01' => '1',
      '02' => '2',
      '03' => '3',
      '04' => '4',
      '05' => '5',
      '06' => '6',
      '07' => '7',
      '08' => '8',
      '09' => '9',
      '10' => '10',
      '11' => '11',
      '12' => '12',
    ],
    'english' => [
      '01' => 'January',
      '02' => 'February',
      '03' => 'March',
      '04' => 'April',
      '05' => 'May',
      '06' => 'June',
      '07' => 'July',
      '08' => 'August',
      '09' => 'September',
      '10' => 'October',
      '11' => 'November',
      '12' => 'December',
    ],
    'french' => [
      '01' => 'janvier',
      '02' => 'février',
      '03' => 'mars',
      '04' => 'avril',
      '05' => 'mai',
      '06' => 'juin',
      '07' => 'juillet',
      '08' => 'aout',
      '09' => 'septembre',
      '10' => 'octobre',
      '11' => 'novembre',
      '12' => 'décembre',
    ],
    'haitian' => [
      '01' => 'janvye',
      '02' => 'fevrye',
      '03' => 'mas',
      '04' => 'avril',
      '05' => 'me',
      '06' => 'jen',
      '07' => 'jiyè',
      '08' => 'out',
      '09' => 'septanm',
      '10' => 'oktòb',
      '11' => 'novanm',
      '12' => 'desanm',
    ],
    'indonesian' => [
      '01' => 'Januari',
      '02' => 'Februari',
      '03' => 'Maret',
      '04' => 'April',
      '05' => 'Mei',
      '06' => 'Juni',
      '07' => 'Juli',
      '08' => 'Agustus',
      '09' => 'September',
      '10' => 'Oktober',
      '11' => 'November',
      '12' => 'Desember',
    ],
    'khmer' => [
      '01' => 'ខែមករា',
      '02' => 'ខែកុម្ភៈ',
      '03' => 'ខែមីនា',
      '04' => 'ខែមេសា',
      '05' => 'ខែឧសភា',
      '06' => 'ខែមិថុនា',
      '07' => 'ខែកក្កដា',
      '08' => 'ខែសីហា',
      '09' => 'ខែកញ្ញា',
      '10' => 'ខែតុលា',
      '11' => 'ខែវិច្ឆិកា',
      '12' => 'ខែធ្នូ',
    ],
    'korean' => [
      '01' => '1',
      '02' => '2',
      '03' => '3',
      '04' => '4',
      '05' => '5',
      '06' => '6',
      '07' => '7',
      '08' => '8',
      '09' => '9',
      '10' => '10',
      '11' => '11',
      '12' => '12',
    ],
    'portuguese' => [
      '01' => 'Janeiro',
      '02' => 'Fevereiro',
      '03' => 'Março',
      '04' => 'Abril',
      '05' => 'Maio',
      '06' => 'Junho',
      '07' => 'Julho',
      '08' => 'Agosto',
      '09' => 'Setembro',
      '10' => 'Outubro',
      '11' => 'Novembro',
      '12' => 'Dezembro',
    ],
    'russian' => [
      '01' => 'Январь',
      '02' => 'Февраль',
      '03' => 'Март',
      '04' => 'Апрель',
      '05' => 'Май',
      '06' => 'Июнь',
      '07' => 'Июль',
      '08' => 'Август',
      '09' => 'Сентябрь',
      '10' => 'Октябрь',
      '11' => 'Ноябрь',
      '12' => 'Декабрь',
    ],
    'spanish' => [
      '01' => 'enero',
      '02' => 'febrero',
      '03' => 'marzo',
      '04' => 'abril',
      '05' => 'mayo',
      '06' => 'junio',
      '07' => 'julio',
      '08' => 'agosto',
      '09' => 'septiembre',
      '10' => 'octubre',
      '11' => 'noviembre',
      '12' => 'deciembre',
    ],
    'vietnamese' => [
      '01' => '1',
      '02' => '2',
      '03' => '3',
      '04' => '4',
      '05' => '5',
      '06' => '6',
      '07' => '7',
      '08' => '8',
      '09' => '9',
      '10' => '10',
      '11' => '11',
      '12' => '12',
    ],
  ];

// urdu
// hindo
// bengali

  $languageFormat = [
    'arabic' => [
      'month-day' => '{month} {day}',
      'month-year' => '{year} {month}',
      'month-day-year' => '{year} {month} {day}',
    ],
    'chinese' => [
      'month-day' => '{month} 月 {day} 日',
      'month-year' => '{year} 年 {month} 月',
      'month-day-year' => '{year} 年 {month} 月 {day} 日',
    ],
    'english' => [
      'month-day' => '{month} {day}',
      'month-year' => '{month} {year}',
      'month-day-year' => '{month} {day}, {year}',
    ],
    'french' => [
      'month-day' => '{day} {month}',
      'month-year' => '{month} {year}',
      'month-day-year' => '{day} {month} {year}',
    ],
    'haitian' => [
      'month-day' => '{day} {month}',
      'month-year' => '{month} {year}',
      'month-day-year' => '{day} {month} {year}',
    ],
    'indonesian' => [
      'month-day' => '{day} {month}',
      'month-year' => '{month} {year}',
      'month-day-year' => '{day} {month} {year}',
    ],
    'khmer' => [
      'month-day' => '{month} ទី {day}',
      'month-year' => '{month} ឆ្នាំ {year}',
      'month-day-year' => '{month} ទី {day} ឆ្នាំ {year}',
    ],
    'korean' => [
      'month-day' => '{month}월 {day}일:',
      'month-year' => '{year}년 {month}월',
      'month-day-year' => '{year}년 {month}월 {day}일:',
    ],
    'portuguese' => [
      'month-day' => '{day}o de {month}',
      'month-year' => '{month} de {year}',
      'month-day-year' => '{day}o de {month} de {year}',
    ],
    'russian' => [
      'month-day' => '{day} {month}',
      'month-year' => '{month} {year} r.',
      'month-day-year' => '{day} {month} {year} r.',
    ],
    'spanish' => [
      'month-day' => '{day} de {month}',
      'month-year' => '{month} de {year}',
      'month-day-year' => '{day} de {month} de {year}',
    ],
    'vietnamese' => [
      'month-day' => 'Ngày {day} tháng {month}',
      'month-year' => 'Tháng {month} năm {year}',
      'month-day-year' => 'Ngày {day} tháng {month} năm {year}',
    ],
  ];

  $datetime = DateTime::createFromFormat('m-d-Y', $date)->getTimestamp();
  $month = date("m", $datetime);
  $day = date("d", $datetime);
  $year = date("Y", $datetime);
  $translatedMonth = isset($languageToMonth[$language][$month]) ? $languageToMonth[$language][$month] : $languageToMonth['english'][$month];
  $formatTemplate = isset($languageFormat[$language][$format]) ? $languageFormat[$language][$format] : $languageFormat['english'][$format];

  $formattedDate = str_replace("{day}",$day,$formatTemplate); 
  $formattedDate = str_replace("{month}",$translatedMonth,$formattedDate); 
  $formattedDate = str_replace("{year}",$year,$formattedDate); 

  return $formattedDate;
}
?>
