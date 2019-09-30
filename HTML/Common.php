<?php

/**
 * Base class for all HTML classes
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 * 
 * @package HTML_Common
 * @author Adam Daniel <adaniel1@eesus.jnj.com>
 * @copyright 2001-2009 The PHP Group
 * @see http://pear.php.net/package/HTML_Common/
 */ 
abstract class HTML_Common
{
    protected static $charset = 'ISO-8859-1';

    /**
     * @var array $_attributes Associative array of attributes
     */
    protected $_attributes = array();

    /**
     * @var int $_tabOffset Tab offset of the tag
     */
    protected $_tabOffset = 0;

    /**
     * @var string $_tab Tab string
     */
    protected $_tab = "\11";

    /**
     * @var string $_lineEnd Contains the line end string
     */
    protected $_lineEnd = "\12";

    /**
     * @var string $_comment HTML comment on the object
     */
    protected $_comment = '';

    /**
     * Class constructor
     *
     * @param array|string|null $attributes Associative array of table tag attributes
     *  or HTML attributes name="value" pairs
     * @param int $tabOffset Indent offset in tabs
     */
    public function __construct($attributes = null, $tabOffset = 0)
    {
        $this->setAttributes($attributes);
        $this->setTabOffset($tabOffset);
    }

    public function HTML_Common($attributes = null, $tabOffset = 0)
    {
        self::__construct($attributes, $tabOffset);
    }
    
    /**
     * Returns the current API version
     *
     * @return float
     */
    public function apiVersion()
    {
        return 2.0;
    }

    /**
     * Returns the lineEnd
     *
     * @return string
     */
    protected function _getLineEnd()
    {
        return $this->_lineEnd;
    }

    /**
     * Returns a string containing the unit for indenting HTML
     *
     * @return string
     */
    protected function _getTab()
    {
        return $this->_tab;
    }

    /**
     * Returns a string containing the offset for the whole HTML code
     *
     * @return string
     */
    protected function _getTabs()
    {
        return str_repeat($this->_getTab(), $this->_tabOffset);
    }

    /**
     * Returns an HTML formatted attribute string
     *
     * @param array $attributes
     * @return string
     */
    protected function _getAttrString($attributes)
    {
        $strAttr = '';

        if (is_array($attributes)) {
            $charset = self::charset();
            foreach ($attributes as $key => $value) {
                $strAttr .= ' ' . $key . '="' . htmlspecialchars($value, ENT_COMPAT, $charset) . '"';
            }
        }
        return $strAttr;
    }

    /**
     * Returns a valid attributes array from either a string or array
     *
     * @param array|string|null $attributes Either a typical HTML attribute string or an associative array
     * @return array
     */
    protected function _parseAttributes($attributes)
    {
        $ret = [];

        if (is_array($attributes)) {
            $ret = array();
            foreach ($attributes as $key => $value) {
                if (is_int($key)) {
                    $key = $value = strtolower($value);
                } else {
                    $key = strtolower($key);
                }
                $ret[$key] = $value;
            }

        } elseif (is_string($attributes)) {
            $preg = "/(([A-Za-z_:]|[^\\x00-\\x7F])([A-Za-z0-9_:.-]|[^\\x00-\\x7F])*)" .
                "([ \\n\\t\\r]+)?(=([ \\n\\t\\r]+)?(\"[^\"]*\"|'[^']*'|[^ \\n\\t\\r]*))?/";
            if (preg_match_all($preg, $attributes, $regs)) {
                for ($counter=0; $counter<count($regs[1]); $counter++) {
                    $name  = $regs[1][$counter];
                    $check = $regs[0][$counter];
                    $value = $regs[7][$counter];
                    if (trim($name) == trim($check)) {
                        $ret[strtolower(trim($name))] = strtolower(trim($name));
                    } else {
                        if (substr($value, 0, 1) == "\"" || substr($value, 0, 1) == "'") {
                            $ret[strtolower(trim($name))] = substr($value, 1, -1);
                        } else {
                            $ret[strtolower(trim($name))] = trim($value);
                        }
                    }
                }
            }
        }

        return $ret;
    }

    /**
     * Returns the array key for the given non-name-value pair attribute
     *
     * @param string $attr Attribute
     * @param array $attributes Array of attribute
     * @return bool|null
     */
    protected function _getAttrKey($attr, $attributes)
    {
        if (isset($attributes[strtolower($attr)])) {
            return true;
        } else {
            return null;
        }
    }

