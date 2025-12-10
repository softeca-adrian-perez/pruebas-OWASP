<?php

class Fecha
{
    const _FORMATO_VISTA_FECHA = 'd-m-Y';
    const _FORMATO_VISTA_FECHA_DE = 'd.m.Y';
    const _FORMATO_BD_FECHA = 'Y-m-d';
    const _FORMATO_VISTA_FECHA_SLASH = 'd/m/Y';

    const _FORMATO_VISTA_FECHA_HORA = 'd/m/Y H:i:s';
    const _FORMATO_VISTA_FECHA_HORA_DE = 'd.m.Y H:i:s';
    const _FORMATO_BD_FECHA_HORA = 'Y-m-d H:i:s';
    const _FORMATO_VISTA_FECHA_HORA_ = 'd/m/Y H:i';
    const _FORMATO_VISTA_FECHA_HORA_DE_ = 'd/m/Y H:i';

    const _DEFAULT_FORMATO_VISTA = '';
    const _DEFAULT_FORMATO_BD = null;
    const _DEFAULT_FORMATO = null;

    private static $_F_DIA_CON_CERO = 'd';
    private static $_F_DIA_SIN_CERO = 'j';
    private static $_F_DIA_SEMANA_NUM = 'N';
    private static $_F_SEMANA_NUM = 'W';
    private static $_F_MES_CON_CERO = 'm';
    private static $_F_MES_SIN_CERO = 'n';
    private static $_F_ANO_LARGO = 'Y';
    private static $_F_ANO_CORTO = 'y';

    private static $_F_HORA_12_CON_CERO = 'h';
    private static $_F_HORA_24_CON_CERO = 'H';
    private static $_F_HORA_12_SIN_CERO = 'g';
    private static $_F_HORA_24_SIN_CERO = 'G';
    private static $_F_MINUTOS = 'i';
    private static $_F_SEGUNDOS = 's';

    private static $_F_ZONA = 'e';

    private static $_NOMBRES_DIAS_SEMANA = array(
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
        7 => 'Domingo',
    );

    private static $_NOMBRES_MESES = array(
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre',
    );

    public static function toFormatoVista($date, $inputFormat = self::_FORMATO_BD_FECHA)
    {
        if (self::isDate($date, $inputFormat)) {
            return self::changeFormat($date, $inputFormat, self::_FORMATO_VISTA_FECHA);
        } else {
            return self::_DEFAULT_FORMATO_VISTA;
        }
    }

    public static function toFormatoBd($date, $inputFormat = self::_FORMATO_VISTA_FECHA)
    {
        if (self::isDate($date, $inputFormat)) {
            $dateArray = self::toArray($date, $inputFormat);
            return sprintf('%04d-%02d-%02d', $dateArray['year'], $dateArray['month'], $dateArray['day']);
        } else {
            return self::_DEFAULT_FORMATO_BD;
        }
    }

    public static function toFormatoVistaFechaHora($date, $inputFormat = self::_FORMATO_BD_FECHA_HORA)
    {
        if (self::isDateHour($date, $inputFormat)) {
            return self::changeFormat($date, $inputFormat, self::_FORMATO_VISTA_FECHA_HORA);
        } else {
            return self::_DEFAULT_FORMATO_VISTA;
        }
    }

    public static function toFormatoVistaFecha($date, $inputFormat = self::_FORMATO_BD_FECHA)
    {
        if (self::isDate($date, $inputFormat)) {
            return self::changeFormat($date, $inputFormat, self::_FORMATO_VISTA_FECHA);
        } else {
            return self::_DEFAULT_FORMATO_VISTA;
        }
    }

    public static function toFormatoBdFechaHora($date, $inputFormat = self::_FORMATO_VISTA_FECHA_HORA)
    {
        if (self::isDateHour($date, $inputFormat)) {
            $dateArray = self::toArray($date, $inputFormat);
            return sprintf('%04d-%02d-%02d %02d:%02d:%02d', $dateArray['year'], $dateArray['month'], $dateArray['day'], $dateArray['hour'], $dateArray['minute'], $dateArray['second']);
        } else {
            return self::_DEFAULT_FORMATO_BD;
        }
    }

