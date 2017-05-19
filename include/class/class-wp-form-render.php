<?php
namespace DTSettings;
/**
 * Class Name: WPForm ( :: render )
 * Class URI: https://github.com/nikolays93/WPForm
 * Description: render forms as wordpress fields
 * Version: 1.2
 * Author: NikolayS93
 * Author URI: https://vk.com/nikolays_93
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

function _isset_default(&$var, $default, $unset = false){
  $result = $var = isset($var) ? $var : $default;
  if($unset)
    $var = FALSE;
  return $result;
}
function _isset_false(&$var, $unset = false){ return _isset_default( $var, false, $unset ); }
function _isset_empty(&$var, $unset = false){ return _isset_default( $var, '', $unset ); }

/**
 * change names for wordpress options
 * @param  array  $inputs      rendered inputs
 * @param  string $option_name name of wordpress option ( @see get_option() )
 * @return array               filtred inputs
 */
add_filter( 'DTSettings\dt_admin_options', 'DTSettings\admin_page_options_filter', 10, 2 );
function admin_page_options_filter( $inputs, $option_name = false ){
  if( ! $option_name )
    $option_name = _isset_false($_GET['page']);

  if( ! $option_name )
    return $inputs;

  if( isset($inputs['id']) )
    $inputs = array($inputs);

  foreach ( $inputs as &$input ) {
    if( isset($input['name']) )
      $input['name'] = "{$option_name}[{$input['name']}]";
    else
      $input['name'] = "{$option_name}[{$input['id']}]";
    
    $input['check_active'] = 'id';
  }
  return $inputs;
}


class WPForm {
  static protected $clear_value;

  /**
   * EXPEREMENTAL!
   * Get ID => Default values from $render_data
   * @param  array() $render_data
   * @return array(array(ID=>default),ar..)
   */
  public static function defaults( $render_data ){
    $defaults = array();
    if(empty($render_data))
      return $defaults;

    if( isset($render_data['id']) )
        $render_data = array($render_data);

    foreach ($render_data as $input) {
      if(isset($input['default']) && $input['default']){
        $input['id'] = str_replace('][', '_', $input['id']);
        $defaults[$input['id']] = $input['default'];
      }
    }

    return $defaults;
  }
  
  /**
   * EXPEREMENTAL! todo: add recursive handle
   * @param  string  $option_name      
   * @param  string  $sub_name         $option_name[$sub_name]
   * @param  boolean $is_admin_options recursive split value array key with main array
   * @return array                     installed options
   */
  public static function active($option_name, $sub_name = false, $is_admin_options = false){
    $active = get_option( $option_name, array() );
    if( $sub_name && isset($active[$sub_name]) && is_array($active[$sub_name]) )
      $active = $active[$sub_name];
    elseif( $sub_name && !isset($active[$sub_name]) )
      return false;

    if(!is_array($active))
        return false;

    if( $is_admin_options === true ){
      $result = array();
      foreach ($active as $key => $value) {
        if( is_array($value) ){
          foreach ($value as $key2 => $value2) {
            $result[$key . '_' . $key2] = $value2;
          }
        }
        else {
          $result[$key] = $value;
        }
      }

      return $result;
      // function self_function($active){
      //   foreach ($active as &$key => &$value) {
      //     if( is_array($value) ){
      //       $key = 
      //     }
      //   }
      // }
    }

    return $active;
  }

