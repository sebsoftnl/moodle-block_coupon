<?php

class admin_setting_customIntConfigText extends admin_setting {

    /** @var mixed int means PARAM_XXX type, string is a allowed format in regex */
    public $paramtype;
    /** @var int default field size */
    public $size;

    /**
     * Config text constructor
     *
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting
     * @param mixed $paramtype int means PARAM_XXX type, string is a allowed format in regex
     * @param int $size default field size
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $paramtype=PARAM_RAW, $size=null) {
        $this->paramtype = $paramtype;
        if (!is_null($size)) {
            $this->size  = $size;
        } else {
            $this->size  = ($paramtype === PARAM_INT) ? 5 : 30;
        }
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * Return the setting
     *
     * @return mixed returns config if successful else null
     */
    public function get_setting() {
        return $this->config_read($this->name);
    }

    public function write_setting($data) {
        if ($this->paramtype === PARAM_INT and $data === '') {
        // do not complain if '' used instead of 0
            $data = 0;
        }
        // $data is a string
        $validated = $this->validate($data);
        if ($validated !== true) {
//            exit(var_dump($validated));
            return $validated;
        }
        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Validate data before storage
     * @param string data
     * @return mixed true if ok string if error found
     */
    public function validate($data) {

        // Make sure its an integer
        if (!is_numeric($data)) return(get_string('error:code_length_not_numeric', BLOCK_VOUCHER));
        
        if ($data <= 6 || $data >= 32) return(get_string('error:wrong_code_length', BLOCK_VOUCHER));

        return true;
    }

    /**
     * Return an XHTML string for the setting
     * @return string Returns an XHTML string
     */
    public function output_html($data, $query='') {
        $default = $this->get_defaultsetting();

        return format_admin_setting($this, $this->visiblename,
        '<div class="form-text defaultsnext"><input type="text" size="'.$this->size.'" id="'.$this->get_id().'" name="'.$this->get_full_name().'" value="'.s($data).'" /></div>',
        $this->description, true, '', $default, $query);
    }
}
?>