    /**
     * Turns the date into Sendgrid format (yyyy-mm-ddT).
     */
    public static function toSendgridFormat($date, $inputFormat = self::_FORMATO_VISTA_FECHA)
    {
        if (self::isDate($date, $inputFormat)) {
            $dateArray = self::toArray($date, $inputFormat);
            return sprintf('%04d-%02d-%02dT%02d:%02d:%02dZ', $dateArray['year'], $dateArray['month'], $dateArray['day'], $dateArray['hour'], $dateArray['minute'], $dateArray['second']);
        } else {
            return self::_DEFAULT_FORMATO_BD;
        }
    }

    public static function changeFormat($date, $inputFormat, $formato_salida)
    {
        if (self::isDateHour($date, $inputFormat)) {
            if ($inputFormat != self::_FORMATO_BD_FECHA_HORA) {
                $date = self::toFormatoBdFechaHora($date, $inputFormat);
            }
            return date($formato_salida, strtotime($date));
        } elseif (self::isDate($date, $inputFormat)) {
            if ($inputFormat != self::_FORMATO_BD_FECHA) {
                $date = self::toFormatoBd($date, $inputFormat);
            }
            return date($formato_salida, strtotime($date));
        } else {
            return self::_DEFAULT_FORMATO;
        }
    }

    public static function isDate($date, $inputFormat = self::_FORMATO_BD_FECHA)
    {
        $dateArray = self::toArray($date, $inputFormat);
        return checkdate($dateArray['month'], $dateArray['day'], $dateArray['year']);
    }

    public static function isHour($hora)
    {
        $arrHora = explode(":", $hora);
        if (count($arrHora) != 2 && count($arrHora) != 3) {
            return false;
        }

        $hora = $arrHora[0];
        $minutos = $arrHora[1];

        if (!self::isNaturalNumber($hora) || $hora > 23 || !self::isNaturalNumber($minutos) || $minutos > 59) {
            return false;
        }
        if (count($arrHora) == 3) {
            $segundos = $arrHora[2];
            if (!self::isNaturalNumber($segundos) || $segundos > 59) {
                return false;
            }
        }

        return true;
    }

    private static function isNaturalNumber($numero)
    {
        return is_numeric($numero) && !is_float($numero) && $numero >= 0;
    }

    public static function isDateHour($txt)
    {
        $asPartes = explode(" ", $txt);
        if (count($asPartes) != 2) {
            return false;
        }

        return self::isDate($asPartes[0]) && self::isHour($asPartes[1]);
    }

    public static function toArray($date, $inputFormat = self::_FORMATO_BD_FECHA)
    {
        return date_parse_from_format($inputFormat, $date); //(PHP 5 >= 5.3.0)
    }

    public static function createDatabaseDate($year, $month, $day)
    {
        return $year . '-' . $month . '-' . $day;
    }

    public static function getDay($date, $incluir_cero = true, $inputFormat = self::_FORMATO_BD_FECHA)
    {
        if (self::isDate($date, $inputFormat)) {
            $date = self::toFormatoBd($date, $inputFormat);
            if ($incluir_cero) {
                return date(self::$_F_DIA_CON_CERO, strtotime($date));
            } else {
                return date(self::$_F_DIA_SIN_CERO, strtotime($date));
            }
        } else {
            return self::_DEFAULT_FORMATO;
        }
    }

    public static function getDayName($day, $short = false, $tam = 2)
    {
        if ($short) {
            return substr(self::$_NOMBRES_DIAS_SEMANA[$day], 0, $tam);
        } else {
            return self::$_NOMBRES_DIAS_SEMANA[$day];
        }
    }

    public static function getNumberDayWeek($date, $inputFormat = self::_FORMATO_BD_FECHA)
    {
        if (self::isDate($date, $inputFormat)) {
            $date = self::toFormatoBd($date, $inputFormat);
            return date(self::$_F_DIA_SEMANA_NUM, strtotime($date));
        } else {
            return self::_DEFAULT_FORMATO;
        }
    }