  /**
   * Render form items
   * @param  boolean $render_data array with items ( id, name, type, options..)
   * @param  array   $active      selected options from form items
   * @param  boolean $is_table    is a table
   * @param  array   $args        array of args (item_wrap, form_wrap, label_tag, hide_desc) @see $default_args
   * @param  boolean $is_not_echo true = return, false = echo
   * @return html                 return or echo
   */
  public static function render(
    $render_data = false,
    $active = array(),
    $is_table = false,
    $args = array(),
    $is_not_echo = false){

    $html = $hidden = array();

    if( empty($render_data) ){
      if( function_exists('is_wp_debug') && is_wp_debug() )
        echo '<pre> Файл настроек не найден </pre>';
      return false;
    }
    
    if( isset($render_data['id']) )
        $render_data = array($render_data);

    if($active === false)
      $active = array();
    
    $default_args = array(
      'item_wrap' => array('<p>', '</p>'),
      'form_wrap' => array('<table class="table form-table"><tbody>', '</tbody></table>'),
      'label_tag' => 'th',
      'hide_desc' => false,
      'clear_value' => 'false'
      );
    $args = array_merge($default_args, $args);
    self::$clear_value = $args['clear_value'];
    if( $args['item_wrap'] === false )
      $args['item_wrap'] = array('', '');

    if($args['form_wrap'] === false)
      $args['form_wrap'] = array('', '');

    if( $args['label_tag'] == 'th' && $is_table == false ){
      $args['label_tag'] = 'label';
    }
    /**
     * Template start
     */
    if($is_table)
        $html[] = $args['form_wrap'][0];

    foreach ( $render_data as $input ) {
      $label   = _isset_false($input['label'], 1);
      $before  = _isset_empty($input['before'], 1);
      $after   = _isset_empty($input['after'], 1);
      $default = _isset_false($input['default'], 1);
      $value   = _isset_false($input['value']);
      $check_active = _isset_false($input['check_active'], 1);
      
      if( $input['type'] != 'checkbox' && $input['type'] != 'radio' )
        _isset_default( $input['placeholder'], $default );

      if( isset($input['desc']) ){
        $desc = $input['desc'];
        $input['desc'] = false;
      }
      elseif( isset( $input['description'] ) ) {
        $desc = $input['description'];
        $input['description'] = false;
      }
      else {
        $desc = false;
      }

      if( !isset($input['name']) )
          $input['name'] = _isset_empty($input['id']);

      $input['id'] = str_replace('][', '_', $input['id']);
      
      /**
       * set values
       */
      $active_name = $check_active ? $input[$check_active] : str_replace('[]', '', $input['name']);
      $active_value = ( is_array($active) && sizeof($active) > 0 && isset($active[$active_name]) ) ?
         $active[$active_name] : false;

      $entry = '';
      if($input['type'] == 'checkbox' || $input['type'] == 'radio'){
        $entry = self::is_checked( $value, $active_value, $default );
      }
      elseif( $input['type'] == 'select' ){
        $entry = ($active_value) ? $active_value : $default;
      }
      else {
        // if text, textarea, number, email..
        $entry = $active_value;
        $placeholder = $default;
      }

      switch ($input['type']) {
        case 'text':
        case 'hidden':
        case 'submit':
        case 'button':
        case 'number':
        case 'email':
          $func = 'render_text';
          break;
        
        default:
          $func = 'render_' . $input['type'];
          break;
      }
      
      $input_html = self::$func($input, $entry, $is_table, $label);

      if( $desc ){
        // todo: set tooltip
        if( isset($args['hide_desc']) && $args['hide_desc'] === true )
          $desc_html = "<div class='description' style='display: none;'>{$desc}</div>";
        else
          $desc_html = "<span class='description'>{$desc}</span>";
      } else {
        $desc_html = '';
      }
      
      if(!$is_table){
        $html[] = $before . $args['item_wrap'][0] . $input_html . $args['item_wrap'][1] . $after . $desc_html;
      }
      elseif( $input['type'] == 'hidden' ){
        $hidden[] = $before . $input_html . $after;
      }
      elseif( $input['type'] == 'html' ){
        $html[] = $args['form_wrap'][1];
        $html[] = $before . $input_html . $after;
        $html[] = $args['form_wrap'][0];
      }
      else {
        $item = $before . $args['item_wrap'][0]. $input_html .$args['item_wrap'][1] . $after;

        $html[] = "<tr id='{$input['id']}'>";
        $html[] = "  <{$args['label_tag']} class='label'>{$label}</{$args['label_tag']}>";
        $html[] = "  <td>";
        $html[] = "    " .$item;
        $html[] = $desc_html;
        $html[] = "  </td>";
        $html[] = "</tr>";
      }
    } // endforeach
    if($is_table)
      $html[] = $args['form_wrap'][1];

    $result = implode("\n", $html) . "\n" . implode("\n", $hidden);
    if( $is_not_echo )
      return $result;
    else
      echo $result;
  }

