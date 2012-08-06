<?php
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    PasswordStrength
 * @version    1.0
 * @author     Jason Judge, Consilience Media Ltd
 * @license    Licensed under the GPL license, see http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright  Code ported from Password Strength (0.1.4) by Sagie Maoz
 * @link       http://fuelphp.com
 *
 * This class emulates the passwordstrength jQuery plugin, so the password policy
 * can be enforced at the server side to match the strength displayed to the user 
 * at the client side.
 * See https://github.com/n0nick/password_strength for the jQuery plugin
 */

namespace \Consilience\PasswordStrength;

class PasswordStrength
{
    // The minimum length of a password.
    public static $minLength = 8;

    public static strenghTexts = array(
        1 => 'Very weak',
        2 => 'Weak',
        3 => 'Average',
        4 => 'Strong',
        5 => 'Very strong',
    );

    public static function countRegexp($val, $rex)
    {
        // The third parameter is supposed to be optional, but throws an error if not supplied.
        return preg_match_all($rex, $val, $dummy);
    }

    public static function getStrength($val, $minLength = NULL)
    {
        $len = strlen($val);

        // Too short. This trumps all other checks.
        if ($len < $minLength) {
            return 0;
        }

        if (!isset($minLength)) $minLength = static::$minLength;

        // Get a count of the instances of each type of character.
        $nums = PasswordStrength::countRegexp($val, '/\d/');
        $lowers = PasswordStrength::countRegexp($val, '/[a-z]/');
        $uppers = PasswordStrength::countRegexp($val, '/[A-Z]/');
        $specials = $len - $nums - $lowers - $uppers;
        
        // Just one type of character fills the whole password.
        if ($nums == $len || $lowers == $len || $uppers == $len || $specials == $len) {
            return 1;
        }
        
        $strength = 0;
        if ($nums) $strength += 2;

        // A mix of both upper and lower ranks higher than just one or the other.
        if ($lowers) $strength += ($uppers ? 4 : 3);
        if ($uppers) $strength += ($lowers ? 4 : 3);

        if ($specials) $strength += 5;
        if ($len > 10) $strength += 1;

        return $strength;
    }

    // Returns the strength of a password as a numeric level.
    // 1 is the worst and 5 is the best.
    public static function getStrengthLevel($val, $minLength = NULL)
    {
        if (!isset($minLength)) $minLength = static::$minLength;

        $strength = static::getStrength($val, $minLength);

        $val = 1;
        if ($strength <= 0) {
            $val = 1;
        } else if ($strength > 0 && $strength <= 4) {
            $val = 2;
        } else if ($strength > 4 && $strength <= 8) {
            $val = 3;
        } else if ($strength > 8 && $strength <= 12) {
            $val = 4;
        } else if ($strength > 12) {
            $val = 5;
        }

        return $val;
    }
}
