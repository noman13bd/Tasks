<?php 
namespace Task\Task;

use Db;
use Task\Database\DbQuery;
use Task\ObjectModel;

class Task extends ObjectModel {
	/** @var $id Task ID */
	public $id;

	/** @var int $parent_id */
	public $parent_id;

	/** @var int $user_id */
	public $user_id;
	
	/** @var string $title */
	public $title;

	/** @var int $points */
	public $points;

	/** @var int $is_done */
	public $is_done;
	
	/** @var $created_at */
    public $created_at;
	
	/** @var $updated_at */
    public $updated_at;

	/**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'tasks',
        'primary' => 'id',
        'fields' => array(
			'parent_id' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 11),
			'user_id' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 11),
			'title' => array('type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isString', 'size' => 250),
			'points' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 11),
			'is_done' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 11),
			'created_at' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'updated_at' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        )
    );

     /**
     * constructor.
     *
     * @param null $id
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
	}
}