  /**
   * check if is checked ( called( $value, $active_value, $default ) )
   * @param  mixed         $value   ['value'] array setting (string || boolean)(!isset ? false)
   * @param  string||false $active  value from $active option
   * @param  mixed         $default ['default'] array setting (string || boolean)(!isset ? false)
   * 
   * @return boolean       checked or not
   */
  private static function is_checked( $value, $active, $default ){
    if( $active === false && $value )
      return true;

    $checked = ( $active === false ) ? false : true;
    if( $active === 'false' || $active === 'off' || $active === '0' )
      $active = false;

    if( $active === 'true'  || $active === 'on'  || $active === '1' )
      $active = true;

    if( $active || $default ){
      if( $value ){
        if( is_array($active) ){
          if( in_array($value, $active) )
            return true;
        }
        else {
          if( $value == $active || $value === true )
            return true;
        }
      }
      else {
        if( $active || (!$checked && $default) )
          return true;
      }
      return false;
    }
  }

  public static function render_checkbox( $input, $checked, $is_table, $label = '' ){
    $result = '';

    if( empty($input['value']) )
      $input['value'] = 'on';

    if( $checked )
      $input['checked'] = 'true';

    // if $clear_value === false dont use defaults (couse default + empty value = true)
    $cv = self::$clear_value;
    if( false !== $cv )
      $result .= "<input name='{$input['name']}' type='hidden' value='{$cv}'>\n";

    $result .= "<input";
    foreach ($input as $attr => $val) {
      if($val){
        $attr = esc_attr($attr);
        $val  = esc_attr($val);
        $result .= " {$attr}='{$val}'";
      }
    }
    $result .= ">";

    if(!$is_table && $label)
      $result .= "<label for='{$input['id']}'> {$label} </label>";

    return $result;
  }

  public static function render_select( $input, $active_id, $is_table, $label = '' ){
    $result = '';
    $options = _isset_false($input['options'], 1);
    if(! $options )
      return false;

    if(!$is_table && $label)
      $result .= "<label for='{$input['id']}'> {$label} </label>";

    $result .= "<select";
    foreach ($input as $attr => $val) {
      if( $val ){
        $attr = esc_attr($attr);
        $val  = esc_attr($val);
        $result .= " {$attr}='{$val}'";
      }
    }
    $result .= ">";
    foreach ($options as $value => $option) {
      $active_str = ($active_id == $value) ? " selected": "";
      $result .= "<option value='{$value}'{$active_str}>{$option}</option>";
    }
    $result .= "</select>";

    return $result;
  }

  public static function render_textarea( $input, $entry, $is_table, $label = '' ){
    $result = '';
    // set defaults
    _isset_default($input['rows'], 5);
    _isset_default($input['cols'], 40);

    if(!$is_table && $label)
      $result .= "<label for='{$input['id']}'> {$label} </label>";

    $result .= "<textarea";
    foreach ($input as $attr => $val) {
      if($val){
        $attr = esc_attr($attr);
        $val  = esc_attr($val);
        $result .= " {$attr}='{$val}'";
      }
    }
    $result .= ">{$entry}</textarea>";

    return $result;
  }

  public static function render_text( $input, $entry, $is_table, $label = '' ){
    $result = '';

    if(!$is_table && $label)
      $result .= "<label for='{$input['id']}'> {$label} </label>";
    if( $entry )
      $input['value'] = $entry;

    $result .= "<input";
    foreach ($input as $attr => $val) {
      if( $val ){
        $attr = esc_attr($attr);
        $val  = esc_attr($val);
        $result .= " {$attr}='{$val}'";
      }
    }
    $result .= ">";

    return $result;
  }
  public static function render_html( $input, $entry, $is_table, $label = '' ){
    return $input['value'];
  }
}