    /**
     * Updates the attributes in $attr1 with the values in $attr2 without changing the other existing attributes
     *
     * @param array $attr1 Original attributes array
     * @param array $attr2 New attributes array
     * @return false|null
     */
    protected function _updateAttrArray(&$attr1, $attr2)
    {
        if (!is_array($attr2)) {
            return false;
        }
        foreach ($attr2 as $key => $value) {
            $attr1[$key] = $value;
        }

        return null;
    }

    /**
     * Removes the given attribute from the given array
     *
     * @param string $attr Attribute name
     * @param array $attributes Attribute array
     * @return void
     */
    protected function _removeAttr($attr, &$attributes)
    {
        $attr = strtolower($attr);
        if (isset($attributes[$attr])) {
            unset($attributes[$attr]);
        }
    }

    /**
     * Returns the value of the given attribute
     *
     * @param string $attr Attribute name
     * @return string|null returns null if an attribute does not exist
     */
    public function getAttribute($attr)
    {
        $attr = strtolower($attr);
        if (isset($this->_attributes[$attr])) {
            return $this->_attributes[$attr];
        }
        return null;
    }

    /**
     * Sets the value of the attribute
     *
     * @param string Attribute name
     * @param string Attribute value (will be set to $name if omitted)
     * @return void
     */
    public function setAttribute($name, $value = null)
    {
        $name = strtolower($name);
        if (is_null($value)) {
            $value = $name;
        }
        $this->_attributes[$name] = $value;
    }

    /**
     * Sets the HTML attributes
     *
     * @param array|string $attributes Either a typical HTML attribute string or an associative array
     * @return void
     */
    public function setAttributes($attributes)
    {
        $this->_attributes = $this->_parseAttributes($attributes);
    }

    /**
     * Returns the assoc array (default) or string of attributes
     *
     * @param bool $asString Whether to return the attributes as string
     * @return array|string attributes
     */
    public function getAttributes($asString = false)
    {
        if ($asString) {
            return $this->_getAttrString($this->_attributes);
        } else {
            return $this->_attributes;
        }
    }

    /**
     * Updates the passed attributes without changing the other existing attributes
     *
     * @param array|string $attributes Either a typical HTML attribute string or an associative array
     * @return void
     */
    public function updateAttributes($attributes)
    {
        $this->_updateAttrArray($this->_attributes, $this->_parseAttributes($attributes));
    }

    /**
     * Removes an attribute
     *
     * @param string $attr Attribute name
     * @return void
     */
    public function removeAttribute($attr)
    {
        $this->_removeAttr($attr, $this->_attributes);
    }

    /**
     * Sets the line end style to Windows, Mac, Unix or a custom string.
     *
     * @param string $style "win", "mac", "unix" or custom string.
     * @return void
     */
    public function setLineEnd($style)
    {
        switch ($style) {
            case 'win':
                $this->_lineEnd = "\15\12";
                break;
            case 'unix':
                $this->_lineEnd = "\12";
                break;
            case 'mac':
                $this->_lineEnd = "\15";
                break;
            default:
                $this->_lineEnd = $style;
        }
    }

    /**
     * Sets the tab offset
     *
     * @param int $offset
     * @return void
     */
    public function setTabOffset($offset)
    {
        $this->_tabOffset = $offset;
    }

    /**
     * Returns the tabOffset
     *
     * @return int
     */
    public function getTabOffset()
    {
        return $this->_tabOffset;
    }

    /**
     * Sets the string used to indent HTML
     *
     * @param string $string String used to indent ("\11", "\t", '  ', etc.).
     * @return void
     */
    public function setTab($string)
    {
        $this->_tab = $string;
    }

    /**
     * Sets the HTML comment to be displayed at the beginning of the HTML string
     *
     * @param string $comment
     * @return void
     */
    public function setComment($comment)
    {
        $this->_comment = $comment;
    }

    /**
     * Returns the HTML comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->_comment;
    }

    /**
     * Returns the objects HTML
     *
     * @return string
     */
    abstract public function toHtml();

    /**
     * Displays the HTML to the screen
     *
     * @return void
     */
    public function display()
    {
        print $this->toHtml();
    }

    /**
     * Sets and gets the charset to use by htmlspecialchars() function
     *
     * Since this parameter is expected to be global, the function is designed
     * to be called statically:
     * <code>
     * HTML_Common::charset('utf-8');
     * </code>
     * or
     * <code>
     * $charset = HTML_Common::charset();
     * </code>
     *
     * Consult the htmlspecialchars() docs for a list of supported character sets.
     *
     * @param string $newCharset New charset to use. Omit if just getting the current value.
     * @return string Current charset
     * @static
     */
    public static function charset($newCharset = null)
    {
        if (!is_null($newCharset)) {
            self::$charset = $newCharset;
        }
        return self::$charset;
    }
}
