<?php
namespace AppBundle\Helper;

/**
 * Classe utilitária de strings do projeto RockBee.
 *
 */
class StringHelper {

    /**
     * Remove caracteres acentuados.
     * @param string $text Texto a ser substituído.
     * @return string Texto com caacteres acentuados removidos.
     */
    static public function removeAccentuation($text) {
        $search = explode(",", "ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u,ã");
        $replace = explode(",", "c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u,a");
        return str_replace($search, $replace, $text);
    }

    /**
     * Método utilizatário para retornar o slug de uma string.
     * @param string $text String da qual o slug será gerado.
     * @return string Slug do parâmetro fornecido.
     */
    static public function slugify($text) {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        if (function_exists('iconv')) {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    /**
     * Método de substring inteligente que não corta palavras, incluindo ... no fim.
     * @param string $text Texto a ser cortado.
     * @param integer $length Tamanho máximo.
     * @param string $tail Cauda a ser inserida no fim da string.
     * @return string String recortado.
     */
    static public function substringByWords($text, $length = 64, $tail = "...") {
        $text = trim($text);
        $txtl = strlen($text);
        if ($txtl > $length) {
            for ($i = 1; $text[$length - $i] != " "; $i++) {
                if ($i == $length) {
                    return substr($text, 0, $length) . $tail;
                }
            }
            $text = substr($text, 0, $length - $i + 1) . $tail;
        }
        return $text;
    }
}
