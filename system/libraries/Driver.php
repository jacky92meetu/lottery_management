<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CI_Driver_Library {

	protected $valid_drivers	= array();
	protected static $lib_name;

	// The first time a child is used it won't exist, so we instantiate it
	// subsequents calls will go straight to the proper child.
	function __get($child)
	{
		if ( ! isset($this->lib_name))
		{
			$this->lib_name = get_class($this);
		}

		// The class will be prefixed with the parent lib
		$child_class = $this->lib_name.'_'.$child;

		if (in_array(strtolower($child_class), array_map('strtolower', $this->valid_drivers)))
		{
			// check and see if the driver is in a separate file
			if ( ! class_exists($child_class))
			{
				// check application path first
				foreach (array(APPPATH, BASEPATH) as $path)
				{
					// and check for case sensitivity of both the parent and child libs
					foreach (array(ucfirst($this->lib_name), strtolower($this->lib_name)) as $lib)
					{
						// loves me some nesting!
						foreach (array(ucfirst($child_class), strtolower($child_class)) as $class)
						{
							$filepath = $path.'libraries/'.$this->lib_name.'/drivers/'.$child_class.EXT;

							if (file_exists($filepath))
							{
								include_once $filepath;
								break;
							}
						}
					}
				}

				// it's a valid driver, but the file simply can't be found
				if ( ! class_exists($child_class))
				{
					log_message('error', "Unable to load the requested driver: ".$child_class);
					show_error("Unable to load the requested driver: ".$child_class);
				}
			}

			$obj = new $child_class;
			$obj->decorate($this);
			$this->$child = $obj;
			return $this->$child;
		}

		// The requested driver isn't valid!
		log_message('error', "Invalid driver requested: ".$child_class);
		show_error("Invalid driver requested: ".$child_class);
	}

	// --------------------------------------------------------------------

}

class CI_Driver {
	protected $parent;

	private $methods = array();
	private $properties = array();

	private static $reflections = array();

	/**
	 * Decorate
	 *
	 * Decorates the child with the parent driver lib's methods and properties
	 *
	 * @param	object
	 * @return	void
	 */
	public function decorate($parent)
	{
		$this->parent = $parent;

		// Lock down attributes to what is defined in the class
		// and speed up references in magic methods

		$class_name = get_class($parent);

		if ( ! isset(self::$reflections[$class_name]))
		{
			$r = new ReflectionObject($parent);

			foreach ($r->getMethods() as $method)
			{
				if ($method->isPublic())
				{
					$this->methods[] = $method->getName();
				}
			}

			foreach ($r->getProperties() as $prop)
			{
				if ($prop->isPublic())
				{
					$this->properties[] = $prop->getName();
				}
			}

			self::$reflections[$class_name] = array($this->methods, $this->properties);
		}
		else
		{
			list($this->methods, $this->properties) = self::$reflections[$class_name];
		}
	}

	// --------------------------------------------------------------------

	/**
	 * __call magic method
	 *
	 * Handles access to the parent driver library's methods
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @return	mixed
	 */
	public function __call($method, $args = array())
	{
		if (in_array($method, $this->methods))
		{
			return call_user_func_array(array($this->parent, $method), $args);
		}

		$trace = debug_backtrace();
		_exception_handler(E_ERROR, "No such method '{$method}'", $trace[1]['file'], $trace[1]['line']);
		exit;
	}

	// --------------------------------------------------------------------

	/**
	 * __get magic method
	 *
	 * Handles reading of the parent driver library's properties
	 *
	 * @param	string
	 * @return	mixed
	 */
	public function __get($var)
	{
		if (in_array($var, $this->properties))
		{
			return $this->parent->$var;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * __set magic method
	 *
	 * Handles writing to the parent driver library's properties
	 *
	 * @param	string
	 * @param	array
	 * @return	mixed
	 */
	public function __set($var, $val)
	{
		if (in_array($var, $this->properties))
		{
			$this->parent->$var = $val;
		}
	}

	// --------------------------------------------------------------------

}

?>