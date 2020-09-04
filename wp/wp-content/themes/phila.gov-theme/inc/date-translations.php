<?php 
function phila_translate_date($date, $language = 'english', $format = 'month-day-year'){

  $languageToMonth = [
    'english' => [
      '01' => 'january',
      '02' => 'february',
      '03' => 'march',
      '04' => 'april',
      '05' => 'may',
      '06' => 'june',
      '07' => 'july',
      '08' => 'august',
      '09' => 'september',
      '10' => 'october',
      '11' => 'november',
      '12' => 'december',
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

  $languageFormat = [
    'english' => [
      'month-day' => '{month} {day}',
      'month-year' => '{month} {year}',
      'month-day-year' => '{month} {day}, {year}',
    ],
    'spanish' => [
      'month-day' => '{day} de {month}',
      'month-year' => '{month} de {year}',
      'month-day-year' => '{day} de {month} de {year}',
    ],
    'chinese' => [
      'month-day' => '{month} 月 {day} 日',
      'month-year' => '{year} 年 {month} 月',
      'month-day-year' => '{year} 年 {month} 月 {day} 日',
    ],
    'vietnamese' => [
      'month-day' => 'Ngày {day} tháng {month}',
      'month-year' => 'tháng {month} năm {year}',
      'month-day-year' => 'Ngày {day} tháng {month} năm {year}',
    ],
    'french' => [
      'month-day' => '{month} {day}',
      'month-year' => '{month} {year}',
      'month-day-year' => '{month} {day}, {year}',
    ],
  ];

  $datetime = DateTime::createFromFormat('m-d-Y', $date)->getTimestamp();
  $month = date("m", $datetime);
  $translatedMonth = $languageToMonth[$language][$month];
  $formatTemplate = $languageFormat[$language][$format];
  $day = date("d", $datetime);
  $year = date("Y", $datetime);

  $formattedDate = str_replace("{day}",$day,$formatTemplate); 
  $formattedDate = str_replace("{month}",$translatedMonth,$formattedDate); 
  $formattedDate = str_replace("{year}",$year,$formattedDate); 

  return $formattedDate;
}
?>
