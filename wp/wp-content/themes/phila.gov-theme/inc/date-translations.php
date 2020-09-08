<?php 
function phila_translate_date($date, $language = 'english', $format = 'month-day-year'){

  $languageToMonth = [
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
  ];

  // TODO: Derrick, all need the year in month-year after FE fix
  $languageFormat = [
    'english' => [
      'month-day' => '{month} {day}',
      'month-year' => '{month} {year}',
      'month-day-year' => '{month} {day}, {year}',
    ],
    'spanish' => [
      'month-day' => '{day} de {month}',
      'month-year' => '{month}',
      'month-day-year' => '{day} de {month} de {year}',
    ],
    'chinese' => [
      'month-day' => '{month} 月 {day} 日',
      'month-year' => '{month} 月',
      'month-day-year' => '{year} 年 {month} 月 {day} 日',
    ],
    'vietnamese' => [
      'month-day' => 'Ngày {day} tháng {month}',
      'month-year' => 'tháng {month}',
      'month-day-year' => 'Ngày {day} tháng {month} năm {year}',
    ],
    'french' => [
      'month-day' => '{month} {day}',
      'month-year' => '{month}',
      'month-day-year' => '{month} {day}, {year}',
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