    public static function getWeek($date, $inputFormat = self::_FORMATO_BD_FECHA)
    {
        if (self::isDate($date, $inputFormat)) {
            $date = self::toFormatoBd($date, $inputFormat);
            return date(self::$_F_SEMANA_NUM, strtotime($date));
        } else {
            return self::_DEFAULT_FORMATO;
        }
    }

    public static function getMonth($date, $incluir_cero = true, $inputFormat = self::_FORMATO_BD_FECHA)
    {
        if (self::isDate($date, $inputFormat)) {
            $date = self::toFormatoBd($date, $inputFormat);
            if ($incluir_cero) {
                return date(self::$_F_MES_CON_CERO, strtotime($date));
            } else {
                return date(self::$_F_MES_SIN_CERO, strtotime($date));
            }
        } else {
            return self::_DEFAULT_FORMATO;
        }
    }

    public static function getMonthName($month, $short = false, $tam = 3)
    {
        if ($short) {
            return substr(self::$_NOMBRES_MESES[$month], 0, $tam);
        } else {
            return self::$_NOMBRES_MESES[$month];
        }
    }

    public static function getYear($date, $short = false, $inputFormat = self::_FORMATO_BD_FECHA)
    {
        if (self::isDate($date, $inputFormat)) {
            $date = self::toFormatoBd($date, $inputFormat);
            if ($short) {
                return date(self::$_F_ANO_CORTO, strtotime($date));
            } else {
                return date(self::$_F_ANO_LARGO, strtotime($date));
            }
        } else {
            return self::_DEFAULT_FORMATO;
        }
    }

    public static function getHour($date, $format24 = true, $includeZero = true, $inputFormat = self::_FORMATO_BD_FECHA_HORA)
    {
        $date = self::toFormatoBdFechaHora($date, $inputFormat);

        if ($format24) {
            $format = $includeZero ? self::$_F_HORA_24_CON_CERO : self::$_F_HORA_24_SIN_CERO;
        } else {
            $format = $includeZero ? self::$_F_HORA_12_CON_CERO : self::$_F_HORA_12_SIN_CERO;
        }

        return date($format, strtotime($date));
    }

    public static function getMinutes($date, $inputFormat = self::_FORMATO_BD_FECHA_HORA)
    {
        $date = self::toFormatoBdFechaHora($date, $inputFormat);
        return date(self::$_F_MINUTOS, strtotime($date));
    }

    public static function getSeconds($date, $inputFormat = self::_FORMATO_BD_FECHA_HORA)
    {
        $date = self::toFormatoBdFechaHora($date, $inputFormat);
        return date(self::$_F_SEGUNDOS, strtotime($date));
    }

    public static function getZone($date, $inputFormat = self::_FORMATO_BD_FECHA)
    {
        if (self::isDate($date, $inputFormat)) {
            $date = self::toFormatoBd($date, $inputFormat);
            return date(self::$_F_ZONA, strtotime($date));
        } else {
            return self::_DEFAULT_FORMATO;
        }
    }

    public static function addDays($date, $numberOfDays, $inputFormat = self::_FORMATO_BD_FECHA)
    {
        if (self::isDate($date, $inputFormat)) {
            $date = self::toFormatoBd($date, $inputFormat);
            return date($inputFormat, strtotime("+ " . $numberOfDays . " day", strtotime($date)));
        } else {
            return self::_DEFAULT_FORMATO;
        }
    }

    public static function substractDays($date, $numberOfDays, $inputFormat = self::_FORMATO_BD_FECHA)
    {
        if (self::isDate($date, $inputFormat)) {
            $date = self::toFormatoBd($date, $inputFormat);
            return date($inputFormat, strtotime("- " . $numberOfDays . " day", strtotime($date)));
        } else {
            return self::_DEFAULT_FORMATO;
        }
    }

    public static function addMonths($date, $numberOfMonths, $inputFormat = self::_FORMATO_BD_FECHA)
    {
        if (self::isDate($date, $inputFormat)) {
            $date = self::toFormatoBd($date, $inputFormat);
            return date($inputFormat, strtotime("+ " . $numberOfMonths . " month", strtotime($date)));
        } else {
            return self::_DEFAULT_FORMATO;
        }
    }

