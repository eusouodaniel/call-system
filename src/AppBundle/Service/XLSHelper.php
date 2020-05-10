<?php

namespace AppBundle\Service;

/**
 * Classe utilitária de strings do projeto RockBee.
 *
 */
class XLSHelper {

    static function titleStyle() {
        $titleStyle = array(
            'fill' => array(
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'F2F2F2')
            ),
            'font' => array(
                'bold' => true,
                'size' => 22,
            ),
            'alignment' => array(
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );

        return $titleStyle;
    }

    static function boldStyle() {
        $boldStyle = array(
            'font' => array(
                'bold' => true,
            )
        );

        return $boldStyle;
    }

    static function headerStyle() {
        $headerStyle = array(
            'fill' => array(
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'D9D9D9')
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );

        return $headerStyle;
    }

}
