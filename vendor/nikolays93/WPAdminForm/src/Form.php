<?php

namespace NikolayS93\WPAdminForm;

class Form extends Active
{
    // protected $inputs;

    static $clear_value = false;
    protected $fields,
              $args,
              $hiddens = array();

    public function __construct($data = null, $is_table = true, $args = array())
    {
        if( !is_array($data) )
            $data = array();

        if( isset($data['id']) || isset($data['name']) )
            $data = array($data);

        if( !is_array($args) )
            $args = array();

        $args = Preset::parse_args($args, $is_table);
        if( $args['admin_page'] ) { // || $args['sub_name']
            foreach ($data as &$field) {
                if ( ! isset($field['id']) && ! isset($field['name']) )
                    continue;

                // if( $args['admin_page'] ) {
                $field_name = isset($field['name']) ? $field['name'] : $field['id'];
                $field['name'] = $args['sub_name'] ?
                    sprintf('%s[%s][%s]', $args['admin_page'], $args['sub_name'], $field_name) :
                    sprintf('%s[%s]', $args['admin_page'], $field_name);

                if( !isset($field['check_active']) )
                    $field['check_active'] = 'id';
                // }
            }
        }

        $this->fields = $data;
        $args['is_table'] = $is_table;
        $this->args = $args;
    }

    final public function display()
    {
        $arrActive = $this->get( $this->args );

        $html = $this->args['form_wrap'][0];
        foreach ($this->fields as $field) {
            if ( ! isset($field['id']) && ! isset($field['name']) )
                continue;

            $active_key = $field['check_active'] ?
                $field[ $field['check_active'] ] :
                str_replace('[]', '', $field['name']);
            $active_value = isset( $arrActive[ $active_key ] ) ? $arrActive[ $active_key ] : false;

            // &$field
            $input = new Input( $field, $active_value, $this->args );
            $html .= new Field( $field, $input, $this->args );
        }
        $html .= $this->args['form_wrap'][1];

        $result = $html . "\n" . implode("\n", $this->hiddens);

        echo $result;
    }
}