    public static function substractMonths($date, $numberOfMonths, $inputFormat = self::_FORMATO_BD_FECHA)
    {
        if (self::isDate($date, $inputFormat)) {
            $date = self::toFormatoBd($date, $inputFormat);
            return date($inputFormat, strtotime("- " . $numberOfMonths . " month", strtotime($date)));
        } else {
            return self::_DEFAULT_FORMATO;
        }
    }

    public static function addYears($date, $numberOfYears, $inputFormat = self::_FORMATO_BD_FECHA)
    {
        if (self::isDate($date, $inputFormat)) {
            $date = self::toFormatoBd($date, $inputFormat);
            return date($inputFormat, strtotime("+ " . $numberOfYears . " year", strtotime($date)));
        } else {
            return self::_DEFAULT_FORMATO;
        }
    }

    public static function substractYears($date, $numberOfYears, $inputFormat = self::_FORMATO_BD_FECHA)
    {
        if (self::isDate($date, $inputFormat)) {
            $date = self::toFormatoBd($date, $inputFormat);
            return date($inputFormat, strtotime("- " . $numberOfYears . " year", strtotime($date)));
        } else {
            return self::_DEFAULT_FORMATO;
        }
    }

    public static function getDistance($date1, $date2, $inputFormat = self::_FORMATO_BD_FECHA, $unit = 'd', $abs = false, $round = 'floor')
    {
        if (self::isDate($date1, $inputFormat) && self::isDate($date2, $inputFormat)) {
            $date1 = self::toFormatoBdFechaHora($date1, $inputFormat);
            $date2 = self::toFormatoBdFechaHora($date2, $inputFormat);
            $result = strtotime($date2) - strtotime($date1);
            switch ($unit) {
                case 'w':
                    $result = $result / (60 * 60 * 24 * 7);
                    break;
                case 'd':
                    $result = $result / (60 * 60 * 24);
                    break;
                case 'h':
                    $result = $result / (60 * 60);
                    break;
                case 'i':
                    $result = $result / 60;
                    break;
                case 's':
                    break;
                default:
                    $result = $result / (60 * 60 * 24);
            }

            switch ($round) {
                case 'floor':
                    $result = floor($result);
                    break;
                case 'round':
                    $result = round($result);
                    break;
                case 'ceil':
                    $result = ceil($result);
                    break;
                case '':
                case false:
                    break;
                default:
                    $result = floor($result);
            }

            return ($abs) ? abs($result) : $result;
        } else {
            return self::_DEFAULT_FORMATO;
        }
    }

    public static function getNextDates($date, $numberOfDays, $inputFormat = self::_FORMATO_BD_FECHA)
    {
        if (self::isDate($date, $inputFormat)) {
            $fechas = array();
            for ($i = 0; $i <= $numberOfDays; $i++) {
                $fechas[] = self::addDays($date, $i, $inputFormat);
            }
            return $fechas;
        } else {
            return self::_DEFAULT_FORMATO;
        }
    }

    public static function getPreviousDates($date, $numberOfDays, $inputFormat = self::_FORMATO_BD_FECHA)
    {
        if (self::isDate($date, $inputFormat)) {
            $fechas = array();
            for ($i = 0; $i <= $numberOfDays; $i++) {
                $fechas[] = self::substractDays($date, $i, $inputFormat);
            }
            return $fechas;
        } else {
            return self::_DEFAULT_FORMATO;
        }
    }

    public static function getNumberOfWorkingDays($month, $year, $noWorkingDays =  array('6', '7'))
    {
        $fecha_inicio = self::createDatabaseDate($year, $month, '01');
        if (self::isDate($fecha_inicio, self::_FORMATO_BD_FECHA)) {
            $numberOfDays = 0;
            $fecha_fin = self::addMonths($fecha_inicio, 1);
            for ($date = $fecha_inicio; $date != $fecha_fin; $date = self::addDays($date, 1)) {
                $day = self::getNumberDayWeek($date);
                if (!in_array($day, $noWorkingDays)) {
                    $numberOfDays++;
                }
            }
            return $numberOfDays;
        } else {
            return self::_DEFAULT_FORMATO;
        }
    }

