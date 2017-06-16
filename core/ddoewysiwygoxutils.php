<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

/**
 * @package core
 */
class ddoewysiwygoxutils extends ddoewysiwygoxutils_parent
{

    public function showJsonAndExit( $mMsg = null )
    {
        $this->showMessageAndExit( $this->encodeJson( $mMsg ) );
    }


    public function encodeJson( $mMsg = null )
    {
        if ( is_string( $mMsg ) )
        {
            if( !$this->isUtfString( $mMsg ) )
            {
                $mMsg = utf8_encode( $mMsg );
            }
        }
        else
        {
            // Typecast for Objects
            if ( is_object( $mMsg ) )
            {
                $mMsg = ( array )$mMsg;
            }

            $mMsg = $this->_encodeUtf8Array( $mMsg );
        }

        return json_encode( $mMsg );
    }


    public function isUtfString( $sString = '' )
    {
        if( is_string( $sString ) && ( function_exists( 'mb_detect_encoding' ) && mb_detect_encoding( $sString, 'UTF-8', true ) !== false ) )
        {
            return true;
        }

        return false;
    }


    protected function _encodeUtf8Array( $aArray )
    {
        $aRet = array();

        foreach ( $aArray as $sKey => $mValue )
        {
            if( !$this->isUtfString( $mValue ) )
            {
                $sKey = utf8_encode( $sKey );
            }

            if ( is_string( $mValue ) )
            {
                if( !$this->isUtfString( $mValue ) )
                {
                    $mValue = utf8_encode( $mValue );
                }
            }
            elseif ( is_array( $mValue ) )
            {
                $mValue = $this->_encodeUtf8Array( $mValue );
            }

            $aRet[ $sKey ] = $mValue;
        }

        return $aRet;
    }
}