    public static function getNumberOfDaysOfMonth($month, $year)
    {
        $date = self::createDatabaseDate($year, $month, '01');
        if (self::isDate($date, self::_FORMATO_BD_FECHA)) {
            return date('t', strtotime($date));
        } else {
            return self::_DEFAULT_FORMATO;
        }
    }

    public static function isLeapYear($year)
    {
        $date = self::createDatabaseDate($year, '01', '01');
        if (self::isDate($date, self::_FORMATO_BD_FECHA)) {
            return date('L', strtotime($date));
        } else {
            return self::_DEFAULT_FORMATO;
        }
    }

    public static function getMonthWeeks($year, $month)
    {
        $semanas = array();
        $fecha_inicio = self::createDatabaseDate($year, $month, '01');
        do {
            $semana = self::getWeekDates($fecha_inicio);
            $semanas[] = $semana;
            $fecha_inicio = self::addDays($semana['fin'], 1);
        } while ($month == self::getMonth($fecha_inicio));

        return $semanas;
    }

    public static function getWeekDates($startOfWeek)
    {
        $numberDayStartOfWeek = date('N', strtotime($startOfWeek));
        switch ($numberDayStartOfWeek) {
            case 7:
                $numberDaysToEnd = 0;
                break;
            case 1:
                $numberDaysToEnd = 6;
                break;
            case 2:
                $numberDaysToEnd = 5;
                break;
            case 3:
                $numberDaysToEnd = 4;
                break;
            case 4:
                $numberDaysToEnd = 3;
                break;
            case 5:
                $numberDaysToEnd = 2;
                break;
            case 6:
                $numberDaysToEnd = 1;
                break;
            default:
                return array();
        }

        $numberOfWeek = self::getWeek($startOfWeek);
        $month = self::getMonth($startOfWeek);
        $endOfWeek = self::addDays($startOfWeek, $numberDaysToEnd);
        while (self::getMonth($endOfWeek) != $month) {
            $endOfWeek = self::substractDays($endOfWeek, 1);
        }

        return array(
            'num' => $numberOfWeek,
            'inicio' => $startOfWeek,
            'fin' => $endOfWeek,
        );
    }

    public static function getNameOfDays()
    {
        return self::$_NOMBRES_DIAS_SEMANA;
    }

    public static function getNameOfMonths()
    {
        return self::$_NOMBRES_MESES;
    }

    public static function isDatabaseFormat($date, $separador = '-')
    {
        $tempFecha = explode($separador, $date);

        return checkdate($tempFecha[1], $tempFecha[2], $tempFecha[0]);
    }

    public static function shortNameMonth($numberOfMonth)
    {
        $names = array(
            1 => __t('Date.Short_month_1'),
            __t('Date.Short_month_2'),
            __t('Date.Short_month_3'),
            __t('Date.Short_month_4'),
            __t('Date.Short_month_5'),
            __t('Date.Short_month_6'),
            __t('Date.Short_month_7'),
            __t('Date.Short_month_8'),
            __t('Date.Short_month_9'),
            __t('Date.Short_month_10'),
            __t('Date.Short_month_11'),
            __t('Date.Short_month_12'),
        );
        return isset($names[$numberOfMonth]) ? $names[$numberOfMonth] : null;
    }

    public static function converUtcToTimeZoneNow($format)
    {
        $date = new \DateTime(date($format),  new \DateTimeZone('Europe/Madrid'));
        $newTZ = new DateTimeZone(Configure::read('Config.timezone'));
        $date->setTimezone($newTZ);
        return $date->format($format);
    }

    public static function toDateViewFormatSlash($date, $inputFormat = self::_FORMATO_BD_FECHA)
    {
        if (self::isDate($date, $inputFormat)) {
            return self::changeFormat($date, $inputFormat, self::_FORMATO_VISTA_FECHA_SLASH);
        } else {
            return self::_DEFAULT_FORMATO_VISTA;
        }
    }

    public static function getCompleteDate()
    {
        return '_' . date('Ymd_') . time();
    }
}
